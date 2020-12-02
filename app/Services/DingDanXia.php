<?php


namespace App\Services;


use App\components\GlobalConstant;
use App\Errors;
use App\Models\Goods;
use App\Models\GoodsCat;
use App\Models\MsGoods;
use App\Models\NineGoods;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

class DingDanXia
{
    use Errors;

    private $httpClient;

    const POOL_concurrency = "concurrency";
    const POOL_fulfilled = "fulfilled";
    const POOL_rejected = "rejected";

    public static function getAliKey()
    {
        return env("DDX_ALIKEY");
    }

    public static function getJdUnionId()
    {
        return env("JD_UNIONID");
    }

    //获取淘宝客会员运营专属推广id
    public static function getTbkMemberPid()
    {
        return env("TAOBAOKE_MEMBER_PID");
    }

    //获取淘宝客渠道专属推广id
    public static function getTbkChannelPid()
    {
        return env("TAOBAOKE_MEMBER_PID");
    }

    public function __construct()
    {
        $this->httpClient = new Client();
    }


    //这里的目的是方便后续添加签名
    public static function createParams(array $params, $isString = false)
    {
        $params["apikey"] = self::getAliKey();
        return $isString ? http_build_query($params) : $params;
    }


    public function getJDGoodsByGid(array $gid)
    {
        $maxSection   = 100;
        $gidCount     = count($gid);
        $url          = "http://api.tbk.dingdanxia.com/jd/query_goods";
        $sectionTotal = ceil($gidCount / $maxSection);
        //按照分段并发请求
        $requestsFunc = function ($gid) use ($sectionTotal, $maxSection, $gidCount, $url) {
            for ($section = 1; $section <= $sectionTotal; $section++) {
                $offset = ($section - 1) * $maxSection;
                yield new \GuzzleHttp\Psr7\Request("GET", $url . "?" . self::createParams([
                        "skuIds"   => implode(",", array_slice($gid, $offset, $maxSection)),
                        "isCoupon" => 1,
                    ], true));
            }
        };

        $ret     = [];
        $pool    = new Pool($this->httpClient, $requestsFunc($gid), [
            self::POOL_concurrency => $sectionTotal,
            self::POOL_fulfilled   => function (ResponseInterface $resp, $index) use (&$ret) {
                $body    = $resp->getBody();
                $content = $body->getContents();
                $body->close();
                $content = \GuzzleHttp\json_decode($content, true);
                if (@$content["code"] != 200) {
                    $this->setError("goods_info_" . $index, @$content["msg"]);
                    return;
                }
                foreach ($content["data"] as $data) {
                    $goods = Goods::createRowGoodsByJD($data);
                    if ($goods) {
                        $ret[] = $goods;
                    }
                }
            },
            self::POOL_rejected    => function ($reason, $index) {
                $this->setError("goods_info_" . $index, $reason);
            }
        ]);
        $promise = $pool->promise();
        $promise->wait();
        return $ret;
    }

    public function getGoodsByGid(array $gid, $source, $is_special = false)
    {
        //京东的单独处理
        if ($source == Goods::SOURCE_JD) {
            return $this->getJDGoodsByGid($gid);
        }
        $requestsFunc = function ($gid) use ($source, $is_special) {
            $url   = "";
            $idKey = "";
            switch ($source) {
                case Goods::SOURCE_TB:
                    $idKey  = "q";
                    $params = [
                        $idKey => 0,
                    ];
                    if ($is_special) {
                        $params['has_coupon'] = 'false';//如果特殊商品踩不到会这样处理
                    }
                    $url = "http://api.tbk.dingdanxia.com/tbk/super_search";
                    break;
                case Goods::SOURCE_PDD:
                    $idKey  = "keyword";
                    $url    = "http://api.tbk.dingdanxia.com/pdd/goods_search";
                    $params = [
                        $idKey        => 0,
                        "with_coupon" => "true"
                    ];
                    break;
            }

            foreach ($gid as $id) {
                $params[$idKey] = $id;
                yield new \GuzzleHttp\Psr7\Request("GET", $url . "?" . self::createParams($params, true));
            }
        };

        $concurrency = count($gid);
        if ($concurrency > 20) {
            $concurrency = 20;
        }
        $ret     = [];
        $pool    = new Pool($this->httpClient, $requestsFunc($gid), [
            self::POOL_concurrency => $concurrency,
            self::POOL_fulfilled   => function (ResponseInterface $resp, $index) use (&$ret, $source, $is_special) {
                $body    = $resp->getBody();
                $content = $body->getContents();
                $body->close();
                $content = \GuzzleHttp\json_decode($content, true);
                if (@$content["code"] != 200) {
                    $this->setError("goods_info_" . $index, @$content["msg"] . $content['data']['msg']);
                    return;
                }

                $content = reset($content["data"]);

                if (!$content) {
                    return;
                }
                $goods = "";
                switch ($source) {
                    case Goods::SOURCE_TB:
                        if ($is_special) {
                            $goods = Goods::createRowSpecialGoodsByTb($content);
                        } else {
                            $goods = Goods::createRowGoodsByTb($content);
                        }
                        break;
                    case Goods::SOURCE_PDD:
                        $goods = Goods::createRowGoodsByPdd($content);
                        break;
                }
                $goods && $ret[] = $goods;
            },
            self::POOL_rejected    => function ($reason, $index) {
                $this->setError("goods_info_" . $index, $reason);
            }
        ]);
        $promise = $pool->promise();
        $promise->wait();

        //淘宝的才需要
        if ($source == Goods::SOURCE_TB) {
            $ret = $this->tbkShopInfo($ret);
            if ($is_special) {
                $ret = $this->getCouponSearch($ret);
            }
            $ret = $this->gidPrivilege($ret, $source);//这里也只有淘宝的才需要转链了 其他的都要动态生成
        }
        return $ret;
    }

    //高拥转链 这里只能转链会员渠道的推广位
    //分享赚钱必须实时转链 单独写一个方法 使用渠道推广位
    //必须传递goods的数组
    public function gidPrivilege(array $ret, $source)
    {
        $requestsFunc = function ($ret) use ($source) {
            $url   = "";
            $idKey = "";
            switch ($source) {
                case Goods::SOURCE_TB:
                    $idKey  = "id";
                    $params = [
                        $idKey     => 0,
                        "itemInfo" => true,
                        "pid"      => self::getTbkMemberPid()
                    ];
                    $url    = "http://api.tbk.dingdanxia.com/tbk/id_privilege";
                    break;
                case Goods::SOURCE_PDD:
                    $idKey  = "goods_id_list";
                    $url    = "http://api.tbk.dingdanxia.com/pdd/convert";
                    $params = [
                        $idKey => 0,
                    ];
                    break;
                case Goods::SOURCE_JD:
                    $url    = "http://api.tbk.dingdanxia.com/jd/by_unionid_promotion";
                    $params = [
                        "materialId" => "",
                        "unionId"    => self::getJdUnionId(),
                        "couponUrl"  => "",
                        "chainType"  => 1
                    ];
                    break;
            }
            foreach ($ret as $goods) {
                if ($source == Goods::SOURCE_JD) {
                    $params["materialId"] = $goods->getExtendJson(Goods::EXTENDS_JSON_KEY_MATERIAL_ID);
                    $params["couponUrl"]  = $goods->getExtendJson(Goods::EXTENDS_JSON_KEY_COUPON_URL);
                } else {
                    if ($source == Goods::SOURCE_TB && $goods->activity_id) {
                        $params['activityId'] = $goods->activity_id;//加activity_id主要是为了获取coupon的时间
                    }
                    $params[$idKey] = $goods->gid;
                }
                yield new \GuzzleHttp\Psr7\Request("GET", $url . "?" . self::createParams($params, true));
            }
        };
        $concurrency  = count($ret);
        $pool         = new Pool($this->httpClient, $requestsFunc($ret), [
            self::POOL_concurrency => $concurrency,
            self::POOL_fulfilled   => function (ResponseInterface $resp, $index) use (&$ret, $source) {
                $body    = $resp->getBody();
                $content = $body->getContents();
                $body->close();
                $content = \GuzzleHttp\json_decode($content, true);
                if (@$content["code"] != 200) {
                    $this->setError("goods_info_" . $index, @$content["msg"]);
                    return;
                }
                $content = $content["data"];
                //提取当前的goods
                $goods = $ret[$index];
                switch ($source) {
                    case Goods::SOURCE_TB:
                        $goods->pop_url             = $content["coupon_click_url"];
                        $goods->qh_final_price      = $content["itemInfo"]["qh_final_price"];
                        $goods->qh_final_commission = floor2($content["itemInfo"]["qh_final_commission"]);
                        if ($goods->activity_id) {
                            $goods->coupon_start_time = @$content["coupon_start_time"];
                            $goods->coupon_end_time   = @$content["coupon_end_time"];
                            $goods->coupon_amount     = @$content["coupon"];
                            unset($goods->activity_id);
                            self::gidPrivilege($ret, $source);//多件拍的券要再次抓取一次 不然价格有可能为负数
                        }
                        break;
                    case Goods::SOURCE_PDD:
                        $goods->pop_url = $content["url"];
                        break;
                    case Goods::SOURCE_JD:
                        $goods->pop_url = $content["clickURL"];
                }
                $ret[$index] = $goods;
            },
            self::POOL_rejected    => function ($reason, $index) {
                $this->setError("goods_privilege_" . $index, $reason);
            }
        ]);
        $promise      = $pool->promise();
        $promise->wait();
        return $ret;
    }

    //淘宝客店铺信息获取
    //必须传递goods的数组
    public function tbkShopInfo(array $ret)
    {
        $requestsFunc = function ($ret) {
            foreach ($ret as $goods) {
                yield new \GuzzleHttp\Psr7\Request("GET", "http://api.tbk.dingdanxia.com/shop/wdetail?" . self::createParams([
                        "id" => $goods->gid
                    ], true));
            }
        };
        $concurrency  = count($ret);
        $pool         = new Pool($this->httpClient, $requestsFunc($ret), [
            self::POOL_concurrency => $concurrency,
            self::POOL_fulfilled   => function (ResponseInterface $resp, $index) use (&$ret) {
                $body    = $resp->getBody();
                $content = $body->getContents();
                $body->close();
                $content = \GuzzleHttp\json_decode($content, true);
                if (@$content["code"] != 200) {
                    $this->setError("goods_wdetail_" . $index, @$content["msg"]);
                    return;
                }


                $content = $content["data"];
                //提取当前的goods
                $goods = $ret[$index];

                $goods->shop_icon = (string)$content["seller"]["shopIcon"];
                $goods->shop_url  = (string)$content["seller"]["taoShopUrl"];
                $goods->video  = $content["video"]?? null;
                if (stripos($goods->shop_icon, "http") === false && $goods->shop_icon) {
                    $goods->shop_icon = "http://" . trim($goods->shop_icon, "//");
                }
                if (stripos($goods->shop_url, "http") === false && $goods->shop_url) {
                    $goods->shop_url = "http://" . trim($goods->shop_url, "//");
                }
                $goods->setExtendJson(Goods::EXTENDS_JSON_KEY_SHOP_EVALUATES, $content["seller"]["evaluates"]);
                $ret[$index] = $goods;
            },
            self::POOL_rejected    => function ($reason, $index) {
                $this->setError("goods_wdetail_" . $index, $reason);
            }
        ]);
        $promise      = $pool->promise();
        $promise->wait();
        return $ret;
    }


    public function getNineGoods($every_num, $cat_id)
    {
        $key          = 'nine_min_id' . $cat_id;
        $min_id       = Cache::get($key) ?? 1;
        $requestsFunc = function ($cat, $min_id, $every_num) {
            $url                 = "http://api.tbk.dingdanxia.com/spk/jiukuaijiu";
            $params['cid']       = $cat;
            $params['min_id']    = $min_id;
            $params['page_size'] = $every_num;
            $params['sort']      = 0;//综合（最新）;
            yield new \GuzzleHttp\Psr7\Request("GET", $url . "?" . self::createParams($params, true));
        };

        $concurrency = 20;
        $ret         = [];
        $pool        = new Pool($this->httpClient, $requestsFunc($cat_id, $min_id, $every_num), [
            self::POOL_concurrency => $concurrency,
            self::POOL_fulfilled   => function (ResponseInterface $resp, $index) use (&$ret, $cat_id) {
                $body    = $resp->getBody();
                $content = $body->getContents();
                $body->close();
                $content = \GuzzleHttp\json_decode($content, true);
                if (@$content["code"] != 200) {
                    Cache::set('nine_min_id' . $cat_id, 1);
                    $this->setError("goods_info_" . $index, @$content["msg"]);
                    return;
                }
                Cache::set('nine_min_id' . $cat_id, $content['min_id'] ?? 1);
                $content = reset($content["data"]);
                $ret     = Goods::createRowNineObjectByTb($content, $cat_id);
            },
            self::POOL_rejected    => function ($reason, $index) {
                $this->setError("goods_info_" . $index, $reason);
            }
        ]);
        $promise     = $pool->promise();
        $promise->wait();
//        $ret = $this->gidPrivilege($ret,NineGoods::SOURCE_TB);
        return $ret;
    }


    public function getTbTestGIds()
    {
        $gids = [];
        for ($page = 1; $page <= 20; $page++) {
            $resp    = $this->httpClient->get("http://api.tbk.dingdanxia.com/tbk/super_search", [
                RequestOptions::QUERY => self::createParams([
                    "page_no"    => $page,
                    "page_size"  => 30,
                    "has_coupon" => "true",
                    "q"          => "女装"
                ])
            ]);
            $content = $resp->getBody()->getContents();
            $content = \GuzzleHttp\json_decode($content, true);
            $content = $content["data"];
            foreach ($content as $item) {
                if (@$item["item_id"] && is_numeric(@$item["item_id"])) {
                    $gids[] = @$item["item_id"];
                }
            }
        }
        $gids = array_unique($gids);
        print_r(implode(",", array_slice($gids, 0, 300)));
        echo "\r\n";
        die(print_r($gids));
    }

    public function getTaoBaoKeCat()
    {
        $requestsFunc = function () {
            $url            = "http://api.tbk.dingdanxia.com/spk/cate";
            $params['tree'] = true;
            yield new \GuzzleHttp\Psr7\Request("GET", $url . "?" . self::createParams($params, true));
        };

        $concurrency = 2;
        $ret         = [];
        $pool        = new Pool($this->httpClient, $requestsFunc(), [
            self::POOL_concurrency => $concurrency,
            self::POOL_fulfilled   => function (ResponseInterface $resp) use (&$ret) {
                $body    = $resp->getBody();
                $content = $body->getContents();
                $body->close();
                $content = \GuzzleHttp\json_decode($content, true);
                if (@$content["code"] != 200) {
                    echo '抓取淘宝客分类数据失败';
                    return;
                }

                $ret = GoodsCat::createRowTaoBaoKeCat($content['data']);

            },
            self::POOL_rejected    => function ($reason, $index) {
                $this->setError("goods_info_" . $index, $reason);
                echo '抓取淘宝客分类数据失败' . $reason;
            }
        ]);
        $promise     = $pool->promise();
        $promise->wait();
        return $ret;
    }

    public function getTbDetailImages($gid)
    {
        $resp    = $this->httpClient->get("http://api.tbk.dingdanxia.com/shop/good_images", [
            RequestOptions::QUERY => self::createParams([
                "id" => $gid
            ])
        ]);
        $content = $resp->getBody()->getContents();
        $content = \GuzzleHttp\json_decode($content, true);
        $content = $content["data"];
        return $content;
    }

    public function getPddDetailImages($gid)
    {
        $resp    = $this->httpClient->get("http://api.tbk.dingdanxia.com/pdd/goods_detail", [
            RequestOptions::QUERY => self::createParams([
                "goods_id_list" => $gid
            ])
        ]);
        $content = $resp->getBody()->getContents();
        $content = \GuzzleHttp\json_decode($content, true);
        $content = reset($content["data"]);
        $content = $content['goods_gallery_urls'];
        return $content;
    }

    public function getJdDetailImages($gid)
    {
        $resp    = $this->httpClient->get("http://api.tbk.dingdanxia.com/jd/good_images", [
            RequestOptions::QUERY => self::createParams([
                "id" => $gid
            ])
        ]);
        $content = $resp->getBody()->getContents();
        $content = \GuzzleHttp\json_decode($content, true);
        if (@$content["code"] != 200) {
            return [];
        }
        $content = $content["data"];
        $content = \GuzzleHttp\json_encode($content);
        if (strpos($content, 'https:http') !== false) {
            $content = str_replace('https:http', 'https', $content);
            $content = \GuzzleHttp\json_decode($content);
        } else {
            $content = \GuzzleHttp\json_decode($content);
        }
        return $content;
    }

    //秒杀商品
    public function getSecKill($point)
    {
        $url = "http://api.tbk.dingdanxia.com/spk/qiang";
        $rel = [];
        try {
            $min_id = 1;
            for ($i = 0; $i < 10; $i++) {
                $params  = [
                    "hour_type" => $point,
                    "min_id"    => $min_id
                ];
                $resp    = $this->httpClient->get($url, [
                    RequestOptions::QUERY => self::createParams($params)
                ]);
                $body    = $resp->getBody();
                $content = $body->getContents();
                $body->close();
                $content = \GuzzleHttp\json_decode($content, true);
                if (@$content["code"] != 200) {
                    $this->setError("err_ms", "接口返回:" . @$content["msg"]);
                    break;
                }
                $min_id  = $content['min_id'];
                $content = reset($content["data"]);
                $content = MsGoods::setRowMs($content, $point);
                $content = $this->getTbDetailArrayGid($content);
                $content = $this->getMsGoodsByGid($content);
                $rel[]   = $content;
            }

        } catch (RequestException $e) {
            $this->setError("err_ms", "接口返回 : " . $e->getMessage());
            return false;
        }
        return $rel;
    }

    public function getTbDetailArrayGid($ret)
    {

        $requestsFunc = function ($ret) {
            foreach ($ret as $item) {
                $url          = "http://api.tbk.dingdanxia.com/shop/good_images";
                $params['id'] = $item->gid;
                yield new \GuzzleHttp\Psr7\Request("GET", $url . "?" . self::createParams($params, true));
            }
        };

        $concurrency = 20;
        $pool        = new Pool($this->httpClient, $requestsFunc($ret), [
            self::POOL_concurrency => $concurrency,
            self::POOL_fulfilled   => function (ResponseInterface $resp, $index) use (&$ret) {
                $body    = $resp->getBody();
                $content = $body->getContents();
                $body->close();
                $content = \GuzzleHttp\json_decode($content, true);
                if (@$content["code"] != 200) {
                    $this->setError('detail_images_error', '抓取淘宝客详情图片失败' . $content['msg']);
                    return false;
                }
                $goods_ms                = $ret[$index];
                $goods_ms->detail_images = $content['data'];
                $ret[$index]             = $goods_ms;

            },
            self::POOL_rejected    => function ($reason, $index) {
                $this->setError("detail_images_error" . $index, $reason);
                return false;
            }
        ]);
        $promise     = $pool->promise();
        $promise->wait();
        return $ret;
    }

    public function getMsGoodsByGid(array $ret)
    {

        $requestsFunc = function ($ret) {


            $idKey  = "q";
            $params = [
                $idKey       => 0,
                "has_coupon" => "false",
            ];
            $url    = "http://api.tbk.dingdanxia.com/tbk/super_search";


            foreach ($ret as $item) {
                $params[$idKey] = $item->gid;
                yield new \GuzzleHttp\Psr7\Request("GET", $url . "?" . self::createParams($params, true));
            }
        };

        $concurrency = 20;
        $pool        = new Pool($this->httpClient, $requestsFunc($ret), [
            self::POOL_concurrency => $concurrency,
            self::POOL_fulfilled   => function (ResponseInterface $resp, $index) use (&$ret) {
                $body    = $resp->getBody();
                $content = $body->getContents();
                $body->close();
                $content = \GuzzleHttp\json_decode($content, true);
                if (@$content["code"] != 200) {
                    $this->setError("goods_info_" . $index, @$content["msg"] . $content['data']['msg']);
                    return;
                }
                $content = reset($content["data"]);
                if (!$content) {
                    return;
                }
                $goods_ms                   = $ret[$index];
                $goods_ms->zk_final_price   = $content['zk_final_price'];
                $goods_ms->images_url       = @$content['small_images']['string'] ? $content['small_images']['string'] : '';
                $goods_ms->item_description = @$content['item_description'];
                $ret[$index]                = $goods_ms;
            },
            self::POOL_rejected    => function ($reason, $index) {
                $this->setError("goods_info_" . $index, $reason);
            }
        ]);
        $promise     = $pool->promise();
        $promise->wait();

        //淘宝的才需要

        $ret = $this->tbkShopInfo($ret);
        $ret = $this->gidPrivilege($ret, 1);//这里也只有淘宝的才需要转链了 其他的都要动态生成

        return $ret;
    }

    public function getCouponSearch(array $ret)
    {

        $requestsFunc = function ($ret) {
            $url = "http://api.tbk.dingdanxia.com/tbk/coupon_search";
            foreach ($ret as $item) {
                $params['id'] = $item->gid;
                yield new \GuzzleHttp\Psr7\Request("GET", $url . "?" . self::createParams($params, true));
            }
        };

        $concurrency = 20;
        $pool        = new Pool($this->httpClient, $requestsFunc($ret), [
            self::POOL_concurrency => $concurrency,
            self::POOL_fulfilled   => function (ResponseInterface $resp, $index) use (&$ret) {
                $body    = $resp->getBody();
                $content = $body->getContents();
                $body->close();
                $content = \GuzzleHttp\json_decode($content, true);
                if (@$content["code"] != 200) {
                    $this->setError("goods_info_" . $index, @$content["msg"] . $content['data']['msg']);
                    return;
                }
                $content = $content['data'];
                if (!$content) {
                    return;
                }
                $goods              = $ret[$index];
                $goods->activity_id = $content['activity_id'];
                $ret[$index]        = $goods;
            },
            self::POOL_rejected    => function ($reason, $index) {
                $this->setError("goods_info_" . $index, $reason);
            }
        ]);
        $promise     = $pool->promise();
        $promise->wait();
        return $ret;
    }

    public function consoleCatGoods($source, $keyword)
    {
        $requestsFunc = function () use ($source, $keyword) {
            switch ($source) {
                case Goods::SOURCE_TB:
                    $params = [
                        "q"         => $keyword,
                        "page_size" => 50,
                        "page_no"   => 1
                    ];
                    $url    = "http://api.tbk.dingdanxia.com/tbk/super_search";
                    break;
                case Goods::SOURCE_PDD:
                    $url    = "http://api.tbk.dingdanxia.com/pdd/goods_search";
                    $params = [
                        "keyword"   => $keyword,
                        "page_size" => 50,
                        "page"      => 1
                    ];
                    break;
                default:
                    $url    = "http://api.tbk.dingdanxia.com/jd/query_goods";
                    $params = [
                        "keyword"   => $keyword,
                        "pageSize"  => 50,
                        "pageIndex" => 1
                    ];
                    break;
            }
            yield new \GuzzleHttp\Psr7\Request("GET", $url . "?" . self::createParams($params, true));

        };
        $concurrency  = 20;
        $ret          = [];
        $pool         = new Pool($this->httpClient, $requestsFunc(), [
            self::POOL_concurrency => $concurrency,
            self::POOL_fulfilled   => function (ResponseInterface $resp) use (&$ret, $source) {
                $body    = $resp->getBody();
                $content = $body->getContents();
                $body->close();
                $content = \GuzzleHttp\json_decode($content, true);
                if (@$content["code"] != 200) {
                    $this->setError("搜索商品失败", @$content["msg"]);
                    return;
                }
                $content = $content["data"];
                if (!$content) {
                    return;
                }
                switch ($source) {
                    case Goods::SOURCE_TB:
                        $ret = Goods::formatGoodColumnTb($content, $source);
                        break;
                    case Goods::SOURCE_PDD:
                        $ret = Goods::formatGoodColumnPdd($content, $source);
                        break;
                    default:
                        $ret = Goods::formatGoodColumnJd($content, $source);
                        break;
                }
            },
            self::POOL_rejected    => function ($reason, $index) {
                $this->setError("goods_info_" . $index, $reason);
            }
        ]);
        $promise      = $pool->promise();
        $promise->wait();
        if($source==Goods::SOURCE_TB){
            $ret = $this->tbkShopInfo($ret);
            $ret = $this->gidPrivilege($ret, $source);//这里也只有淘宝的才需要转链了 其他的都要动态生成
        }

        return $ret;
    }

    public function updateGidInfo(array $ret, $source)
    {
        $requestsFunc = function ($ret) use ($source) {
            $url   = "";
            $idKey = "";
            switch ($source) {
                case Goods::SOURCE_TB:
                    $idKey  = "id";
                    $params = [
                        $idKey     => 0,
                        "itemInfo" => true,
                        "pid"      => self::getTbkMemberPid()
                    ];
                    $url    = "http://api.tbk.dingdanxia.com/tbk/id_privilege";
                    break;
                case Goods::SOURCE_PDD:
                    $idKey  = "goods_id_list";
                    $url    = "http://api.tbk.dingdanxia.com/pdd/goods_search";
                    $params = [
                        $idKey => 0,
                    ];
                    break;
                case Goods::SOURCE_JD:
                    $idKey  = "skuIds";
                    $url    = "http://api.tbk.dingdanxia.com/jd/query_goods";
                    $params = [
                        $idKey => 0,
                    ];
                    break;

            }
            foreach ($ret as $goods) {
                $params[$idKey] = $goods->gid;
                yield new \GuzzleHttp\Psr7\Request("GET", $url . "?" . self::createParams($params, true));
            }
        };
        $concurrency  = count($ret);
        $pool         = new Pool($this->httpClient, $requestsFunc($ret), [
            self::POOL_concurrency => $concurrency,
            self::POOL_fulfilled   => function (ResponseInterface $resp, $index) use (&$ret, $source) {
                $body    = $resp->getBody();
                $content = $body->getContents();
                $body->close();
                $content = \GuzzleHttp\json_decode($content, true);
                //提取当前的goods
                $goods             = $ret[$index];
                if (@$content["code"] != 200) {
                    if (@$content['code']== -1){
                        Goods::where('gid',$goods->gid)->delete();
                    }
                    $this->setError("goods_info_" . $index, @$content["msg"]);
                    return;
                }
                $content = $content["data"];
                $goods->timestamps = false;
                switch ($source) {
                    case Goods::SOURCE_TB:
                        $goods->pop_url             = $content["coupon_click_url"];
                        $goods->qh_final_price      = bcadd($content["itemInfo"]["qh_final_price"], 0, 2);
                        $goods->qh_final_commission = bcadd($content["itemInfo"]["qh_final_commission"], 0, 2);
                        $goods->coupon_start_time   = @$content["coupon_start_time"] ? $content["coupon_start_time"] . ' 00:00:00' : null;
                        $goods->coupon_end_time     = @$content["coupon_end_time"] ? $content['coupon_end_time'] . ' 00:00:00' : null;
                        $goods->coupon_amount       = $content["coupon"] ? bcadd($content['coupon'], 0, 2) : 0;
                        if ($goods->coupon_amount == 0) {
                            $goods->coupon_amount = "0.00";
                        }
                        break;
                    case Goods::SOURCE_PDD:
                        $content                    = reset($content);
                        if(empty($content)){
                            Goods::where('gid',$goods->gid)->delete();
                            break;
                        }
                        $goods->qh_final_price      = bcsub($content["min_group_price"] / 100, $content["coupon_discount"] / 100, 2);
                        $goods->qh_final_commission = bcmul($goods->qh_final_price, ($content["promotion_rate"] / 1000), 2);
                        $goods->coupon_start_time   = @$content["coupon_start_time"] ? date("Y-m-d H:i:s", $content["coupon_start_time"]) : null;
                        $goods->coupon_end_time     = @$content["coupon_end_time"] ? date("Y-m-d H:i:s", $content["coupon_end_time"]) : null;
                        $goods->coupon_amount       = $content["coupon_discount"] ? bcadd($content["coupon_discount"] / 100, 0, 2) : 0;
                        if ($goods->coupon_amount == 0) {
                            $goods->coupon_amount = "0.00";
                        }
                        break;
                    case Goods::SOURCE_JD:
                        $content    = reset($content);
                        $couponInfo = [];
                        foreach ($content["couponInfo"]["couponList"] as $coupon) {
                            if ($coupon["isBest"] == 1) {
                                $couponInfo = $coupon;
                            }
                        }
                        if (!$couponInfo) {
                            $couponInfo = reset($content["couponInfo"]["couponList"]);
                        }
                        if(!$couponInfo){
                            $goods->extends_json = null;//更新优惠券的时候更新此字段 不然会影响前端下单提示优惠券过期或下架
                        }
                        $goods->commission_rate     = $content["commissionInfo"]["commissionShare"];
                        $coupon_amount              = $couponInfo["discount"] ? floatval($couponInfo["discount"]) : 0;
                        $goods->qh_final_price      = bcsub($content["priceInfo"]["lowestPrice"], $coupon_amount, 2);
                        $goods->qh_final_commission = bcmul($goods->qh_final_price, ($goods->commission_rate / 100), 2);
                        $goods->coupon_start_time   = @$couponInfo["useStartTime"] ? date("Y-m-d H:i:s", $couponInfo["useStartTime"] / 1000) : null;
                        $goods->coupon_end_time     = @$couponInfo["useEndTime"] ? date("Y-m-d H:i:s", $couponInfo["useEndTime"] / 1000) : null;
                        $goods->coupon_amount       = $coupon_amount;
                        if ($goods->coupon_amount == 0) {
                            $goods->coupon_amount = "0.00";
                        }
                        break;
                }
                $ret[$index] = $goods;
            },
            self::POOL_rejected    => function ($reason, $index) {
                $this->setError("goods_privilege_" . $index, $reason);
            }
        ]);
        $promise      = $pool->promise();
        $promise->wait();
        return $ret;
    }


}

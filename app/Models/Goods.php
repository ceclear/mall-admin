<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * App\Models\Goods
 *
 * @property int $id
 * @property int $source 来源 1淘宝 2京东 3...
 * @property int $gid 对应每个来源的商品id
 * @property int $cat_id_one 一级分类
 * @property int $cat_id_two 二级分类
 * @property string|null $pict_url 商品主图
 * @property string|null $images_url 商品图列表
 * @property string $title 商品标题
 * @property float $reserve_price 商品原价
 * @property float $zk_final_price 折扣价
 * @property float $qh_final_price 卷后价
 * @property float $qh_final_commission 卷后佣金
 * @property float $commission_rate 佣金比率
 * @property string|null $coupon_start_time 优惠券起始时间
 * @property string|null $coupon_end_time 优惠券结束时间
 * @property float $coupon_amount 优惠券面额
 * @property string|null $item_description 宝贝描述（推荐理由）
 * @property int $volume 近三十天销量
 * @property string|null $shop_url 店铺url
 * @property string|null $shop_icon 店铺图片
 * @property string $shop_name 店铺名称
 * @property int $shop_type 对于source为淘宝的是0集市 1天猫超市
 * @property int $sort 排序
 * @property int $partition 商品分区 1猜你喜欢 2火爆推荐 以实际代码常量为准
 * @property string|null $goods_desc 商品描述
 * @property string|null $extends_json 扩展字段
 *                 //店铺评分数据
 *                  shop_evaluates:{
 *                     "title": "宝贝描述",
 *                     "score": "4.8 ",
 *                 },
 *                 {
 *                     "title": "卖家服务",
 *                     "score": "4.7 ",
 *                 },
 *                 {
 *                     "title": "物流服务",
 *                     "score": "4.7 ",
 *                 }
 * @property string|null $pop_url 转链后的推广链接
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property array|null $detail_images 详情图片淘宝专用
 * @property int|null $top 顶置
 * @property int|null $is_auto 手动还是自动的数据1手动2自动
 * @property string|null $top_time 置顶时间
 * @property int|null $sort_time 排序时间，默认创建时间
 * @property string|null $show_title 修改后的名称，主要用于列表展示
 * @property string|null $show_pic 修改后的主图，主要用于列表展示
 * @property string|null $video 视频地址
 * @property-read \App\Models\GoodsCat|null $catOne
 * @property-read \App\Models\GoodsCat|null $catTwo
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereCatIdOne($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereCatIdTwo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereCouponAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereCouponEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereCouponStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereDetailImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereExtendsJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereGid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereGoodsDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereImagesUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereIsAuto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereItemDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods wherePartition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods wherePictUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods wherePopUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereQhFinalCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereQhFinalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereReservePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereShopIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereShopName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereShopType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereShopUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereShowPic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereShowTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereSortTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereTop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereTopTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereVideo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereZkFinalPrice($value)
 * @mixin \Eloquent
 * @property int|null $point_time 秒杀时间
 * @property int|null $point_id 秒杀点
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods wherePointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods wherePointTime($value)
 */
class Goods extends Model
{
    protected $connection = 'lx-mall';
    protected $table = 'goods';
    use commonTrait;
    protected $casts = [
        'detail_images' => 'array',
    ];
    const EXTENDS_JSON_KEY_SHOP_EVALUATES = "shop_evaluates";
    const EXTENDS_JSON_KEY_MATERIAL_ID = "materialId";
    const EXTENDS_JSON_KEY_COUPON_URL = "couponUrl";

    const SOURCE_TB = 1;
    const SOURCE_JD = 2;
    const SOURCE_PDD = 3;
    const SOURCE_WPH = 4;

    public static function getCollectionSourceMap($key = "ALL")
    {
        $ret = [
            self::SOURCE_TB => "淘宝",
            self::SOURCE_JD => "京东",
            self::SOURCE_PDD => "拼多多",
        ];

        return $ret;
    }

    public static function getSourceMap($key = "ALL")
    {
        $ret = [
            self::SOURCE_TB => "淘宝",
            self::SOURCE_JD => "京东",
            self::SOURCE_PDD => "拼多多",
            self::SOURCE_WPH => "唯品会"
        ];
        if ( $key === "ALL" ) {
            return $ret;
        }
        return Arr::get($ret, $key, false);
    }

//    public function getIsAutoAttribute($value)
//    {
//        return $value==1?'手动':'自动';
//    }

    /**
     * @param $list
     * @param $cat_id
     * @return array
     */
    public static function createRowNineObjectByTb($list, $cat_id)
    {
        $rel  = [];
        $sort = self::max('sort');
        if (!$sort)
            $sort = 1;
        foreach ($list as $item) {
            $self = self::where("gid", $item["itemid"])->first();
            if (!$self) {
                $self = new self();
            }
            $images_url = $item['taobao_image'] ? explode(',', $item['taobao_image']) : '';
            $sort++;
            $self->cat_id_one = 0;
            $self->cat_id_two = 0;
            $self->source              = self::SOURCE_TB;
            $self->partition = \App\GlobalConstant::SYSTEM_PARTITION_TIME_9_9;
            $self->gid                 = $item["itemid"];
            $self->cat_id_one          = $cat_id;
            $self->pict_url            = $item["itempic"];
            $self->images_url          = $images_url ? json_encode($images_url) : '';
            $self->title               = $item["itemtitle"];
            $self->reserve_price       = $item["itemprice"];
            $self->zk_final_price      = $item["itemprice"];
            $self->qh_final_price      = $item["itemendprice"];
            $self->qh_final_commission = $item["tkmoney"];
            $self->commission_rate     = $item["tkrates"] / 100;
            $self->coupon_start_time   = date('Y-m-d H:i:s', $item["couponstarttime"]);
            $self->coupon_end_time     = date('Y-m-d H:i:s', $item["couponendtime"]);
            $self->coupon_amount       = $item["couponmoney"];
            $self->item_description    = $item["itemdesc"];
            $self->volume              = $item["itemsale"];
            $self->shop_name           = $item["shopname"];
            $self->shop_type           = $item["shoptype"]=='B'?1:0;
            $self->sort                = $sort;
            $self->created_at          = Carbon::now()->toDateTimeString();
            $self->updated_at          = Carbon::now()->toDateTimeString();
            $rel[]                     = $self;
        }
        return $rel;
    }

    /**
     * 这里只返回对应的模型数据
     * 不保存
     * @param $data
     * @return Goods
     */
    public static function createRowGoodsByTb($data)
    {
        $self = self::where("source", self::SOURCE_TB)->where("gid", $data["item_id"])->first();
        if ( !$self ) {
            $self = new self();
        }
        $self->source = self::SOURCE_TB;
        $self->gid = $data["item_id"];
        $self->pict_url = $data["pict_url"];
        $self->show_pic = $data["pict_url"];
        if ( @$data["small_images"] ) {
            $self->images_url = \GuzzleHttp\json_encode($data["small_images"]["string"]);
        }
        $self->title = $data["title"];
        $self->show_title = $data["title"];
        $self->reserve_price = $data["reserve_price"];
        $self->zk_final_price = $data["zk_final_price"];
        $self->commission_rate = $data["commission_rate"] / 100;
        $self->coupon_start_time = @$data["coupon_start_time"]??null;
        $self->coupon_end_time = @$data["coupon_end_time"]??null;
        $self->coupon_amount = @$data["coupon_amount"];
        if ( !$self->coupon_amount ) {
            $self->coupon_amount=0;
        }
        $self->item_description = $data["item_description"];
        $self->volume = $data["volume"];
        $self->shop_name = $data["shop_title"];
        $self->shop_type = $data["user_type"];
        return $self;
    }

    public static function createRowSpecialGoodsByTb($data)
    {
        $self = self::where("source", self::SOURCE_TB)->where("gid", $data["item_id"])->first();
        if ( !$self ) {
            $self = new self();
        }
        $self->source = self::SOURCE_TB;
        $self->gid = $data["item_id"];
        $self->pict_url = $data["pict_url"];
        $self->show_pic = $data["pict_url"];
        if ( @$data["small_images"] ) {
            $self->images_url = \GuzzleHttp\json_encode($data["small_images"]["string"]);
        }
        $self->title = $data["title"];
        $self->show_title = $data["title"];
        $self->reserve_price = $data["reserve_price"];
        $self->zk_final_price = $data["zk_final_price"];
        $self->commission_rate = $data["commission_rate"] / 100;
        $self->item_description = $data["item_description"];
        $self->volume = $data["volume"];
        $self->shop_name = $data["shop_title"];
        $self->shop_type = $data["user_type"];
        $self->coupon_amount = $data["coupon"]??0;
        return $self;
    }

    public static function createRowGoodsByPdd($data)
    {
        $self = self::where("source", self::SOURCE_PDD)->where("gid", $data["goods_id"])->first();
        if ( !$self ) {
            $self = new self();
        }
        $self->source = self::SOURCE_PDD;
        $self->gid = $data["goods_id"];
        $self->pict_url = $data["goods_image_url"];
        $self->show_pic = $data["goods_image_url"];
//        $self->images_url = \GuzzleHttp\json_encode($data["goods_gallery_urls"]);
//        if ( $self->images_url == "null" ) {
//            $self->images_url = "";//2020-7-23订单侠说拼多多官方更改了此字段，所以换另外个接口获取图片
//        }
        $self->title = $data["goods_name"];
        $self->show_title = $data["goods_name"];
        $self->reserve_price = $data["min_group_price"] / 100;//单位分
        $self->zk_final_price = $self->reserve_price;
        $self->commission_rate = $data["promotion_rate"] / 10;//千分比转化为百分比
        $self->coupon_start_time = date("Y-m-d H:i:s", $data["coupon_start_time"]);
        $self->coupon_end_time = date("Y-m-d H:i:s", $data["coupon_end_time"]);
        $self->coupon_amount = $data["coupon_discount"] / 100;
        if ( !$self->coupon_amount ) {
            return ;
        }
       // die(var_dump($data["sales_tip"]));
        $self->item_description = $data["goods_desc"];
        if ( stripos($data["sales_tip"], "万") ) {
            $data["sales_tip"] = str_replace("万", "", $data["sales_tip"]);
            $data["sales_tip"] = floatval($data["sales_tip"]) * 10000;
        }
        $self->volume = $data["sales_tip"];
        $self->shop_name = $data["mall_name"];
        $self->shop_type = $data["merchant_type"];
        $self->shop_icon = "";
        $self->shop_url = "";
        $self->qh_final_price = bcsub($self->zk_final_price , $self->coupon_amount,2);
        $self->qh_final_commission = bcmul($self->qh_final_price , ($data["promotion_rate"] / 1000),2);
        $self->setExtendJson(self::EXTENDS_JSON_KEY_SHOP_EVALUATES, [
            [
                "title" => "宝贝描述",
                "score" => $data["desc_txt"],
                "levelText" => $data["desc_txt"]
            ],
            [
                "title" => "卖家服务",
                "score" => $data["serv_txt"],
                "levelText" => $data["serv_txt"]
            ],
            [
                "title" => "物流服务",
                "score" => $data["lgst_txt"],
                "levelText" => $data["lgst_txt"]
            ],
        ]);
        return $self;
    }

    public static function createRowGoodsByJD($data)
    {
        $self = self::where("source", self::SOURCE_JD)->where("gid", $data["skuId"])->first();
        if ( !$self ) {
            $self = new self();
        }
        $self->source = self::SOURCE_JD;
        $self->gid = $data["skuId"];
        $data["imageInfo"]["imageList"] = array_column($data["imageInfo"]["imageList"], "url");
        $self->pict_url = array_shift($data["imageInfo"]["imageList"]);//提取主图
        $self->show_pic = $self->pic_url;
        $self->images_url = \GuzzleHttp\json_encode($data["imageInfo"]["imageList"]);
        $self->title = $data["skuName"];
        $self->show_title = $data["skuName"];
        $self->reserve_price = $data["priceInfo"]["price"];
        $self->zk_final_price = $data["priceInfo"]["lowestPrice"];
        $self->commission_rate = $data["commissionInfo"]["commissionShare"];

        //优惠券信息 获取最优的优惠券
        $couponInfo = [];
        foreach ( $data["couponInfo"]["couponList"] as $coupon ) {
            if ( $coupon["isBest"] == 1 ) {
                $couponInfo = $coupon;
            }
        }
        if ( !$couponInfo ) {
            $couponInfo = reset($data["couponInfo"]["couponList"]);
        }
        //如果没有优惠券跳过次阶段
//        if ( !$couponInfo ) {
//            return ;
//        }
        $self->coupon_start_time = $couponInfo?date("Y-m-d H:i:s", $couponInfo["useStartTime"] / 1000):null;
        $self->coupon_end_time = $couponInfo?date("Y-m-d H:i:s", $couponInfo["useEndTime"] / 1000):null;
        $self->coupon_amount = $couponInfo?floatval($couponInfo["discount"]):0;

        $self->qh_final_price = bcsub($self->zk_final_price ,  $self->coupon_amount,2);
        $self->qh_final_commission =  bcmul($self->qh_final_price , ($self->commission_rate / 100),2);#$data["commissionInfo"]["commission"];

        $self->item_description = "";
        $self->volume = $data["inOrderCount30Days"];
        $self->shop_name = $data["shopInfo"]["shopName"];
        $self->shop_type = -1;
        $self->shop_icon = "";
        $self->shop_url = "";
        $self->setExtendJson(self::EXTENDS_JSON_KEY_MATERIAL_ID, $data["materialUrl"]);
        $self->setExtendJson(self::EXTENDS_JSON_KEY_COUPON_URL, $couponInfo["link"]);
        return $self;
    }

    public function catOne()
    {
        return $this->hasOne(GoodsCat::class, "id", "cat_id_one");
    }
    public function catTwo()
    {
        return $this->hasOne(GoodsCat::class, "id", "cat_id_two");
    }

    public static function formatGoodColumnTb($list, $source)
    {
        $arr = [];
        foreach ($list as $item) {
            $self = self::where("source",$source)->where("gid", $item["item_id"])->first();
            if ( !$self ) {
                $self = new self();
            }
            $self->source         = $source;
            $self->gid           = $item['item_id'];
            $self->pict_url       = $item['pict_url'];
            $self->show_pic       = $item['pict_url'];
            $self->title         = $item['title'];
            $self->show_title         = $item['title'];
            $self->volume         = $item['volume'];
            $self->reserve_price  = $item['reserve_price'];
            if(@$item['coupon_start_time']){
                $self->coupon_start_time=$item['coupon_start_time'];
            }
            if(@$item['coupon_end_time']){
                $self->coupon_end_time=$item['coupon_end_time'];
            }
            $self->zk_final_price = $item['zk_final_price'];
            $self->coupon_amount      = $item['coupon']??0;
            $self->qh_final_price      = bcsub($item['zk_final_price'] , $self->coupon_amount, 2);
            $self->commission_rate     = $item['commission_rate']/100;
            $self->qh_final_commission = bcmul(bcsub($item['zk_final_price'] , $self->coupon_amount,2) , $item['commission_rate'] / 10000,2);
            $self->pop_url            = @$item['coupon_share_url']?"http://" . trim($item['coupon_share_url'], "//"):'';
            $self->shop_name          = $item['shop_title'];
            $self->shop_type          = $item['user_type'];
            $arr[]                       = $self;
        }
        return $arr;
    }

    public static function formatGoodColumnPdd($list, $source)
    {
        $arr = [];
        foreach ($list as $item) {
            $self = self::where("source",$source)->where("gid", $item["goods_id"])->first();
            if ( !$self ) {
                $self = new self();
            }
            $self->gid                = $item['goods_id'];
            $self->pict_url            = $item['goods_image_url'];
            $self->show_pic            = $item['goods_image_url'];
            $self->title               = $item['goods_name'];
            $self->show_title               = $item['goods_name'];
            $self->volume              = (int)$item['sales_tip'];
            $self->reserve_price       = $item['min_group_price']/100;
            if(@$item['coupon_start_time']){
                $self->coupon_start_time   = date("Y-m-d H:i:s", $item["coupon_start_time"]);
            }
            if(@$item['coupon_end_time']){
                $self->coupon_end_time     = date("Y-m-d H:i:s", $item["coupon_end_time"]);
            }
            $self->coupon_amount       = $item["coupon_discount"] / 100;
            $self->zk_final_price      = $self->reserve_price;
            $self->coupon_amount       = $item['coupon_discount'] / 100;
            $self->commission_rate     = $item['promotion_rate'] / 10;
            $self->qh_final_price      = bcsub($self->zk_final_price , $item['coupon_discount'] / 100, 2);
            $self->qh_final_commission = bcmul(bcsub($self->reserve_price, $self->coupon_amount, 2), $self->commission_rate / 100, 2);
            $self->pop_url             = "";
            $self->shop_name           = $item['mall_name'];
            $self->shop_type           = $item['merchant_type'];
            $arr[]                       = $self;
        }
        return $arr;
    }

    public static function formatGoodColumnJd($list, $source)
    {
        $arr = [];
        foreach ($list as $item) {
            $self = self::where("source",$source)->where("gid", $item["skuId"])->first();
            if ( !$self ) {
                $self = new self();
            }
            $self->gid            = $item['skuId'];
            $imageArr               = array_shift($item["imageInfo"]["imageList"]);
            $self->pict_url       = $imageArr['url'];
            $self->show_pic       = $imageArr['url'];
            $self->title          = $item['skuName'];
            $self->show_title          = $item['skuName'];
            $self->volume         = $item['inOrderCount30Days'];
            $self->reserve_price  = $item["priceInfo"]["price"];
            $self->zk_final_price = $item["priceInfo"]["lowestPrice"];
            $couponInfo = [];
            foreach ($item["couponInfo"]["couponList"] as $coupon) {
                if ($coupon["isBest"] == 1) {
                    $couponInfo = $coupon;
                }
            }
            if (!$couponInfo) {
                $couponInfo = reset($item["couponInfo"]["couponList"]);
            }

            $self->coupon_start_time = date("Y-m-d H:i:s", $couponInfo["useStartTime"] / 1000);
            $self->coupon_end_time   = date("Y-m-d H:i:s", $couponInfo["useEndTime"] / 1000);
            $self->coupon_amount     = floatval($couponInfo["discount"]);
            $self->qh_final_price  = bcsub($self->zk_final_price, $self->coupon_amount, 2);
            $self->commission_rate = $item['commissionInfo']['commissionShare'];
            //京东有两个佣金 一个是按原价算一个是卷后佣金
            if (isset($item['commissionInfo']['couponCommission'])) {
                $self->qh_final_commission = $item['commissionInfo']['couponCommission'];
            } else {
                $self->qh_final_commission = bcmul(bcsub($self->reserve_price, $self->coupon_amount, 2), $self->commission_rate / 100, 2);
            }

            $self->pop_url     = "";
            $self->shop_name  = $item['shopInfo']['shopName'];
            $self->shop_type  = -1;
            $arr[]               = $self;
        }
        return $arr;
    }
}

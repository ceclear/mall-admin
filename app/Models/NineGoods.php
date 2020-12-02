<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\NineGoods
 *
 * @property int $id
 * @property int $source 来源 1淘宝 2京东 3...
 * @property int $gid 对应每个来源的商品id
 * @property int|null $cat_id_one 一级分类
 * @property int|null $cat_id_two 二级分类
 * @property string $pict_url 商品主图
 * @property string $images_url 商品图列表
 * @property string $title 商品标题
 * @property float $reserve_price 商品原价
 * @property float $zk_final_price 折扣价
 * @property float $qh_final_price 券后价
 * @property float $qh_final_commission 到手佣金
 * @property float $commission_rate 佣金比例
 * @property string|null $coupon_start_time 优惠券起始时间
 * @property string|null $coupon_end_time 优惠券结束时间
 * @property float $coupon_amount 优惠券面额
 * @property string|null $item_description 宝贝描述（推荐理由）
 * @property int $volume 近三十天销量
 * @property string $shop_name 店铺名称
 * @property string|null $pop_url 转链后的推广链接
 * @property int $sort 排序
 * @property int $top 顶置值
 * @property int $status 状态
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $shop_type 店铺类型
 * @property array|null $detail_images 详情图片淘宝专用
 * @property int|null $is_auto 手动还是自动的数据1手动2自动
 * @property int|null $top_time 置顶时间，默认创建时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods whereCatIdOne($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods whereCatIdTwo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods whereCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods whereCouponAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods whereCouponEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods whereCouponStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods whereDetailImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods whereGid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods whereImagesUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods whereIsAuto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods whereItemDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods wherePictUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods wherePopUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods whereQhFinalCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods whereQhFinalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods whereReservePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods whereShopName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods whereShopType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods whereTop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods whereTopTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods whereVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NineGoods whereZkFinalPrice($value)
 * @mixin \Eloquent
 */
class NineGoods extends Model
{
    protected $connection = 'lx-mall';
    protected $table = 'goods_nine';
    protected $guarded = ['id'];
    use commonTrait;

    protected $casts = [
        'detail_images' => 'array',
    ];

    const EXTENDS_JSON_KEY_SHOP_EVALUATES = "shop_evaluates";

    const SOURCE_TB = 1;
    const SOURCE_JD = 2;
    const SOURCE_PDD = 3;
    const EVERY_NUM = 20;

    public static function getSourceMap($key = "ALL")
    {
        $ret = [
            self::SOURCE_TB => "淘宝",
            self::SOURCE_JD => "京东",
            self::SOURCE_PDD => "拼多多"
        ];
        if ($key === "ALL") {
            return $ret;
        }
        return Arr::get($ret, $key, false);
    }

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
            $self->source              = self::SOURCE_TB;
            $self->gid                 = $item["itemid"];
            $self->cat_id_one          = $cat_id;
            $self->pict_url            = $item["itempic"];
            $self->images_url          = $images_url ? json_encode($images_url) : '';
            $self->title               = $item["itemtitle"];
            $self->reserve_price       = $item["itemprice"];
            $self->zk_final_price      = $item["itemprice"] * $item['discount'];
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


    public static function catShow($id)
    {
        if ($data = Cache::get('tbk_cat')) {
            foreach ($data as $item) {
                if ($id == $item['cid'])
                    return $item['name'];
            }
        }
        return '--';
    }

    public static function NineCat(){
        $arr=[];
        if($data=Cache::get('tbk_cat')){
            foreach ($data as $item) {
                $arr[$item['cid']]=$item['name'];
            }
            return $arr;
        }
        return [];
    }

}

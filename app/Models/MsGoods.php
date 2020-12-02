<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\MsGoods
 *
 * @property int $source 来源
 * @property int $point 时间点
 * @property int $gid 对应每个来源的商品id
 * @property string|null $pict_url 商品主图
 * @property array|null $images_url 商品图列表
 * @property string $title 商品标题
 * @property float $reserve_price 商品原价
 * @property float|null $zk_final_price 折扣价
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
 * @property string|null $shop_name 店铺名称
 * @property int $shop_type 对于source为淘宝的是0集市 1天猫超市
 * @property int $sort 排序
 * @property array|null $detail_images 详情图片
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
 * @property int|null $top 顶置
 * @property int|null $is_auto 手动还是自动的数据1手动2自动
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods whereCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods whereCouponAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods whereCouponEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods whereCouponStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods whereDetailImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods whereExtendsJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods whereGid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods whereGoodsDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods whereImagesUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods whereIsAuto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods whereItemDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods wherePictUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods wherePoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods wherePopUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods whereQhFinalCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods whereQhFinalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods whereReservePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods whereShopIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods whereShopName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods whereShopType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods whereShopUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods whereTop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods whereVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MsGoods whereZkFinalPrice($value)
 * @mixin \Eloquent
 */
class MsGoods extends Model
{
    protected $connection = 'lx-mall';
    protected $table = 'goods_ms';
    use commonTrait;

    protected $casts = [
        'detail_images' => 'array',
        'images_url'    => 'array'
    ];

    const EXTENDS_JSON_KEY_SHOP_EVALUATES = "shop_evaluates";

    const SOURCE_TB = 1;
    const SOURCE_JD = 2;
    const SOURCE_PDD = 3;
    const EVERY_NUM = 20;

    const MS_HOUR_TYPE = [6, 7, 8, 9, 10, 11, 12];

    public static function getSourceMap($key = "ALL")
    {
        $ret = [
            self::SOURCE_TB  => "淘宝",
            self::SOURCE_JD  => "京东",
            self::SOURCE_PDD => "拼多多"
        ];
        if ($key === "ALL") {
            return $ret;
        }
        return Arr::get($ret, $key, false);
    }


    public static function setRowMs($list, $point)
    {
        $rel = [];
        foreach ($list as $item) {
            $self                      = new self();
            $self->source              = self::SOURCE_TB;
            $self->point               = $point;
            $self->reserve_price       = $item['itemprice'];
            $self->title               = $item['itemtitle'];
            $self->commission_rate     = $item['tkrates'];
            $self->coupon_start_time   = @date('Y-m-d H:i:s', $item['couponstarttime']);
            $self->coupon_end_time     = @date('Y-m-d H:i:s', $item['couponendtime']);
            $self->coupon_amount       = $item['couponmoney'];
            $self->pict_url            = $item['itempic'];
            $self->volume              = $item['itemsale'];
            $self->qh_final_price      = $item['itemendprice'];
            $self->qh_final_commission = $item['tkmoney'];
            $self->gid                 = $item['itemid'];
            $self->shop_name           = $item['sellernick'];
            $self->shop_type           = $item['shoptype'] == 'B' ? 1 : 0;
//            $data['discount']            = number_format(1 - ($item['itemprice'] - $item['itemendprice']) / $item['itemprice'], 2) * 10;
            $rel[] = $self;
        }
        return $rel;
    }


}

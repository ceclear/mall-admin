<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property int $uid 会员id
 * @property int $share_uid 分享者uid
 * @property string $order_sn 订单编号
 * @property int $source 订单来源 1淘宝 2京东 3...
 * @property string $order_parent_sn 主订单号
 * @property float $pre_fee 预估收益
 * @property float $total_commission_fee 佣金
 * @property float $total_commission_rate 佣金比率
 * @property int $pay_time 付款时间
 * @property string $pay_ym 付款年月
 * @property string $pay_date 付款时间 年月日
 * @property float $pay_price 佣金比率
 * @property int $refund_tag 维权标签，0 含义为非维权 1 含义为维权订单
 * @property int $cash_back 0未返现 1已返现
 * @property int $status 12-付款，13-关闭，14-确认收货，3-结算成功 0-默认未知状态
 * @property string $goods_item_id 商品id
 * @property int $goods_item_num 商品数量
 * @property string $goods_title 商品标题
 * @property string $goods_link 商品链接
 * @property string $goods_img 商品图片
 * @property string $seller_shop_title 卖家店铺名称
 * @property string $seller_nick 卖家名称
 * @property string $sub_type
 * @property string|null $extends_json 扩展字段
 * @property int $st 订单所属的时间范围 开始时间 用于之后更新订单状态使用 目前京东和淘宝没有单独根据订单编号来获取订单详情的接口
 * @property int $et 订单所属的时间范围 结束时间
 * @property float $user_self_fee_rate 自购比率
 * @property float $user_share_fee_rate 分享比率
 * @property float $system_fee_rate 平台扣除比率 百分比
 * @property int $settle_time 结算时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $order_earning_time
 * @property float $subsidy_fee_rate 平台补贴
 * @property int $p_uid 父级用户pid
 * @property int $pp_uid 父级的父级用户pid
 * @property int $sys_settlement
 * @property int $is_free 0非免单，1免单，2已返佣
 * @property float $qh_final_price 卷后价
 * @property int $goods_partition 商品分区
 * @property int $goods_cat_one 商品分区
 * @property int $goods_cat_two 商品分区
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Goods[] $goodsInfo
 * @property-read int|null $goods_info_count
 * @property-read \App\Models\TbRelationUser|null $tbBind
 * @property-read \App\Models\UsersBenefit|null $userAmount
 * @property-read \App\Models\UsersInfo|null $userInfo
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereCashBack($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereEt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereExtendsJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereGoodsCatOne($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereGoodsCatTwo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereGoodsImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereGoodsItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereGoodsItemNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereGoodsLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereGoodsPartition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereGoodsTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereIsFree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereOrderEarningTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereOrderParentSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePayDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePayPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePayTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePayYm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePpUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePreFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereQhFinalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereRefundTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereSellerNick($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereSellerShopTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereSettleTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereShareUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereSt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereSubType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereSubsidyFeeRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereSysSettlement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereSystemFeeRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereTotalCommissionFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereTotalCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereUserSelfFeeRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereUserShareFeeRate($value)
 * @mixin \Eloquent
 */
class Order extends Model
{
    protected $connection = 'lx-mall';
    protected $table = 'order';

    const STATUS_WAIT_PAID = 11;
    const STATUS_PAID = 12;
    const STATUS_CLOSED = 13;
    const STATUS_RECEIPT = 14;
    const STATUS_FEE_SUCCESS = 3;//结算成
    const STATUS_OTHER = -99;//其他类型
    const STATUS_INVALID = 15;//无效
    const STATUS_COMPLETE = 16;//已完成
    const STATUS_TEAM_OK = 17; //已成团
    const STATUS_VERIFY_SUCCESS = 18;//审核成功
    const STATUS_VERIFY_FAIL = 19;//审核失败 不可提现

    const STATUS_FAN_LI=['0'=>'未返','1'=>'已返'];

    const REFUND_TAG_NOT = 0;//没有售后状态
    const REFUND_TAG_ING = 1;//售后中
    const REFUND_TAG_SUCCESS = 2;//维权成功
    const REFUND_TAG_FAIL = 3;//维权失败 维权失败需要给用户返回佣金

    public static function getRefundTagMap($key = "ALL", $default = "未知状态")
    {
        $ret = [
            self::REFUND_TAG_NOT => "无维权",
            self::REFUND_TAG_ING => "维权中",
            self::REFUND_TAG_SUCCESS => "维权成功",
            self::REFUND_TAG_FAIL => "维权失败",
        ];
        if ($key === "ALL") {
            return $ret;
        }
        return Arr::get($ret, $key, $default);
    }

    public static function getStatusMap($key = "ALL", $default = "未知状态")
    {
        $ret = [
            self::STATUS_PAID => "已付款",
            self::STATUS_CLOSED => "已关闭",
            self::STATUS_RECEIPT => "已收货",
            self::STATUS_FEE_SUCCESS => "已结算",
            self::STATUS_OTHER => "其他",
            self::STATUS_INVALID => "无效",
            self::STATUS_WAIT_PAID => "待支付",
            self::STATUS_COMPLETE => "已完成",
            self::STATUS_TEAM_OK => "已成团",
            self::STATUS_VERIFY_SUCCESS => "审核成功",
            self::STATUS_VERIFY_FAIL => "审核失败"
        ];
        if ($key === "ALL") {
            return $ret;
        }
        return Arr::get($ret, $key, $default);
    }

//    public function getSourceAttribute($value)
//    {
//        return Goods::getSourceMap($value);
//    }


    public function tbBind(){
        return $this->hasOne(TbRelationUser::class,'id','uid');
    }
    public function userInfo(){
        return $this->hasOne(UsersInfo::class,'id','uid');
    }

    public function goodsInfo(){
        return $this->hasMany(Goods::class,'gid','goods_item_id');
    }

    public function userAmount(){
        return $this->hasOne(UsersBenefit::class,'order_sn','order_sn')->where('order_source',$this->source)->where('beneficiary_uid',$this->uid);
    }
}

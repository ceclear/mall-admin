<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserBenefitTotal
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal query()
 * @mixin \Eloquent
 * @property int $id
 * @property int $uid
 * @property string $ym
 * @property float $amount 团队收益金额
 * @property float $ext_amount 额外收益金额
 * @property float $amount_subsidy 平台补贴
 * @property float $amount_team 团队贡献
 * @property float $total_amount 总收益金额
 * @property int $status 是否结算如余额
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property float $per_amount 余额
 * @property float $per_ext_amount 冻结
 * @property float $per_amount_subsidy 累计收益
 * @property float $per_amount_team 累计收益
 * @property float $per_total_amount 累计收益
 * @property int $pay_order_num 付款订单数
 * @property int $settle_order_num 结算订单数
 * @property string $ymd
 * @property int $pay_order_team_num 付款团队/粉丝订单数
 * @property int $settle_order_team_num 结算团队/粉丝订单数
 * @property int $share_pay_order_num 分享订支付单数
 * @property int $share_settle_order_num 分享结算订单数
 * @property int $share_amount 分享结算金额
 * @property int $share_pre_amount 分享预估金额
 * @property float $my_total_amount 我的收入 不包含团队的总收入
 * @property float $my_pre_total_amount 我的预估收入 不包含团队的总收入
 * @property int $order_source 订单来源
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal whereAmountSubsidy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal whereAmountTeam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal whereExtAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal whereMyPreTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal whereMyTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal whereOrderSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal wherePayOrderNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal wherePayOrderTeamNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal wherePerAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal wherePerAmountSubsidy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal wherePerAmountTeam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal wherePerExtAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal wherePerTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal whereSettleOrderNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal whereSettleOrderTeamNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal whereShareAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal whereSharePayOrderNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal whereSharePreAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal whereShareSettleOrderNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal whereYm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal whereYmd($value)
 * @property int $user_level 用户团队级别
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBenefitTotal whereUserLevel($value)
 */
class UserBenefitTotal extends Model
{
    protected $table = "user_benefit_total";
    protected $connection = "lx-mall";
}

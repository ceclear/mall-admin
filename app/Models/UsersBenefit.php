<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UsersBenefit
 *
 * @property int $id
 * @property int $beneficiary_uid 收益人
 * @property int $contributor_uid 贡献人
 * @property int $contributor_level 贡献人级别
 * @property int $beneficiary_level 受益人级别
 * @property int $beneficiary_stratum 贡献层数 也就是直属 一级 二级 这里只有两级
 * @property string $order_sn 所属订单编号
 * @property int $order_source 订单来源 1淘宝 2京东 3...
 * @property float $order_benefit_amount 订单收益金额 【对应订单的预估收益或者佣金】
 * @property float $order_pay_price 订单支付金额
 * @property float $amount 直接收益 返利用这个算
 * @property float $amount_subsidy 平台补贴收益
 * @property float $amount_ext
 * @property float $total_amount
 * @property float|null $rate 收益比率 如果是自购的则表示自购+平台补贴，分享则为分享比率，返利是一级就是一级比率 耳机就是耳机比率
 * @property float $team_colonel_rate 向上级团长返利的团队比率 2级以外 具体算法
 * @property float $team_colonel_amount 向上级团长返利的团队比率 2级以外 具体算法 team_colonel_amount = (team_colonel_rate * amount) / 团长人数
 * @property int $team_colonel_num 团队团长数
 * @property float $team_senior_colonel_rate 向上级高级团长返利的团队比率 2级以外 算法同上
 * @property float $team_senior_colonel_amount
 * @property int $team_senior_colonel_num 团队高级团长数
 * @property string $order_pay_ym 订单支付年月 以及预估收益 当月
 * @property string|null $order_pay_date 订单支付年月日 以及预估收益 当日
 * @property string|null $order_earning_date 确认收货且卖家完成佣金支付的日期
 * @property string $order_earning_ym 确认收货且卖家完成佣金支付的年月 用于结算
 * @property int $order_earning_time
 * @property int $order_pay_time
 * @property int $order_status 订单状态 和订单表一致
 * @property int $is_settle 是否结算 【系统结算】
 * @property int $is_direct_settle
 * @property int $type 1自购 2分享 3直属返利
 * @property string|null $extends_json 扩展字段
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property float $amount_base 基础金额 用于计算返利的基础值
 * @property int $is_per_settle 是否结算 【预估结算】
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereAmountBase($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereAmountExt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereAmountSubsidy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereBeneficiaryLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereBeneficiaryStratum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereBeneficiaryUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereContributorLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereContributorUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereExtendsJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereIsDirectSettle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereIsPerSettle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereIsSettle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereOrderBenefitAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereOrderEarningDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereOrderEarningTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereOrderEarningYm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereOrderPayDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereOrderPayPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereOrderPayTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereOrderPayYm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereOrderSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereOrderStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereTeamColonelAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereTeamColonelNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereTeamColonelRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereTeamSeniorColonelAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereTeamSeniorColonelNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereTeamSeniorColonelRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBenefit whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UsersBenefit extends Model
{
    protected $table = "users_benefit";
    protected $connection = "lx-mall";
}

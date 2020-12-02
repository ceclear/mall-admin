<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Withdrawal
 *
 * @property int $id
 * @property int $uid 用户ID
 * @property string $trade_no 订单编号
 * @property float $amount 变动金额
 * @property int $status 状态：0待处理 1已处理 2失败
 * @property string|null $remark 备注
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $out_trade_no 外部订单编号
 * @property string|null $real_name 真实姓名
 * @property string|null $mobile 手机号
 * @property string|null $alipay 支付宝
 * @property int $operator 操作人
 * @property string|null $completed_at 完成时间
 * @property int $withdrawal_type 0为支付宝提现，1为微信提现
 * @property string|null $wx_openid 微信openid
 * @property string|null $id_card 身份证
 * @property-read \Encore\Admin\Auth\Database\Administrator|null $manage
 * @property-read \App\Models\UsersInfo|null $user
 * @property-read \App\Models\UserCards|null $userCards
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdrawal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdrawal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdrawal query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdrawal whereAlipay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdrawal whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdrawal whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdrawal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdrawal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdrawal whereIdCard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdrawal whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdrawal whereOperator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdrawal whereOutTradeNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdrawal whereRealName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdrawal whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdrawal whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdrawal whereTradeNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdrawal whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdrawal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdrawal whereWithdrawalType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Withdrawal whereWxOpenid($value)
 * @mixin \Eloquent
 */
class Withdrawal extends Model
{
    protected $connection = 'lx-mall';
    protected $table = 'withdrawal';

    protected $fillable = [
        'status',
        'remark',
        'out_trade_no',
        'completed_at',
        'operator'
    ];

    const STATUS_PENDING = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_FAILED = 2;
    const STATUS_REFUSE = 3;
    const STATUS_JOB = 4;

    static $status = [
        self::STATUS_PENDING => '待处理',
        self::STATUS_SUCCESS => '成功',
        self::STATUS_FAILED => '失败',
        self::STATUS_REFUSE => '拒绝',
        self::STATUS_JOB => '待打款'
    ];

    // 自动拒绝错误理由
    public static $wechatAutoDeny = [
        'NAME_MISMATCH' => '微信姓名不一致', // 付款人身份校验不通过
        'V2_ACCOUNT_SIMPLE_BAN' => '微信未通过实名', // 无法给未实名用户付款
        'SENDNUM_LIMIT' => '当日提现次数超限', // 该用户今日付款次数超过限制
    ];


    /**
     * 关联管理员
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function manage()
    {
        return $this->hasOne(\Encore\Admin\Auth\Database\Administrator::class, 'id', 'operator');
    }

    public function user()
    {
        return $this->hasOne(UsersInfo::class, 'id', 'uid');
    }

    public function userCards()
    {
        return $this->hasOne(UserCards::class, "uid", "uid");
    }
}

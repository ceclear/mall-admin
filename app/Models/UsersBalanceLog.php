<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UsersBalanceLog
 *
 * @property int $id
 * @property int $uid 用户ID
 * @property float $amount 余额
 * @property float $amount_before_change 变动前金额
 * @property string|null $remark 备注
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $operation_log 操作备注
 * @property string $tag 标签, 可根据此字段定义特殊操作日志的标识，可做回滚处理
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBalanceLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBalanceLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBalanceLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBalanceLog whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBalanceLog whereAmountBeforeChange($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBalanceLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBalanceLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBalanceLog whereOperationLog($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBalanceLog whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBalanceLog whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBalanceLog whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBalanceLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UsersBalanceLog extends Model
{
    protected $connection = 'lx-mall';
    protected $table = "users_balance_log";

    public static function addLog($uid, $amount, $amountBeforeChange, $remark = "")
    {
        $self = new self();
        $self->uid = $uid;
        $self->amount = $amount;
        $self->amount_before_change = $amountBeforeChange;
        $self->remark = $remark;
        return $self->save();
    }
}

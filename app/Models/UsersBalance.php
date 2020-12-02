<?php

namespace App\Models;

use App\Errors;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UsersBalance
 *
 * @property int $id
 * @property int $uid
 * @property float $balance 余额
 * @property float $freeze_balance 冻结
 * @property float $total_income 累计收益
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\UsersInfo|null $userInfo
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBalance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBalance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBalance query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBalance whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBalance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBalance whereFreezeBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBalance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBalance whereTotalIncome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBalance whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersBalance whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UsersBalance extends Model
{
    use Errors;

    protected $connection = 'lx-mall';
    protected $table = "users_balance";

    /**
     * 使用isLock为true时 方法外部一定要开启事务
     * @param $uid
     * @param string $select
     * @param bool $isLock
     * @return UsersBalance|\Illuminate\Database\Eloquent\Builder|Model|object|null
     */
    public static function getBalanceByUid($uid, $select = "*", $isLock = false)
    {
        RE:
        $ret = self::where("uid", $uid);
        if ( $isLock ) {
            $ret->lockForUpdate();
        }
        $ret = $ret->select($select)->first();
        if ( $ret ) {
            return $ret;
        }
        $ret = new self();
        $ret->uid = $uid;
        $ret->balance = 0;
        $ret->freeze_balance = 0;
        $ret->total_income = 0;
        $ret->save();
        if ( $isLock ) {
            goto RE;
        }
        return $ret;
    }

    public function userInfo()
    {
        return $this->hasOne(UsersInfo::class, "id", "uid");
    }
}

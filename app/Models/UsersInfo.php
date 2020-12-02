<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * App\Models\UsersInfo
 *
 * @property int $id 直接对应uid
 * @property int $pid 上级id
 * @property string $nickname 昵称
 * @property string $phone 手机号码
 * @property string|null $wx_openid 微信openid
 * @property string|null $exclusive_code_invite 专属口令
 * @property string $avatar 头像
 * @property int $level 1合伙人 2团长 3高级团长 这个level跟feeCfg里的type保持一样
 * @property string|null $login_times
 * @property string|null $login_ip
 * @property string $token
 * @property string $password
 * @property string $salt
 * @property int $token_status 1正常 0禁用 用于设置一个token的失效
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $register_time
 * @property int $today_invite_count 今日直接邀请数
 * @property int $month_invite_count 当月直接邀请数
 * @property int $is_exclusive 是否设置过专属口令
 * @property string|null $upgrade_time
 * @property int $recommend_num 有效直推人数
 * @property int $is_valid 是否有效 0无效，1有效
 * @property int $ppid 耳机父级id
 * @property string $wx 微信号
 * @property string|null $wx_qr 微信号二维码
 * @property string|null $first_login_times 首次登陆时间
 * @property string|null $wx_nickname 微信昵称
 * @property string|null $id_card 身份证
 * @property-read \App\Models\UsersGroup|null $groupParentsNum
 * @property-read \App\Models\UsersInfo|null $inviteUser
 * @property-read \App\Models\TbRelationUser|null $relationUser
 * @property-read \App\Models\UsersBalance|null $userBalance
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo whereExclusiveCodeInvite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo whereFirstLoginTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo whereIdCard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo whereIsExclusive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo whereIsValid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo whereLoginIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo whereLoginTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo whereMonthInviteCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo wherePpid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo whereRecommendNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo whereRegisterTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo whereSalt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo whereTodayInviteCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo whereTokenStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo whereUpgradeTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo whereWx($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo whereWxNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo whereWxOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersInfo whereWxQr($value)
 * @mixin \Eloquent
 */
class UsersInfo extends Model
{
    protected $connection = 'lx-mall';
    protected $table = "users_info";


    const LEVEL_1 = 1;
    const LEVEL_2 = 2;
    const LEVEL_3 = 3;

    public static function getSourceMap($key = "ALL")
    {
        $ret = [
            self::LEVEL_1 => "合伙人",
            self::LEVEL_2 => "团长",
            self::LEVEL_3 => "高级团长"
        ];
        if ($key === "ALL") {
            return $ret;
        }
        return Arr::get($ret, $key, false);
    }

    public function getLevelAttribute($value)
    {
        return self::getSourceMap($value);
    }

    public function inviteUser()
    {
        return $this->hasOne(self::class, "id", "pid");
    }

    public function groupParentsNum()
    {
        return $this->hasOne(UsersGroup::class, "id", "id");
    }

    public function relationUser(){
        return $this->hasOne(TbRelationUser::class,'id','id');
    }

    public function userBalance(){
        return $this->hasOne(UsersBalance::class,'uid','id');
    }
}

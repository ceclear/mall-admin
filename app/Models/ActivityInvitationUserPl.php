<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityInvitationUserPl extends Model
{

    protected $connection = 'lx-mall';
    protected $table = 'activity_invitation_user_pl';

    public static function getRowOrCreate($uid, $isLock = false, $isSave = true)
    {
        $ret = $isLock ? self::whereUid($uid)->lockForUpdate()->first() : self::whereUid($uid)->first();
        if ( $ret ) {
            return $ret;
        }
        $ret = new self();
        $ret->uid = $uid;
        $ret->prize_num = 1;
        $ret->prize_num_total = 1;
        $ret->prize_num_already = 0;
        $ret->total_hf_amount = 0;
        if ( !$isSave ) {
            return $ret;
        }
        if ( !$ret->save() ) {
            return false;
        }
        return $ret;
    }

    public static function addPrizeNum($uid, $num = 1)
    {
        //给上级增加抽奖次数
        \DB::beginTransaction();
        $userPl = ActivityInvitationUserPl::getRowOrCreate($uid, true, false);
        $userPl->prize_num += $num;
        $userPl->prize_num_total += $num;
        $userPl->save();
        \DB::commit();
        return $userPl;
    }
}

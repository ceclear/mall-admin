<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ActivityInvitationPrizeLog
 *
 * @property int $id
 * @property int $uid
 * @property int $type 活动类型
 * @property int $prize_num 第几次抽奖
 * @property float $hf_amount 当前累计的话费数
 * @property string $prize 奖品
 * @property int $status -1 未中奖 0待领取 1已发放 2已领取
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $nickname
 * @property string|null $desc
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityInvitationPrizeLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityInvitationPrizeLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityInvitationPrizeLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityInvitationPrizeLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityInvitationPrizeLog whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityInvitationPrizeLog whereHfAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityInvitationPrizeLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityInvitationPrizeLog whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityInvitationPrizeLog wherePrize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityInvitationPrizeLog wherePrizeNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityInvitationPrizeLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityInvitationPrizeLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityInvitationPrizeLog whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityInvitationPrizeLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ActivityInvitationPrizeLog extends Model
{
    protected $table = "activity_invitation_prize_log";
    protected $connection = "lx-mall";
    /*const */
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SharePosters
 *
 * @property int $id
 * @property string $poster 海报
 * @property int $status 1显示，0禁用
 * @property string $qr_url 二维码地址
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $type 类型
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SharePosters newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SharePosters newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SharePosters query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SharePosters whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SharePosters whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SharePosters wherePoster($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SharePosters whereQrUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SharePosters whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SharePosters whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SharePosters whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SharePosters extends Model
{
    protected $connection = "lx-mall";
    protected $table = "share_posters";

    public static $type = [
        'personal_center' => '个人中心',
        'rank' => '琅琊榜',
        'first_reward' => '新用户奖励',
        'free' => '新人免单',
        "invite" => "邀请新人",
        "sale_rank" => "销售榜单",
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Alert
 *
 * @property int $id
 * @property string $title
 * @property string $thumbnail 缩略图
 * @property int $jump_type 跳转类型
 * @property string|null $jump_id
 * @property string $jump_url 跳转地址
 * @property int $sort 排序
 * @property int $status 状态 1开启，0关闭
 * @property string|null $begin_time 开始时间
 * @property string|null $end_time 结束时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert whereBeginTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert whereJumpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert whereJumpType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert whereJumpUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert whereThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Alert extends Model
{
    protected $connection = "lx-mall";
    protected $table = 'alert';

    static public $jumpType = [
        1 => '外部跳转',
        2 => '普通内部跳转',
        3 => '五大首页内部跳转',
    ];
}

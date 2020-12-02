<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AdPosition
 *
 * @property int $id
 * @property string $name 广告位名称
 * @property string $symbol 广告位标识
 * @property int $sort 排序
 * @property int $status 状态，1正常0禁用
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdPosition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdPosition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdPosition query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdPosition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdPosition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdPosition whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdPosition whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdPosition whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdPosition whereSymbol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdPosition whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $bg_color 背景色
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdPosition whereBgColor($value)
 */
class AdPosition extends Model
{
    protected $connection = 'lx-mall';
    protected $table = 'ad_position';
}

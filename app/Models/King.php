<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\King
 *
 * @property int $id
 * @property string $title 文案
 * @property string|null $url 跳转链接可不填
 * @property string $icon 图标
 * @property int|null $sort 排序
 * @property int $skip 跳转id
 * @property int|null $status 状态1正常0禁用
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\King newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\King newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\King query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\King whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\King whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\King whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\King whereSkip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\King whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\King whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\King whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\King whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\King whereUrl($value)
 * @mixin \Eloquent
 */
class King extends Model
{
    protected $connection = 'lx-mall';
    protected $table = 'king';

}

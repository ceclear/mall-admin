<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Rebate
 *
 * @property int $id
 * @property string|null $key key值
 * @property string $url 链接
 * @property string $title 标题
 * @property string|null $tip tips
 * @property string $icon icon
 * @property int|null $status 状态1正常0禁用
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $sort 排序
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rebate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rebate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rebate query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rebate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rebate whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rebate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rebate whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rebate whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rebate whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rebate whereTip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rebate whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rebate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rebate whereUrl($value)
 * @mixin \Eloquent
 */
class Rebate extends Model
{
    protected $connection = 'lx-mall';
    protected $table = 'rebate';
}

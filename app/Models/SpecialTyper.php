<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/13
 * Time: 20:17
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SpecialTyper
 *
 * @property int $id
 * @property string $title 分类名称
 * @property int|null $sort 排序
 * @property int $status 状态；1=发布，2=保存；3=关闭
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpecialTyper newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpecialTyper newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpecialTyper query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpecialTyper whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpecialTyper whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpecialTyper whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpecialTyper whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpecialTyper whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpecialTyper whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SpecialTyper extends Model
{
    protected $connection = 'lx-mall';
    protected $table = 'special_type';
}
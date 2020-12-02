<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Advert
 *
 * @property int $id
 * @property int $pos_id 位置id
 * @property int $skip_type 跳转类型1跳转活动页2点击跳转3app内跳转
 * @property int|null $product_id 数据ID
 * @property string $url 跳转链接
 * @property string $img_url 图片链接
 * @property int $sort 排序
 * @property int $status 状态1正常0禁用
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $name 名称
 * @property-read \App\Models\AdPosition|null $ad_position
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advert newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advert query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advert whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advert whereImgUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advert whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advert wherePosId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advert whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advert whereSkipType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advert whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advert whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advert whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advert whereUrl($value)
 * @mixin \Eloquent
 * @property string|null $bg_color 背景色
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Advert whereBgColor($value)
 */
class Advert extends Model
{
    protected $connection = 'lx-mall';
    protected $table = 'advert';

    const SKIP_TYPE = [
        1 => '跳转活动页',
        2 => '点击跳转',
        3 => 'app内跳转'
    ];

    public function ad_position()
    {
        return $this->hasOne(AdPosition::class, 'id', 'pos_id');
    }
}

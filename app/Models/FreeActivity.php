<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FreeActivity
 *
 * @property int $id
 * @property string $title 页面title
 * @property string $wx 导师微信
 * @property string $tb_goods 淘宝商品
 * @property string $pdd_goods 拼多多商品
 * @property string $img 推荐活动缩略图
 * @property string $uri 推荐活动地址
 * @property string $rule 新人免单规则
 * @property string|null $start_time 开始时间
 * @property string|null $end_time 开始时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreeActivity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreeActivity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreeActivity query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreeActivity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreeActivity whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreeActivity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreeActivity whereImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreeActivity wherePddGoods($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreeActivity whereRule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreeActivity whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreeActivity whereTbGoods($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreeActivity whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreeActivity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreeActivity whereUri($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreeActivity whereWx($value)
 * @mixin \Eloquent
 */
class FreeActivity extends Model
{
    protected $connection = 'lx-mall';
    protected $table = 'free_activity';
}

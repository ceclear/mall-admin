<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PushType
 *
 * @property int $id
 * @property string $name 类型名称
 * @property int $is_index 是否首页，0为不是，1为是
 * @property string|null $url 地址
 * @property string|null $param 参数
 * @property int $status 0保存，1发布, 2关闭
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PushType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PushType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PushType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PushType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PushType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PushType whereIsIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PushType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PushType whereParam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PushType whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PushType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PushType whereUrl($value)
 * @mixin \Eloquent
 */
class PushType extends Push
{
    protected $table = 'push_type';

    const STATUS_SAVE = 0;//保存
    const STATUS_PUSH = 1;//发布
    const STATUS_BAN = 2;//禁用

    //状态
    static $status = [
        -1 => '全部',
        self::STATUS_SAVE => '保存',
        self::STATUS_PUSH => '发布',
        self::STATUS_BAN => '关闭'
    ];

    static $goodsPush = [
        'goodsdetail',//商品详情
        '99freeshipping',//9.9包邮
        'brandflash',//品牌闪购
        'limitflash',//限时秒杀
        'mallrebate'//商城返利
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Circle
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Circle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Circle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Circle query()
 * @mixin \Eloquent
 */
class Circle extends Model
{
    protected $connection = 'lx-mall';

    const STATUS_SAVE = 0;//保存
    const STATUS_PUSH = 1;//发布
    const STATUS_BAN = 2;//禁用

    //状态
    static $status = [
        -1 => '全部',
        self::STATUS_SAVE => '保存',
        self::STATUS_PUSH => '发布',
        self::STATUS_BAN => '禁用'
    ];


    //状态颜色
    static $status_color = [
        0 => 'info',
        1 => 'success',
        2 => 'danger'
    ];

    /**
     * 根据集合数据获取节点数据
     * @param $area_collection 集合数组
     * @return array
     */
    public static function toNode($area_collection) {
        $node = [];

        foreach ($area_collection as $value) {
            $node[$value->id] = $value->name;
        }

        return $node;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Push
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Push newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Push newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Push query()
 * @mixin \Eloquent
 */
class Push extends Model
{
    protected $connection = 'lx-mall';

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

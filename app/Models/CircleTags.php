<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CircleTags
 *
 * @property int $id
 * @property string $name 标签名称
 * @property int $status 0保存，1发布 2禁用
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleTags newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleTags newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleTags query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleTags whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleTags whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleTags whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleTags whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleTags whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CircleTags extends Circle
{
    protected $table = 'circle_tags';

    /**
     * 获取标签
     * @param array $status
     * @return mixed
     */
    public static function getTag(array $status = []) {
        $model = self::select('id', 'name');

        if($status) {
            $model->where('status', $status);
        } else {
            $model->where('status', self::STATUS_PUSH);
        }

        return $model->get();
    }
}

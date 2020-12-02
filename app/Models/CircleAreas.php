<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CircleAreas
 *
 * @property int $id
 * @property int $type 板块
 * @property string $name 分区名称
 * @property int $status 0保存，1发布 2禁用
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $sort 排序
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleAreas newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleAreas newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleAreas query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleAreas whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleAreas whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleAreas whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleAreas whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleAreas whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleAreas whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleAreas whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CircleAreas extends Circle
{
    protected $table = 'circle_areas';

    const PLATE_SELECT = 1;//链信优选
    const PLATE_MATERIAL = 2;//营销素材
    const PLATE_SCHOOL = 3;//链信学院

    static $type = [
        -1 => '请选择板块',
        self::PLATE_SELECT => '链信优选',
        self::PLATE_MATERIAL => '营销素材',
        self::PLATE_SCHOOL => '链信学院'
    ];

    /**
     * 根据板块和状态获取分区
     * @param int $type 板块
     * @param array $status 状态
     * @return mixed
     */
    public static function getArea(int $type, array $status = []) {
        $model = self::where('type', $type);

        if($status) {
            $model->where('status', $status);
        } else {
            $model->where('status', self::STATUS_PUSH);
        }

        return $model->select('id', 'name')->get();
    }
}

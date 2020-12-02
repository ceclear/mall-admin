<?php

namespace App\Models;

use App\GlobalConstant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\GoodsCat
 *
 * @property int $id
 * @property string $name 分类名称
 * @property int $pid 父级id
 * @property int $level 1
 * @property int $sort 排序
 * @property string|null $icon icon图标
 * @property int $status 0禁用 1正常
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $is_grab 是否自动采集
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsCat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsCat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsCat query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsCat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsCat whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsCat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsCat whereIsGrab($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsCat whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsCat whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsCat wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsCat whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsCat whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsCat whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GoodsCat extends Model
{
    protected $connection = 'lx-mall';
    protected $table = 'goods_cat';
    protected $guarded = ['id'];

    public static function getStatusMap($key = "ALL")
    {
        $ret = [
            GlobalConstant::IS_YES => "正常",
            GlobalConstant::IS_NO  => "禁用",
        ];
        if ($key === "ALL") {
            return $ret;
        }
        return Arr::get($ret, $key, false);
    }


    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (strpos($model->icon, "http") === false) {
                $model->icon = rtrim(env("OSS_URL"), "/") . "/" . ltrim($model->icon, "/");
            }
        });
    }

    public static function createRowTaoBaoKeCat($list)
    {
        $arr = [];
        foreach ($list as $item) {
            $data  = [
                'name' => $item['name'],
                'pid'  => $item['pid'],
                'icon' => $item['icon'],
                'cid'  => $item['cid']
            ];
            $arr[] = $data;
        }
        Cache::set('tbk_cat', $arr);
        return true;
    }
}

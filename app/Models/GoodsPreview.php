<?php

namespace App\Models;

use App\GlobalConstant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * App\Models\GoodsPreview
 *
 * @property int $id
 * @property int $source 来源 1淘宝 2京东 3...
 * @property int $gid 对应每个来源的商品id
 * @property int|null $cat_id_one 一级分类
 * @property int|null $cat_id_two 二级分类
 * @property int|null $partition 商品分区 1猜你喜欢 2火爆推荐 以实际代码常量为准
 * @property string $pre_date 预采集时间及上线时间
 * @property int|null $is_special 是否特殊商品
 * @property int|null $success 采集状态0未采集1成功2失败
 * @property string|null $remark 备注
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $publisher 添加人
 * @property-read \App\Models\GoodsCat|null $catOne
 * @property-read \App\Models\GoodsCat|null $catTwo
 * @property-read \App\Models\Goods|null $goodInfo
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsPreview newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsPreview newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsPreview query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsPreview whereCatIdOne($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsPreview whereCatIdTwo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsPreview whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsPreview whereGid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsPreview whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsPreview whereIsSpecial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsPreview wherePartition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsPreview wherePreDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsPreview wherePublisher($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsPreview whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsPreview whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsPreview whereSuccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GoodsPreview whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GoodsPreview extends Model
{
    protected $connection = 'lx-mall';
    protected $table = 'goods_preview';
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

    public function catOne()
    {
        return $this->hasOne(GoodsCat::class, "id", "cat_id_one");
    }
    public function catTwo()
    {
        return $this->hasOne(GoodsCat::class, "id", "cat_id_two");
    }

    public function goodInfo(){
        return $this->hasOne(Goods::class,'gid','gid');
    }

}

<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/13
 * Time: 21:49
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * App\Models\SpecialBrand
 *
 * @property int $id
 * @property string $title 专场名称
 * @property string $logo logo
 * @property string|null $seckill_time_start 秒杀时间开始
 * @property string|null $seckill_time_end 秒杀时间结束
 * @property int $special_type_id 专场ID
 * @property string $goods_id 商品ID
 * @property float|null $min_price 最低价
 * @property float|null $min_discount 最低折扣
 * @property int|null $sort 排序
 * @property int $status 状态；1=发布，2=保存；3=关闭
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $is_top 是否置顶
 * @property string|null $top_time 置顶时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpecialBrand newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpecialBrand newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpecialBrand query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpecialBrand whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpecialBrand whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpecialBrand whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpecialBrand whereIsTop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpecialBrand whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpecialBrand whereMinDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpecialBrand whereMinPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpecialBrand whereSeckillTimeEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpecialBrand whereSeckillTimeStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpecialBrand whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpecialBrand whereSpecialTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpecialBrand whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpecialBrand whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpecialBrand whereTopTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SpecialBrand whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SpecialBrand extends Model
{
    protected $connection = 'lx-mall';
    protected $table = 'special_brand';

    public function getSpecialType()
    {
        return $this->belongsTo(SpecialTyper::class,'special_type_id','id');
    }

//    public function getGoodsIdAttribute($value)
//    {
//        return explode(',', $value);
//    }
//
//    public function setGoodsIdAttribute($value)
//    {
//        $this->attributes['goods_id'] = implode(',', $value);
//    }

    public function setLogoAttribute($logo)
    {
        $newLogo = $logo;
        if ( stripos($logo, "http") === false ) {
            $newLogo = "";
        }
        $this->attributes['logo'] = $newLogo;
    }

    public function getLogoAttribute($logo)
    {
        if ( stripos($logo, "http") === false ) {
            return "";
        }
        return $logo;
    }

}
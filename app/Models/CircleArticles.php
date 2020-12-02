<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\CircleArticles
 *
 * @property int $id
 * @property int $type 类别
 * @property string|null $area_id 分区ID
 * @property string|null $title 标题
 * @property int|null $tag_id 标签ID
 * @property int $account_type 账户类型
 * @property int $account_id 账户ID
 * @property string|null $avatar 缩略图
 * @property int $recommend_type 推荐类型
 * @property string|null $recommend_good 推荐商品
 * @property string|null $online_word 在线文档
 * @property int $is_top 是否置顶
 * @property int|null $top_day 置顶天数
 * @property string $release_time 发布时间
 * @property string|null $flash_time_start 秒杀时间
 * @property int $status 0保存，1发布，2未发布，3禁用
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $flash_time_end 秒杀结束时间
 * @property int $show_video 是否展示商品视频
 * @property string|null $show_title 活动标题
 * @property string|null $show_cover 视频/获取封面
 * @property string|null $material 此字段为Attribute使用，不存储数据
 * @property-read \App\Models\CircleAccounts|null $account
 * @property-read \App\Models\CircleAreas|null $area
 * @property-read \App\Models\CircleArticleInfo|null $info
 * @property-read \App\Models\CircleTags|null $tag
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticles newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticles newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticles query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticles whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticles whereAccountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticles whereAreaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticles whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticles whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticles whereFlashTimeEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticles whereFlashTimeStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticles whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticles whereIsTop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticles whereMaterial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticles whereOnlineWord($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticles whereRecommendGood($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticles whereRecommendType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticles whereReleaseTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticles whereShowCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticles whereShowTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticles whereShowVideo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticles whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticles whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticles whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticles whereTopDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticles whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticles whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CircleArticles extends Circle
{
    protected $table = 'circle_articles';

    //商品推荐类型
    static $recommend_type = [
        1 => '商品',
        2 => '活动页'
    ];

    public static function getRecommendType()
    {
        return array_merge([0 => '请选择类型'], self::$recommend_type);
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (strpos($model->avatar, "http") === false) {
                $model->avatar = rtrim(env("OSS_URL"), "/") . "/" . ltrim($model->avatar, "/");
            }
        });
    }

    public function setFlashTimeEndAttribute($flash_time_end)
    {
        if ($flash_time_end) {
            $this->attributes['flash_time_end'] = Carbon::parse($flash_time_end)->endOfDay();
        }
    }

    public function getAreaIdAttribute($value)
    {
        if ($this->type == CircleAreas::PLATE_MATERIAL) {
            return explode(',', $value);
        }

        return $value;
    }

    public function setAreaIdAttribute($value)
    {
        $area_id = $value;
        if ($this->type == CircleAreas::PLATE_MATERIAL) {
            $area_id = implode(',', $value);
        }

        $this->attributes['area_id'] = $area_id;
    }

    public function setMaterialAttribute($value)
    {
        $old_content = $this->getMaterialAttribute();
        foreach ($value as $key => &$val) {
            // 如果旧数据未删除，且当前不存在图片，则将旧图片路径添加进去
            if (isset($old_content[$key]) && !isset($val['image'])) {
                $val['image'] = $old_content[$key]['image'];
            }
        }

        $keysValue = [];
        foreach ($value as $key => $row) {
            $keysValue[$key] = $row['sort'];
        }
        array_multisort($keysValue, SORT_ASC, $value);

        $value = array_values($value);

        $d = [];
        foreach ($value as $v) {
            $d[] = $v['image'];
        }

        //if (!isset($this->getAttributes()['id'])) {
            CircleArticleInfo::$material = json_encode($d);
//        } else {
//            CircleArticleInfo::where('article_id', $this->getAttributes()['id'])->update([
//                'material' => json_encode($d)
//            ]);
//        }
    }

    public function getMaterialAttribute()
    {
        $data = [];

        if (!isset($this->getAttributes()['id'])) {
            return $data;
        }

        $values = CircleArticleInfo::where('article_id', $this->getAttributes()['id'])->value('material');

        if ($values) {
            foreach ($values as $key => $value) {
                $data[$key] = [
                    'sort' => $key + 1,
                    'image' => $value
                ];
            }
        }

        return $data;
    }

    /**
     * 关联详情信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function info()
    {
        return $this->hasOne(CircleArticleInfo::class, 'article_id');
    }

    /**
     * 关联账户信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function account()
    {
        return $this->hasOne(CircleAccounts::class, 'id', 'account_id');
    }

    /**
     * 关联分区信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function area()
    {
        return $this->hasOne(CircleAreas::class, 'id', 'area_id');
    }

    /**
     * 关联标签信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function tag()
    {
        return $this->hasOne(CircleTags::class, 'id', 'tag_id');
    }
}

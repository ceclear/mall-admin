<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CircleArticleInfo
 *
 * @property int $id
 * @property int $article_id 内容ID
 * @property string|null $copy_writing 文案/描述
 * @property string|null $material 素材
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $video 商品视频
 * @property-read \App\Models\CircleArticles $article
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticleInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticleInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticleInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticleInfo whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticleInfo whereCopyWriting($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticleInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticleInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticleInfo whereMaterial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticleInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleArticleInfo whereVideo($value)
 * @mixin \Eloquent
 */
class CircleArticleInfo extends Circle
{
    protected $table = 'circle_article_info';

    static $material = null;

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->material = self::$material;
        });
    }

    public function article()
    {
        return $this->belongsTo(CircleArticles::class, 'article_id');
    }

//    public function setMaterialAttribute($material)
//    {
//        if (is_array($material)) {
//            foreach ($material as $key => $m) {
//                if (strpos($m, "http") === false) {
//                    $material[$key] = rtrim(env("OSS_URL"), "/") . "/" . ltrim($m, "/");
//                }
//            }
//            $this->attributes['material'] = json_encode($material);
//        }
//    }

    public function getMaterialAttribute($material)
    {
        $ret = json_decode($material, true);
        if ( is_array($ret) ) {
            foreach ( $ret as $k=>&$item ) {
                if ( stripos($item, "http") === false ) {
                    unset($ret[$k]);
                }
            }
        }
        return $ret;
    }

    public function setVideoAttribute($video)
    {
        if ($video && strpos($video, "http") === false) {
            $video = rtrim(env("OSS_URL"), "/") . "/" . ltrim($video, "/");
            $this->attributes['video'] = $video;
        }
    }
}

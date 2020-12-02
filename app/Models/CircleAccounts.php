<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CircleAccounts
 *
 * @property int $id
 * @property int $type 账号类型
 * @property string $nickname 昵称
 * @property string $avatar 头像
 * @property int $status 0保存，1发布 2禁用
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleAccounts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleAccounts newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleAccounts query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleAccounts whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleAccounts whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleAccounts whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleAccounts whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleAccounts whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleAccounts whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CircleAccounts whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CircleAccounts extends Circle
{
    protected $table = 'circle_accounts';

    //类型
    static $type = [
        1 => '官方账号',
        2 => '服装鞋包',
        3 => '美容保养',
        4 => '母婴用品',
        5 => '家居用品',
        6 => '餐饮美食',
        7 => '生活服务',
        8 => '酒水饮料',
        9 => '教育网络',
        10 => '建材装饰',
        11 => '汽车服务',
        12 => '环保机械',
        13 => '礼品饰品'
    ];

    public static function getType() {
        return array_merge([-1 => '全部'], self::$type);
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
}

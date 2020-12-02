<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PushList
 *
 * @property int $id
 * @property int $type_id 类型id
 * @property string|null $data_id 数据id
 * @property string $title 标题
 * @property string|null $desc 描述
 * @property \Illuminate\Support\Carbon $push_time 推送时间
 * @property int $platform 推送平台，详见model配置
 * @property int $status 0保存，1发布, 2失败
 * @property int $publisher 发布者
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $brand_id 品牌ID
 * @property-read \Encore\Admin\Auth\Database\Administrator|null $manage
 * @property-read \App\Models\PushType|null $type
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PushList newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PushList newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PushList query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PushList whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PushList whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PushList whereDataId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PushList whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PushList whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PushList wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PushList wherePublisher($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PushList wherePushTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PushList whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PushList whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PushList whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PushList whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PushList extends Push
{
    protected $table = 'push_list';

    const STATUS_SAVE = 0;//保存
    const STATUS_PUSH = 1;//发布
    const STATUS_SUSSESS = 2;//成功
    const STATUS_FAILED = 3;//失败

    const PLANT_ALL = 0;
    const PLANT_IOS = 1;
    const PLANT_ANDROID = 2;

    static $plant = [
        self::PLANT_ALL => '全部',
        self::PLANT_IOS => 'IOS',
        self::PLANT_ANDROID => '安卓'
    ];

    //状态
    static $status = [
        -1 => '全部',
        self::STATUS_SAVE => '保存',
        self::STATUS_PUSH => '发布',
        self::STATUS_SUSSESS => '发送成功',
        self::STATUS_FAILED => '发送失败'
    ];

    protected $dates = [
        'push_time',
    ];

    /**
     * 关联类型信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function type()
    {
        return $this->hasOne(PushType::class, 'id', 'type_id');
    }

    /**
     * 关联管理员
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function manage()
    {
        return $this->hasOne(\Encore\Admin\Auth\Database\Administrator::class, 'id', 'publisher');
    }
}

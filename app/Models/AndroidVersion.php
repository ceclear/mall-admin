<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AndroidVersion
 *
 * @property int $id
 * @property string $version 版本号
 * @property int $is_force 是否强制更新1
 * @property string $update_record 更新日志
 * @property int $status 状态1正常0禁用
 * @property string $create_user 创建人
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $file_url apk文件地址
 * @property int|null $type app类型1安卓2ios
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AndroidVersion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AndroidVersion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AndroidVersion query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AndroidVersion whereCreateUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AndroidVersion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AndroidVersion whereFileUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AndroidVersion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AndroidVersion whereIsForce($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AndroidVersion whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AndroidVersion whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AndroidVersion whereUpdateRecord($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AndroidVersion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AndroidVersion whereVersion($value)
 * @mixin \Eloquent
 */
class AndroidVersion extends Model
{
    protected $connection = 'lx-mall';
    protected $table = 'android_version';

}

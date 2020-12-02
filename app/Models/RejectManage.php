<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RejectManage
 *
 * @property int $id
 * @property string $yc_key 隐藏建
 * @property int|null $status 状态1正常0禁用
 * @property int|null $switch 隐藏状态0不隐藏1隐藏
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RejectManage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RejectManage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RejectManage query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RejectManage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RejectManage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RejectManage whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RejectManage whereSwitch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RejectManage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RejectManage whereYcKey($value)
 * @mixin \Eloquent
 */
class RejectManage extends Model
{
    protected $connection = 'lx-mall';
    protected $table = 'reject_manage';

}

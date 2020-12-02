<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AdminUserLog
 *
 * @property int $id
 * @property string|null $username
 * @property string|null $last_login_ip
 * @property string|null $last_login_time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminUserLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminUserLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminUserLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminUserLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminUserLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminUserLog whereLastLoginIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminUserLog whereLastLoginTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminUserLog whereUsername($value)
 * @mixin \Eloquent
 */
class AdminUserLog extends Model
{
    protected $table = 'admin_user_log';
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * App\Models\UsersGroup
 *
 * @property int $id 这个id与lianxin的users主键id保持一致 就不需要自增了
 * @property int $pid 直属父级id
 * @property int $ppid 耳机父级id
 * @property string|null $parents_id 所有父级id英文逗号分隔
 * @property int $parents_num 所有父级数量
 * @property string|null $children_colonel_ids 子集团长ids 按层级顺序保存
 * @property int $children_num 子集人数
 * @property int $children_colonel_num 子集团长数量
 * @property string|null $children_s_colonel_ids 高级团长
 * @property int $children_s_colonel_num 子集高级团长数量
 * @property int $team_level 团队级别 1合伙人 2团长 3高级团长
 * @property int $is_valid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $upgrade_time
 * @property string $group_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersGroup whereChildrenColonelIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersGroup whereChildrenColonelNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersGroup whereChildrenNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersGroup whereChildrenSColonelIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersGroup whereChildrenSColonelNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersGroup whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersGroup whereIsValid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersGroup whereParentsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersGroup whereParentsNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersGroup wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersGroup wherePpid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersGroup whereTeamLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersGroup whereUpgradeTime($value)
 * @mixin \Eloquent
 */
class UsersGroup extends Model
{
    protected $connection = 'lx-mall';
    protected $table = "users_group";

}

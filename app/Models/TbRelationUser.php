<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * App\Models\TbRelationUser
 *
 * @property int $id 与用户id保持一致
 * @property int|null $relation_id 渠道关系ID
 * @property int|null $special_id 淘宝用户sid
 * @property string $tb_nickname 淘宝账号昵称
 * @property string $tb_user_id 淘宝账号id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbRelationUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbRelationUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbRelationUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbRelationUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbRelationUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbRelationUser whereRelationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbRelationUser whereSpecialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbRelationUser whereTbNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbRelationUser whereTbUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbRelationUser whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TbRelationUser extends Model
{
    protected $connection = 'lx-mall';
    protected $table = 'tb_relation_user';
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FeeSysRate
 *
 * @property int $id
 * @property float $rate
 * @property int $status 1正常 0禁用
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeeSysRate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeeSysRate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeeSysRate query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeeSysRate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeeSysRate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeeSysRate whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeeSysRate whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeeSysRate whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FeeSysRate extends Model
{
    protected $connection = 'lx-mall';
    protected $table = 'fee_sys_rate';
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\NewPeopleFreeLogs
 *
 * @property int $id
 * @property int $uid
 * @property float $price 支付价格
 * @property string $order_sn
 * @property int $source
 * @property int $is_free
 * @property int $status
 * @property string|null $goods_item_id
 * @property int $goods_item_num
 * @property string|null $reason
 * @property float|null $new_price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewPeopleFreeLogs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewPeopleFreeLogs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewPeopleFreeLogs query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewPeopleFreeLogs whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewPeopleFreeLogs whereGoodsItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewPeopleFreeLogs whereGoodsItemNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewPeopleFreeLogs whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewPeopleFreeLogs whereIsFree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewPeopleFreeLogs whereNewPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewPeopleFreeLogs whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewPeopleFreeLogs wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewPeopleFreeLogs whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewPeopleFreeLogs whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewPeopleFreeLogs whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewPeopleFreeLogs whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NewPeopleFreeLogs whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class NewPeopleFreeLogs extends Model
{
    protected $table = 'new_people_free_logs';
    protected $connection = 'lx-mall';
}

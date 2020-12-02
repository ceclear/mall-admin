<?php

namespace App\Models;

use App\Errors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * App\Model\ExecQueue
 *
 * @property int $id
 * @property string $type
 * @property string $unique_id 唯一键
 * @property string|null $extends_json
 * @property int $status
 * @property string|null $msg
 * @property int $exec_sort 执行顺序，数字越大优先级越大
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExecQueue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExecQueue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExecQueue query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExecQueue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExecQueue whereExecSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExecQueue whereExtendsJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExecQueue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExecQueue whereMsg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExecQueue whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExecQueue whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExecQueue whereUniqueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExecQueue whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExecQueue extends Model
{
    use commonTrait, Errors;
    protected $connection = 'lx-mall';
    protected $table = "exec_queue";

    const STATUS_WAITING = 0;
    const STATUS_ING = 1;
    const STATUS_SUCCESS = 2;
    const STATUS_CLOSE = 3;
    const STATUS_FAIL = 4;
    const STATUS_LOCK = 5;
    const STATUS_REST = 6;

    const TYPE_USERS_BENEFIT_TOTAL_SETTLEMENT= "user_settlement";
    const TYPE_ORDER_REFUND = "order_refund";

    public static function getStatusMap($key = "ALL", $default = false)
    {
        $ret = [
            self::STATUS_WAITING => "待执行",
            self::STATUS_ING => "执行中",
            self::STATUS_SUCCESS => "执行成功",
            self::STATUS_CLOSE => "已关闭",
            self::STATUS_FAIL => "执行失败",
            self::STATUS_LOCK => "已锁定",
            self::STATUS_REST => "待重新执行"
        ];
        if ( $key === "ALL" ) {
            return $ret;
        }

        return Arr::get($ret, $key, $default);
    }

    public static function addQueue($type, $data)
    {
        $self = new self();
        $self->type = $type;
        $self->extends_json = is_array($data) ? json_encode($data) : $data;
        $self->status = self::STATUS_WAITING;
        $self->unique_id = md5(uniqid($type, true));
        $self->exec_sort = 0;
        try {
            $ret = $self->save();
        } catch ( \Exception $exception ) {
            self::Error($exception->getMessage());
            return false;
        }
        if ( $ret ) {
            return true;
        }
        self::Error("数据添加失败");
        return false;
    }

    public static function addUsersBenefitTotalSettlementQueue($ym, $dateType, $uid = 0)
    {
        return self::addQueue(self::TYPE_USERS_BENEFIT_TOTAL_SETTLEMENT, [
            "ym" => $ym,
            "dateType" => $dateType,
            "uid" => $uid
        ]);
    }

    public static function addOrderRefund($orderSn, $refundTag)
    {
        if ( !Order::getRefundTagMap($refundTag) ) {
            self::Error("不是有效的标签类型");
            return false;
        }
        return self::addQueue(self::TYPE_ORDER_REFUND, [
            "orderSn" => $orderSn,
            "refundTag" => $refundTag
        ]);
    }

}

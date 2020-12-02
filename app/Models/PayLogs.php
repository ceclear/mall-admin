<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayLogs extends Model
{
    protected $connection = 'lx-mall';
    protected $table = 'pay_logs';

    const PAY_WAIT = 0;//待支付
    const PAY_SUCCESS = 1;//支付成功
    const PAY_FAIL = 2;//支付失败
    const PAY_REFUND = 3;//退款

    static $status = [
        self::PAY_WAIT => '待支付',
        self::PAY_SUCCESS => '支付成功',
        self::PAY_FAIL => '支付失败',
        self::PAY_REFUND => '退款'
    ];
}

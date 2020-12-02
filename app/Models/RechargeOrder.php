<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RechargeOrder extends Model
{
    protected $connection = 'lx-mall';
    protected $table = 'recharge_order';

    const PAY_WAIT = 0;
    const PAY_SUCCESS = 1;
    const PAY_FAIL = 2;
    const RECHARGE_SUCCESS = 3;
    const RECHARGE_FAIL = 4;
    const PAY_REFUND = 5;

    static $status = [
        self::PAY_WAIT => '未支付',
        self::PAY_SUCCESS => '充值中',
        self::PAY_FAIL => '支付失败',
        self::RECHARGE_SUCCESS => '充值成功',
        self::RECHARGE_FAIL => '充值失败',
        self::PAY_REFUND => '资产退回'
    ];

    static $payment = [
        1 => '支付宝',
        2 => '微信'
    ];
}

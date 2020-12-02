<?php

namespace App\Models;

use App\Errors;
use Illuminate\Database\Eloquent\Model;

class UserCoupons extends Model
{
    use Errors;

    protected $connection = 'lx-mall';
    protected $table = "user_coupons";

    const STATUS_USEABLE = 0;//可使用
    const STATUS_USED = 1;

    static $status = [
        self::STATUS_USEABLE => '可使用',
        self::STATUS_USED => '已使用'
    ];

    const TYPE_PHONE_TICKET = 0;

    static $type = [
        self::TYPE_PHONE_TICKET => '话费券'
    ];
}

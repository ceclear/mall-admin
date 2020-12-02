<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorLogs extends Model
{
    protected $connection = 'lx-mall';
    protected $table = 'error_logs';

    const SKIP_TYPE = [
        1 => '跳转活动页',
        2 => '点击跳转',
        3 => 'app内跳转'
    ];

}

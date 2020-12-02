<?php

namespace App\Listeners;

use App\Events\LoginEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class LoginListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  LoginEvent  $event
     * @return void
     */
    public function handle(LoginEvent $event)
    {
        //
        //获取事件中保存的信息
        $user = $event->getUser();
        $ip = $event->getIp();
        $timestamp = $event->getTimestamp();

        //登录信息
        $login_info = [
            'last_login_ip' => $ip,
            'last_login_time' => $timestamp,
            'username' => $user->username,
            'created_at'=>date('Y-m-d H:i:s')
        ];
        //插入到数据库
        DB::table('admin_user_log')->insert($login_info);
    }
}

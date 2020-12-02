<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Encore\Admin\Auth\Database\Administrator;


class LoginEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /**
     * @var User 用户模型
     */
    protected $user;

    /**
     * @var string IP地址
     */
    protected $ip;

    /**
     * @var int 登录时间戳
     */
    protected $timestamp;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $ip, $timestamp)
    {
        //
        $this->user = $user;

        $this->ip = $ip;
        $this->timestamp = $timestamp;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}

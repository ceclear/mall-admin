<?php

namespace App\Services;

use App\Models\Goods;
use App\Models\PushList;
use App\Models\PushType;
use App\Models\UsersInfo;
use Miao\MiPush\Builder;
use Miao\MiPush\Constants;
use Miao\MiPush\IOSBuilder;
use Miao\MiPush\Sender;
use Miao\MiPush\TargetedMessage;

class PushService
{
    /**
     * 发送message
     * @param int $platform
     * @param array $data
     * @param int $uid
     */
    public static function send($platform = 0, array $data = [], $uid = 0)
    {
        if ($platform == PushList::PLANT_IOS) {
            self::sendIOSMessage($data);
        } elseif ($platform == PushList::PLANT_ANDROID) {
            self::sendAndroidMessage($data);
        } else {
            self::sendAndroidMessage($data, $uid);
            self::sendIOSMessage($data, $uid);
        }
    }

    /**
     * 发送ios message
     * @param $data
     * @param int $uid
     * @return \Miao\MiPush\Result
     */
    public static function sendIOSMessage($data, $uid = 0)
    {
        $secret = env('IOS_SECRET');
        $bundleId = env('IOS_BUNDLEID');

        Constants::setBundleId($bundleId);
        Constants::setSecret($secret);

        if (!env('PUSH_ONLINE')) {
            Constants::useSandbox();
        }

        $sender = new Sender();

        $message = self::getMessage(PushList::PLANT_IOS, $data);
        $message->build();

        if ($uid) {
            return $sender->sendToAlias($message, self::getAlias($uid));
        }
        return $sender->broadcastAll($message);
    }

    /**
     * 发送Android message
     * @param $data
     * @param int $uid
     * @return \Miao\MiPush\Result
     */
    public static function sendAndroidMessage($data, $uid = 0)
    {
        $secret = env('ANDROID_SECRET');
        $package = env('ANDROID_PACKAGE');

        Constants::setPackage($package);
        Constants::setSecret($secret);

        $sender = new Sender();
        $message = self::getMessage(PushList::PLANT_ANDROID, $data);
        $message->build();

        if ($uid) {
            return $sender->sendToAlias($message, self::getAlias($uid));
        }
        return $sender->broadcastAll($message);
    }

    /**
     * 获取message
     * @param int $platform
     * @param array $data
     * @return Builder|IOSBuilder
     */
    private static function getMessage($platform = 0, array $data = [])
    {
        if ($platform == PushList::PLANT_IOS) {
            return self::getIOSMessage($data);
        }
        return self::getAndroidMessage($data);
    }

    /**
     * 组装Android message
     * @param $data
     * @return Builder
     */
    public static function getAndroidMessage($data)
    {
        // message1 演示自定义的点击行为
        $message = new Builder();
        $message->title($data['title']);  // 通知栏的title
        $message->description($data['desc']); // 通知栏的descption
        $message->passThrough(0);  // 这是一条通知栏消息，如果需要透传，把这个参数设置成1,同时去掉title和descption两个参数
        $payload = $data['payload'] ?? [];
        $message->payload(json_encode($payload)); // 携带的数据，点击后将会通过客户端的receiver中的onReceiveMessage方法传入。
        $message->extra(Builder::notifyForeground, 1); // 应用在前台是否展示通知，如果不希望应用在前台时候弹出通知，则设置这个参数为0
        $message->notifyId(time() % 10000); // 通知类型。最多支持0-4 5个取值范围，同样的类型的通知会互相覆盖，不同类型可以在通知栏并存
        $message->build();

        return $message;
    }

    /**
     * 组装ios message
     * @param $data
     * @return IOSBuilder
     */
    public static function getIOSMessage($data)
    {
        $message = new IOSBuilder();
        $message->title($data['title']);
        $message->body($data['desc']);
        $message->soundUrl('default');

        $payload = $data['payload'] ?? [];
        $message->extra('payload', json_encode($payload));
        $message->build();

        return $message;
    }

    /**
     * 获取alias
     * @param $uid
     * @return string
     */
    private static function getAlias($uid)
    {
        $userInfo = UsersInfo::find($uid);

        return $userInfo ? $userInfo->phone : '';
    }

    /**
     * 手动推送任务
     * @param $task
     */
    public static function pushTask($task)
    {
        $subType = isset($task->type->url) && in_array($task->type->url, PushType::$goodsPush) ? $task->type->url : 'normal';

        $payload = [
            'type' => 'hand',
            'sub_type' => $subType,
            'is_index' => $task->type->is_index
        ];

        if (optional($task->type)->url && !in_array($subType, PushType::$goodsPush)) {
            $payload['url'] = $task->type->url . '?id=' . ((int)$task->data_id ?? 0);
        } else {
            $payload['url'] = '';
        }

        if (in_array($subType, PushType::$goodsPush)) {
            $payload['data_id'] = $task->data_id;
            $payload['source'] = Goods::where('gid', $task->data_id)->value('source');
            $payload['brand_id'] = $subType == 'brandflash' ? $task->brand_id : '';
        }

        self::send($task->platform, [
            'title' => $task->title,
            'desc' => $task->desc,
            'payload' => $payload
        ]);

        $task->status = PushList::STATUS_SUSSESS;
        $task->save();
    }
}

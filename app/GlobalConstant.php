<?php


namespace App;

use Illuminate\Support\Arr;

class GlobalConstant
{
    //统一数组分隔符
    const DELIMITER_CHARACTER = ",";

    const IS_YES = 1;
    const IS_NO = 0;

    const SYSTEM_PARTITION_SUPER_BK = 2;
    const SYSTEM_PARTITION_LIKE = 1;
    const SYSTEM_REAL_SALE = 3;
    const SYSTEM_GOOD_COUPON = 4;
    const SYSTEM_PARTITION_PEOPLE = 5;
    const SYSTEM_PARTITION_MS = 7;
    const SYSTEM_PARTITION_TIME_9_9 = 6;

    const SOURCE_TB = 1;
    const SOURCE_JD = 2;
    const SOURCE_PDD = 3;

    /*const SYSTEM_PARTITION_TIME_QG = 2;
    const SYSTEM_PARTITION_TIME_9_9 = 3;*/

    public static function getSystemPartitionMap($key = "ALL")
    {
        $ret = [
            self::SYSTEM_PARTITION_PEOPLE   => "超级爆款",
            self::SYSTEM_PARTITION_SUPER_BK => "人气特卖",
            self::SYSTEM_PARTITION_LIKE     => '猜你喜欢',
            self::SYSTEM_REAL_SALE          => '实时热销',
            self::SYSTEM_GOOD_COUPON        => '好券推荐',
            self::SYSTEM_PARTITION_MS       => '限时秒杀'

        ];
        if ($key === "ALL") {
            return $ret;
        }
        return Arr::get($ret, $key, false);
    }

    public static function getSourceMap($key = "ALL")
    {
        $ret = [
            self::SOURCE_TB  => "淘宝",
            self::SOURCE_JD  => "京东",
            self::SOURCE_PDD => "拼多多"
        ];
        if ($key === "ALL") {
            return $ret;
        }
        return Arr::get($ret, $key, false);
    }

    public static function getPointMap($key = "ALL")
    {
        $ret = [
            1 => "10:00",
            2 => "12:00",
            3 => "14:00",
            4 => "20:00",
            5 => "22:00",
        ];
        if ($key === "ALL") {
            return $ret;
        }
        return Arr::get($ret, $key, false);
    }

    public static function getKingMap($key = "ALL")
    {
        $ret = [
            1  => "新人免单",
            2  => "签到有奖",
            3  => "9.9包邮",
            4  => "淘宝/天猫",
            5  => "拼多多",
            6  => "京东",
            7  => "品牌闪购",
            8  => "饿了么",
            9 => "加油",
            10 => "办卡返现",
            11 => "跳转链接",

        ];
        if ($key === "ALL") {
            return $ret;
        }
        return Arr::get($ret, $key, false);
    }
}

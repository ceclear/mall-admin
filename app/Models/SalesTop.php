<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesTop extends Model
{
    protected $connection = 'lx-mall';
    protected $table = 'sales_top';

    static $status = [
        0 => '正常',
        1 => '作弊禁止',
        2 => '已发放奖励'
    ];

    static $rules = [
        [
            'top' => [1],
            'price' => 50000,
            'reward_max' => 13600,
            'reward_min' => 6800
        ],
        [
            'top' => [2],
            'price' => 30000,
            'reward_max' => 6800,
            'reward_min' => 3400
        ],
        [
            'top' => [3],
            'price' => 10000,
            'reward_max' => 3400,
            'reward_min' => 1600
        ],
        [
            'top' => [4, 5, 6, 7, 8, 9, 10],
            'price' => 8000,
            'reward_max' => 1600,
            'reward_min' => 800
        ],
        [
            'top' => [11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
            'price' => 5000,
            'reward_max' => 800,
            'reward_min' => 400
        ],
        [
            'top' => [21, 22, 23, 24, 25, 26, 27, 28, 29, 30],
            'price' => 3000,
            'reward_max' => 400,
            'reward_min' => 200
        ],
        [
            'top' => [31, 32, 33, 34, 35, 36, 37, 38, 39, 40],
            'price' => 2000,
            'reward_max' => 200,
            'reward_min' => 100
        ],
        [
            'top' => [41, 42, 43, 44, 45, 46, 47, 48, 49, 50],
            'price' => 1000,
            'reward_max' => 100,
            'reward_min' => 50
        ]
    ];

    /**
     * 奖励
     *
     * @param $top
     * @param $price
     * @return int
     */
    public static function getReward($top, $price)
    {
        $rules = self::$rules;

        foreach ($rules as $rule) {
            if (in_array($top, $rule['top'])) {
                return $price >= $rule['price'] ? $rule['reward_max'] : $rule['reward_min'];
            }
        }

        return 0;
    }
}

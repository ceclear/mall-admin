<?php

return [
    'alipay' => [
        'app_id' => env("ALIPAY_APPID"),
        'notify_url' => '',
        'return_url' => '',
        'ali_public_key' => env("ALIPAY_PUBLIC_KEY"),
        'private_key' => env("ALIPAY_PRIVATE_KEY"),
        'log' => [ // optional
            'file' => './logs/alipay.log',
            'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
            'type' => 'single', // optional, 可选 daily.
            'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
        ],
        'http' => [ // optional
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        // 'mode' => 'dev', // optional,设置此参数，将进入沙箱模式
    ],
    'wechat' => [
        'appid' => env("WX_PAY_APPID"),
        'mch_id' => env("WX_PAY_MCH_ID"),
        'key' => env("WX_PAY_KEY"),
//            'notify_url' => 'https://kuakelianxin.cn/wechat-notify',
        'cert_client' => base_path() . '/cert/apiclient_cert.pem', // optional，退款等情况时用到
        'cert_key' => base_path() . '/cert/apiclient_key.pem',// optional，退款等情况时用到
        'log' => [ // optional
            'file' => storage_path('logs') . '/wechat.log',
            'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
            'type' => 'single', // optional, 可选 daily.
            'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
        ],
        'http' => [ // optional
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'normal', // optional, dev/hk;当为 `hk` 时，为香港 gateway。
    ]
];

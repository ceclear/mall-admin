<?php

namespace App\Console\Commands;

use App\Models\Withdrawal;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Yansongda\Pay\Exceptions\BusinessException;
use Yansongda\Pay\Pay;

class Withdraw extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'withdraw:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '提现任务';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Withdrawal::whereIn('status', [Withdrawal::STATUS_JOB])->chunkById(500, function ($orders) {
            foreach ($orders as $log) {
                $order = [
                    'type' => 'app',
                    'partner_trade_no' => $log->trade_no,              //商户订单号
                    'openid' => $log->user->wx_openid,                        //收款人的openid
                    'check_name' => 'NO_CHECK',            //NO_CHECK：不校验真实姓名\FORCE_CHECK：强校验真实姓名
                    'amount' => intval($log->amount * 100),                       //企业付款金额，单位为分
                    'desc' => '链信省钱提现',                  //付款说明
                ];

                $wechat = Pay::wechat(config('pay.wechat'));

                try {
                    $result = $wechat->transfer($order);
                    $update = [
                        'status' => Withdrawal::STATUS_SUCCESS,
                        'remark' => '付款成功',
                        'out_trade_no' => $result['payment_no'],
                        'completed_at' => $result['payment_time'],
                    ];

                } catch (\Yansongda\Pay\Exceptions\BusinessException $e) {
                    if (in_array($e->raw['err_code'], ['SEND_FAILED', 'SYSTEMERROR'])) {
                        $queryResult = $wechat->find($log->trade_no, 'transfer');

                        if ('SUCCESS' === $queryResult['status']) {
                            $update = [
                                'out_trade_no' => $queryResult['detail_id'],
                                'status' => Withdrawal::STATUS_SUCCESS,
                                'completed_at' => $queryResult['payment_time'],
                                'remark' => '付款成功',
                            ];
                        } else {
                            throw $e;
                        }
                    } else {
                        // 常用错误不上报到 sentry
                        foreach (Withdrawal::$wechatAutoDeny as $code => $msg) {
                            if (strpos($e->getMessage(), $code) !== false) {
                                $e = new BusinessException($e->getMessage(), $e->getCode());
                            }
                        }

                        throw $e;
                    }
                }

                if ($update) {
                    $update['operator'] = 0;
                    $log->update($update);
                }
            }
        });
    }
}

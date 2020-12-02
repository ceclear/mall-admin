<?php

namespace App\Jobs;

use App\Models\UsersInfo;
use App\Models\Withdrawal;
use App\Services\BalanceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Yansongda\Pay\Pay;

class Withdraw implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任务可以尝试的最大次数。
     *
     * @var int
     */
    public $tries = 1;

    /**
     * 任务可以执行的最大秒数 (超时时间)。
     *
     * @var int
     */
    public $timeout = 10;

    /**
     * 提现日志对象
     * @var Withdrawal
     */
    protected $log;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Withdrawal $withdrawLog)
    {
        $this->log = $withdrawLog;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $log = $this->log;

        // 非处理中
        if ($log->status != Withdrawal::JOB) {
            throw new \Exception('状态错误');
        };

        $user = UsersInfo::find($log->uid);
        if (is_null($user)) {
            throw new \Exception('用户不存在');
        };

        // 未设置订单号
        if (!$log->trade_no) {
            throw new \Exception('sn不能为空');
        };

        if ($log->withdrawal_type == 0) {//支付宝
            $this->alipay($user, $log);
        } else {//微信
            $this->wechat($user, $log);
        }
    }

    private function alipay($user, $log)
    {
        $order = [
            'out_biz_no' => $log->trade_no,
            'trans_amount' => $log->amount,
            'product_code' => 'TRANS_ACCOUNT_NO_PWD',
            'payee_info' => [
                'identity' => $log->alipay,
                'identity_type' => 'ALIPAY_LOGON_ID',
            ],
        ];

        $alipay = Pay::alipay(config('pay.alipay'));

        $update = [];

        try {
            $result = $alipay->transfer($order);

            if (isset($result->status) && $result->status == 'SUCCESS') {
                $update = [
                    'status' => Withdrawal::SUCCESS,
                    'remark' => '付款成功',
                    'out_trade_no' => $result->order_id,
                    'completed_at' => $result->trans_date,
                ];
            }

        } catch (\Yansongda\Pay\Exceptions\BusinessException $e) {
            $result = $alipay->find([
                'out_trade_no' => $log->trade_no,
            ], 'transfer');

            if (isset($result->status) && $result->status == 'SUCCESS') {
                $update = [
                    'out_trade_no' => $result->order_id,
                    'status' => Withdrawal::SUCCESS,
                    'completed_at' => $result->trans_date,
                    'remark' => '付款成功',
                ];
            }
        }

        if ($update) {
            $log->update($update);
        }
    }

    /**
     * 微信提现
     *
     * @param $user
     * @param $log
     * @throws BusinessException
     * @throws \Yansongda\Pay\Exceptions\BusinessException
     */
    private function wechat($user, $log)
    {
        $order = [
            'type' => 'app',
            'partner_trade_no' => $log->trade_no,              //商户订单号
            'openid' => $user->wx_openid,                        //收款人的openid
            'check_name' => 'NO_CHECK',            //NO_CHECK：不校验真实姓名\FORCE_CHECK：强校验真实姓名
            //'re_user_name' => $log->real_name,
            'amount' => intval($log->amount * 100),                       //企业付款金额，单位为分
            'desc' => '链信省钱提现',                  //付款说明
            'spbill_create_ip' => '47.99.199.241', // 队列服务器IP
        ];

        $wechat = Pay::wechat(config('pay.wechat'));

        try {
            $result = $wechat->transfer($order);
            $update = [
                'status' => Withdrawal::SUCCESS,
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
                        'status' => Withdrawal::SUCCESS,
                        'completed_at' => $queryResult['payment_time'],
                        'remark' => '付款成功',
                    ];
                } else {
                    throw $e;
                }
            } else {
                foreach (Withdrawal::$wechatAutoDeny as $code => $msg) {
                    if (strpos($e->getMessage(), $code) !== false) {
                        $e = new \Exception($msg);
                    }
                }
                throw $e;
            }
        }

        if ($update) {
            $log->update($update);
        }
    }

    /**
     * @param \Exception $exception
     *
     * @throws \Exception
     */
    public function failed(\Exception $exception)
    {
        DB::beginTransaction();
        try {
            $update = [
                'status' => Withdrawal::REFUSE,
            ];

            foreach (Withdrawal::$wechatAutoDeny as $code => $msg) {
                if (strpos($exception->getMessage(), $code) !== false) {
                    $update['remark'] = $msg;
                }
            }

            $this->log->update($update);

            BalanceService::change($this->log->uid, $this->log->amount, '提现失败返回');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            throw new \Exception('请等待或联系客服');
        }
    }
}

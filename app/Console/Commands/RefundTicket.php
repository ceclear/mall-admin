<?php

namespace App\Console\Commands;

use App\Models\PayLogs;
use App\Models\RechargeOrder;
use App\Models\UserCoupons;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Yansongda\Pay\Pay;

class RefundTicket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refund:ticket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '退回话费券';

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
        RechargeOrder::whereIn('status', [
            RechargeOrder::PAY_WAIT])->where('use_ticket', 1)
            ->where('created_at', '<=', date('Y-m-d H:i:s', time() - 3600))->chunkById(500, function ($orders) {
                $wechat = Pay::wechat(config('pay.wechat'));
                foreach ($orders as $order) {
                    $wxOrder = [
                        'type' => 'app',
                        'out_trade_no' => $order->order_sn,
                    ];

                    try {
                        $result = $wechat->find($wxOrder);

                        if (!in_array($result->trade_state, ['NOTPAY', 'CLOSED', 'PAYERROR'])) {
                            continue;
                        }
                        $this->rerfund($order);
                    } catch (\Exception $e) {
                        $this->rerfund($order);
                    }
                }
            });
    }

    /**
     * 退回话费券
     *
     * @param $order
     * @return bool
     */
    private function rerfund($order)
    {
        DB::beginTransaction();
        try {
            $order->status = RechargeOrder::PAY_FAIL;
            $order->save();

            $payLog = PayLogs::where('order_sn', $order->order_sn)->first();
            if ($payLog) {
                $payLog->status = PayLogs::PAY_FAIL;
                $payLog->save();
            }

            //退回优惠券
            if ($order->tickets) {
                $coupons = json_decode($order->tickets, true);
                UserCoupons::whereIn('id', $coupons)->update([
                    'use_time' => null,
                    'status' => UserCoupons::STATUS_USEABLE
                ]);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
}

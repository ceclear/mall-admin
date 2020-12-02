<?php

namespace App\Console\Commands;

use App\Models\UsersInfo;
use App\Models\Withdrawal;
use App\Services\BalanceService;
use Illuminate\Console\Command;

class refuseWithdrawal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refuse:withdrawal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '拒绝所有待处理提现';

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
        Withdrawal::whereIn('status', [Withdrawal::STATUS_PENDING, Withdrawal::STATUS_JOB])->chunkById(500, function ($withdrawals) {
            foreach ($withdrawals as $withdrawal) {
                BalanceService::change($withdrawal->uid, $withdrawal->amount, '拒绝提现');
                $withdrawal->status = Withdrawal::STATUS_REFUSE;
                $withdrawal->remark = '拒绝提现';
                $withdrawal->save();
            }
        });
    }
}

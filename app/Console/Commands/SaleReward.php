<?php

namespace App\Console\Commands;

use App\Models\SalesTop;
use App\Models\UsersInfo;
use App\Services\BalanceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Yansongda\Pay\Exceptions\BusinessException;
use Yansongda\Pay\Pay;

class SaleReward extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sale:reward {type?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '销售榜奖励';

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
        $type = $this->argument('type');

        switch ($type) {
            case 'show':
                $this->show();
                break;
            default:
                $this->reward();
                break;
        }
    }

    /**
     * 奖励
     *
     * @throws \Exception
     */
    public function reward()
    {
        $tops = SalesTop::orderBy('sale_price', 'desc')->limit(50)->get();

        foreach ($tops as $key => $top) {
            if (in_array($top->uid, ['top1', 'top2', 'top3'])) {
                continue;
            }

            if ($top->status > 0) {
                continue;
            }

            BalanceService::change((int)$top->uid, SalesTop::getReward($key + 1, $top->sale_price), '销售榜第' . ($key + 1) . '名奖励');

            $top->status = 2;
            $top->save();
        }
    }

    public function show()
    {
        $saleRank = Redis::zrevrange("sale_list_top", 0, -1, "WITHSCORES");
        dump($saleRank);
    }
}

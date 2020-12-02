<?php

namespace App\Console\Commands;

use App\Models\MsGoods;
use App\Models\NineGoods;
use App\Services\DingDanXia;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class syncTaoBaoMs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync-goods-ms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '淘宝客秒杀';

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
        //
        $hour_point = MsGoods::MS_HOUR_TYPE;
        $model      = MsGoods::query();
        $model->delete();
        $ddx = new DingDanXia();
        foreach ($hour_point as $item) {
            $num = 0;
            $rel = $ddx->getSecKill($item);
            foreach ($rel as $value) {
                foreach ($value as $val) {
                    $val->is_auto=2;
                    $val->save();
                    $num++;
                }

            }
            $this->info('数据抓取成功--------' . $item . '------' . $num);
        }
    }
}

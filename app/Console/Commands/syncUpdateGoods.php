<?php

namespace App\Console\Commands;

use App\GlobalConstant;
use App\Models\Goods;
use App\Services\DingDanXia;
use Carbon\Carbon;
use Illuminate\Console\Command;


class syncUpdateGoods extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync-goods-com';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新商品佣金';

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
        $start_time = Carbon::parse('3 days ago')->toDateString() . ' 00:00:00';
        $end_time   = Carbon::now()->toDateString() . ' 23:59:59';
        $sourceList = [Goods::SOURCE_PDD, Goods::SOURCE_JD, Goods::SOURCE_TB];
        $field      = ['id', 'gid', 'source', 'reserve_price', 'zk_final_price', 'qh_final_price', 'qh_final_commission', 'volume', 'coupon_amount', 'coupon_start_time', 'coupon_end_time', 'pop_url'];
        $ddx        = new DingDanXia();
        $count      = Goods::where('updated_at', '>=', $start_time)->where('updated_at', '<=', $end_time)->select($field)->count();
        foreach ($sourceList as $item) {
            $num = 0;
            Goods::where('source', $item)->where('updated_at', '>=', $start_time)->where('updated_at', '<=', $end_time)->select($field)->chunkById(200, function ($data) use ($ddx, $item, &$num) {
                $num++;
                $list  = $ddx->updateGidInfo(reset($data), $item);
                $total = 0;
                if ($list) {
                    foreach ($list as $key => $value) {
                        unset($value['pop_url']);
                        if (!$value->isDirty('qh_final_commission') && !$value->isDirty('coupon_amount') && !$value->isDirty('coupon_end_time')) {
                            unset($list[$key]);
                        } else {
                            $total++;
                            $value->save();
                        }
                    }
                }
                $this->alert('=======' . Goods::getSourceMap($item) . '==第' . $num . '批数据更新成功======小计' . $total . '条');
            });
        }
        $this->info("更新数据完成,已更新数据" . $count . '条');

    }
}

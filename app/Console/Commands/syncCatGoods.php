<?php

namespace App\Console\Commands;

use App\GlobalConstant;
use App\Models\Goods;
use App\Models\GoodsCat;
use App\Models\NineGoods;
use App\Services\DingDanXia;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class syncCatGoods extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync-cat-goods';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '通过本地库的分类抓取商品';

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
        $cat        = GoodsCat::where('status', 1)->where('is_grab', 1)->get(['id', 'pid', 'name'])->toArray();
        $sourceList = [Goods::SOURCE_PDD, Goods::SOURCE_JD, Goods::SOURCE_TB];
        $ddx        = new DingDanXia();
        foreach ($sourceList as $value) {
            foreach ($cat as $item) {
                $list = $ddx->consoleCatGoods($value, $item['name']);
                if ($list) {
                    foreach ($list as $val) {
                        $val->cat_id_one = $item['id'];
                        $val->cat_id_two = $item['pid'];
                        if (!$val->exists) {
                            $val->sort_time = time();
                            $val->is_auto   = 2;
                        }
                        $val->source = $value;
                        if ($val->qh_final_price > 0) {
                            $this->info("商品" . $val->gid . '成功');
                            $val->save();
                        } else {
                            continue;
                        }

                    }
                } else {
                    $this->alert($item['name'] . '没有数据');
                }
                continue;
            }
        }

    }
}

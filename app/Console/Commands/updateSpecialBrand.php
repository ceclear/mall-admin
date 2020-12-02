<?php

namespace App\Console\Commands;

use App\Models\Circle;
use App\Models\CircleAreas;
use App\Models\CircleArticles;
use App\Models\Goods;
use App\Models\SpecialBrand;
use Carbon\Carbon;
use Illuminate\Console\Command;

class updateSpecialBrand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:special-brand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新品牌闪购信息';

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
        SpecialBrand::where('status', 1)->chunkById(100, function ($brands) {
            foreach ($brands as $brand) {
                $goods = explode(',', $brand->goods_id);

                $g = [];
                foreach ($goods as $good) {
                    $gInfo = Goods::where('gid', $good)->first();
                    if ($gInfo && strtotime($gInfo->coupon_end_time) > time()) {
                        $g[] = $good;
                    }
                }

                if (!$g) {
                    $brand->status = 2;
                }
                $brand->goods_id = implode(',', $g);
                $brand->save();
            }
        });
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Circle;
use App\Models\CircleAreas;
use App\Models\CircleArticles;
use App\Models\Goods;
use Carbon\Carbon;
use Illuminate\Console\Command;

class updateCircleSelect extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:circle-select';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新优选圈子状态';

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
        CircleArticles::where('type', CircleAreas::PLATE_SELECT)->where('recommend_type', 1)->where('status', Circle::STATUS_PUSH)->chunkById(200, function ($articles) {
            foreach ($articles as $article) {
                $goods = Goods::where('gid', $article->recommend_good)->first();
                if (strtotime($goods->coupon_end_time) <= time()) {
                    $article->status = Circle::STATUS_SAVE;
                    $article->save();
                }
            }
        });
    }
}

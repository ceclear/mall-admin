<?php

namespace App\Console\Commands;

use App\GlobalConstant;
use App\Models\Goods;
use App\Models\GoodsPreview;
use App\Services\DingDanXia;
use Carbon\Carbon;
use Illuminate\Console\Command;


class publishGoods extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'publish-goods';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '采集并发布预发布商品';

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
        $date = Carbon::now()->toDateString();
        $ddx  = new DingDanXia();
        GoodsPreview::where('pre_date', '<', $date)->where('success', 0)->delete();
        GoodsPreview::wherePreDate($date)->where('success', 0)->chunkById(200, function ($list) use ($ddx, $date) {
            foreach ($list as $item) {
                $special = $item['is_special'] == 1 ? true : false;
                try {
                    $rel = $ddx->getGoodsByGid([$item['gid']], $item['source'], $special);
                    if ($rel) {
                        foreach ($rel as $val) {
                            $val->cat_id_one = $item['cat_id_one'] ?? 0;
                            $val->cat_id_two = $item['cat_id_two'] ?? 0;
                            $val->partition  = $item['partition'] ?? 0;
                            $val->is_auto    = 1;
                            $val->sort_time  = time();
                            $val->source     = $item['source'];
                            switch ($item['source']) {
                                case Goods::SOURCE_TB:
                                    $val->detail_images = $ddx->getTbDetailImages($item['gid']);
                                    break;
                                case Goods::SOURCE_JD:
                                    $val->detail_images = $ddx->getJdDetailImages($item['gid']);
                                    break;
                                default:
                                    $images             = $ddx->getPddDetailImages($item['gid']);
                                    $val->detail_images = $images;
                                    $val->images_url    = json_encode($images);
                                    break;
                            }
                            unset($val['activity_id']);
                            if ($val->save()) {
                                $item['success'] = 1;
                                $item['remark']  = $date . '成功';
                            } else {
                                $item['success'] = 2;
                                $item['remark']  = $date . '商品保存失败' . substr($ddx->getFirstError(), 0, 40);
                            }
                            $this->info("商品==" . $val->gid . '==完成==' . $date);

                        }
                    } else {
                        $item['success'] = 2;
                        $item['remark']  = '失败,原因:' . $ddx->getFirstError();
                        $this->info("商品" . $item['gid'] . '返回信息' . substr($ddx->getFirstError(), 0, 40) . $date);
                    }
                    $item->save();
                } catch (\Exception $exception) {
                    $this->info("商品---" . $item['gid'] . '出错' . $date . $exception->getMessage());
                    continue;
                }

            }
        });

    }
}

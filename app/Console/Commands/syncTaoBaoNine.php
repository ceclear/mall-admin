<?php

namespace App\Console\Commands;

use App\Models\NineGoods;
use App\Services\DingDanXia;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class syncTaoBaoNine extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync-goods-nine';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '抓取超省购商品';

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
        $ddx = new DingDanXia();
        if (!$cat = Cache::get('tbk_cat')) {
            $cat = $ddx->getTaoBaoKeCat();
        }
        foreach ($cat as $key=>$item) {
            $rel = $ddx->getNineGoods(NineGoods::EVERY_NUM, $item['cid']);
            if ($rel) {
                foreach ($rel as $value) {
                    $rs = $ddx->getTbDetailImages($value['gid']);
                    if ($rs) {
                        $value->detail_images = $rs;
                    }
                    if(!$value->exists){
                        $value->is_auto=2;
                    }
                    $value->save();
                }
                $this->alert($item['name'].'抓取成功');
            }else{
                $this->error('分类'.$item['name'].'没有抓到数据');
            }
        }
        $this->info('抓取99超省购完成');
    }
}

<?php

namespace App\Console\Commands;

use App\Services\DingDanXia;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class syncTaoBaoKeCat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync-tao-bao-ke-cat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '抓取成功淘宝客分类';

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
//        if (Cache::get('tbk_cat')){
//            $this->info('已经存在淘宝分类');
//            return;
//        }
        $ddx = new DingDanXia();
        $rel = $ddx->getTaoBaoKeCat();
        if ($rel) {
            $this->info('抓取成功淘宝客分类成功');
            return;
        }
        $this->error('抓取淘宝客分类失败');
    }
}

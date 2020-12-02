<?php

namespace App\Console\Commands;

use App\Models\CircleArticles;
use Carbon\Carbon;
use Illuminate\Console\Command;

class updateCircleArticle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:circle-article {type?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新好省圈内容信息';

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
            case 'release'://发布抓女童
                $this->updateRelease();
                break;
            case 'top'://置顶状态
                $this->updateTop();
                break;
            default:
                $this->updateRelease();
                break;
        }
    }

    /**
     * 更新发布状态
     */
    private function updateRelease()
    {
        $this->info(Carbon::now() . " 开始更新发布状态");

        CircleArticles::where('status', CircleArticles::STATUS_SAVE)
            ->where('release_time', '<=', Carbon::now())
            ->update([
                'status' => CircleArticles::STATUS_PUSH
            ]);

        $this->info(Carbon::now() . " 结束更新发布状态");
    }

    private function updateTop() {
        $this->info(Carbon::now() . " 开始更新置顶状态");

        //取消置顶
        CircleArticles::where('status', CircleArticles::STATUS_PUSH)
            ->whereRaw('now() >= date_add(release_time, INTERVAL top_day day)')
            ->update([
                'is_top' => 0
            ]);

        $this->info(Carbon::now() . " 结束更新置顶状态");
    }
}

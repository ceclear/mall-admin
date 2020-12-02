<?php

namespace App\Console\Commands;

use App\Models\PushList;
use App\Models\PushType;
use App\Services\PushService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class PushTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push-task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '消息定时推送';

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
        PushList::where('status', PushList::STATUS_PUSH)->where('push_time', '<=', Carbon::now())->chunkById(500,
            function ($tasks) {
                foreach ($tasks as $task) {
                    PushService::pushTask($task);
                }
            });
    }
}

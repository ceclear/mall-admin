<?php

namespace App\Console\Commands;

use App\Models\UsersInfo;
use Illuminate\Console\Command;

class delOpenId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'del:openid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '删除用户OPENID';

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
        UsersInfo::whereNotNull('wx_openid')->update(['wx_openid' => null]);
    }
}

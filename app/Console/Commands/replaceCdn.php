<?php

namespace App\Console\Commands;


use App\Models\Advert;
use App\Models\CircleAccounts;
use App\Models\CircleArticleInfo;
use App\Models\CircleArticles;
use App\Models\GoodsCat;
use App\Models\Rebate;
use App\Models\SpecialBrand;
use App\Models\UsersInfo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;


class replaceCdn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'replace-cdn';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更改cdn';

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
        DB::beginTransaction();
        try{
            $replace = "https://lxshengqian1.oss-cn-hangzhou.aliyuncs.com";
            $new     = "https://sq-oss.kuakelianxin.com";
            SpecialBrand::select(['id', 'logo'])->chunkById(200, function ($special) use ($replace, $new) {
                foreach ($special as $item) {
                    if (strpos($item['logo'], $replace) !== false) {
                        $item['logo'] = str_replace($replace, $new, $item['logo']);
                        $item->save();
                    }
                }
            });
            $this->alert("special_brand替换cdn成功");
            GoodsCat::select(['id', 'icon'])->chunkById(200, function ($special) use ($replace, $new) {
                foreach ($special as $item) {
                    if (strpos($item['icon'], $replace) !== false) {
                        $item['icon'] = str_replace($replace, $new, $item['icon']);
                        $item->save();
                    }
                }
            });
            $this->alert("GoodsCat替换cdn成功");
            Advert::select(['id', 'img_url'])->chunkById(200, function ($special) use ($replace, $new) {
                foreach ($special as $item) {
                    if (strpos($item['img_url'], $replace) !== false) {
                        $item['img_url'] = str_replace($replace, $new, $item['img_url']);
                        $item->save();
                    }
                }
            });
            $this->alert("Advert替换cdn成功");
            CircleAccounts::select(['id', 'avatar'])->chunkById(200, function ($special) use ($replace, $new) {
                foreach ($special as $item) {
                    if (strpos($item['avatar'], $replace) !== false) {
                        $item['avatar'] = str_replace($replace, $new, $item['avatar']);
                        $item->save();
                    }
                }
            });
            $this->alert("CircleAccounts替换cdn成功");
            CircleArticleInfo::select(['id', 'material'])->chunkById(200, function ($special) use ($replace, $new) {
                foreach ($special as $item) {
                    $arr = [];
                    if($item['material']){
                        foreach ($item['material'] as $value) {
                            if (strpos($value, $replace) !== false) {
                                $arr[] = str_replace($replace, $new, $value);
                            }else{
                                $arr[]=$value;
                            }
                        }
                        $item['material'] = $arr;
                        $item->save();
                    }

                }
            });
            $this->alert("CircleArticleInfo替换cdn成功");
            CircleArticles::select(['id', 'avatar'])->chunkById(200, function ($special) use ($replace, $new) {
                foreach ($special as $item) {
                    if (strpos($item['avatar'], $replace) !== false) {
                        $item['avatar'] = str_replace($replace, $new, $item['avatar']);
                        $item->save();
                    }
                }
            });
            $this->alert("CircleArticles替换cdn成功");
            Rebate::select(['id', 'icon'])->chunkById(200, function ($special) use ($replace, $new) {
                foreach ($special as $item) {
                    if (strpos($item['icon'], $replace) !== false) {
                        $item['icon'] = str_replace($replace, $new, $item['icon']);
                        $item->save();
                    }
                }
            });
            $this->alert("Rebate替换cdn成功");
            UsersInfo::select(['id', 'avatar'])->chunkById(200, function ($special) use ($replace, $new) {
                foreach ($special as $item) {
                    if (strpos($item['avatar'], $replace) !== false) {
                        $item['avatar'] = str_replace($replace, $new, $item['avatar']);
                        $item->save();
                    }
                }
            });
            $this->alert("UsersInfo替换cdn成功");
            DB::commit();
            return;
        }catch (\Exception $exception){
            $this->error("错误信息".$exception->getMessage());
            DB::rollBack();
            return;
        }
    }

}

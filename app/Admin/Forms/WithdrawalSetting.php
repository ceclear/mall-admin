<?php

namespace App\Admin\Forms;

use App\Models\Settings;
use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WithdrawalSetting extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '提现设置';

    /**
     * Handle the form request.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request)
    {
        $set = json_encode($request->all());

        try {
            $setting = Settings::where('key', 'withdrawal_set')->first();
            if (!$setting) {
                $setting = new Settings();
                $setting->key = 'withdrawal_set';
            }

            $setting->value = $set;
            $setting->key_desc = '提现设置';
            $setting->save();

            Cache::put('withdrawal_set', $set);

            admin_success('保存成功');
        } catch (\Exception $e) {
            admin_error("保存失败");
        }
        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        //$this->number('day', '每月提现日期')->width(200)->max(31)->min(1)->required();
        $this->number('exempt_limit', '免审提现金额')->width(200)->min(0)->required();
        $this->number('min_limit', '最低提现金额')->width(200)->min(0)->required();
    }

    /**
     * The data of the form.
     *
     * @return array $data
     */
    public function data()
    {
        $setting = Settings::where('key', 'withdrawal_set')->first();

        if ($setting) {
            return json_decode($setting->value, true);
        }

        return [
            //'day' => 25,
            'exempt_limit' => 0,
            'min_limit' => 0,
        ];
    }
}

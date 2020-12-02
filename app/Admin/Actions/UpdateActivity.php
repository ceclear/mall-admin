<?php

namespace App\Admin\Actions;


use App\Services\BalanceService;
use Encore\Admin\Actions\RowAction;

use Encore\Admin\Facades\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use function Sodium\compare;

class UpdateActivity extends RowAction
{
    public $name = '修改抽奖';
    protected $selector = '.update-balance-se';

    public function handle(Model $model, Request $request)
    {
        $amount       = $request->input('prize_num_total');
        $enableAmount = $request->input('prize_num');
        if ($amount != $enableAmount) {
            return $this->response()->error('两次数值不同')->refresh();
        }

        $model->prize_num_total = $amount;
        $model->prize_num       = $enableAmount;
        $model->save();
        return $this->response()->success('操作成功')->refresh();


    }

    public function form()
    {
        $this->text('prize_num_total', '总获得抽奖数')->placeholder('总获得抽奖数')->attribute(['autocomplete' => 'off']);
        $this->text('prize_num', '可用抽奖数')->placeholder('可用抽奖数')->attribute(['autocomplete' => 'off']);
        $token = csrf_token();
        $this->hidden('csrf_token', '')->default($token);
    }


}
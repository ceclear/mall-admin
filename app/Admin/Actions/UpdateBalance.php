<?php

namespace App\Admin\Actions;


use App\Services\BalanceService;
use Encore\Admin\Actions\RowAction;

use Encore\Admin\Facades\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UpdateBalance extends RowAction
{
    public $name = '修改余额';
    protected $selector = '.update-balance-se';

    public function handle(Model $model, Request $request)
    {
        if(!Admin::user()->can('财务')){
            return $this->response()->error('你当前没有操作权限！');
        }
        // $model ...
        $money     = $request->input('balance');
        $max_money = env('MAX_UPDATE_BALANCE');
        $key       = 'update_balance_' . $model->id;
        if (Cache::get($key)) {
            return $this->response()->error('当前操作限制1分钟内不能提交！');
        }
        if (!is_numeric($money) || floatval($money) == 0) {
            return $this->response()->error('金额不符合规范或者不能为0');
        }
        if ($money > $max_money) {
            return $this->response()->error('增加金额不能超过' . $max_money);
        }
        $pass = $request->input('password');
        if (empty($pass) || strcmp(env('UPDATE_BALANCE_PASS'), $pass) != 0) {
            return $this->response()->error('密码错误')->refresh();
        }
        $remark = $request->input('remark');
        if (empty($remark) || mb_strlen($remark) > 20) {
            return $this->response()->error('备注不能为空或超过20个字符')->refresh();
        }
        Cache::set($key, $money, 60);
        $service    = new BalanceService();
        $username   = Auth::guard('admin')->user()->username;
        $new_remark = $username . '在' . date('Y-m-d H:i:s') . '为用户' . $model->id . '增加了金额' . $money;
        if ($remark) {
            $new_remark .=  ',并备注' . $remark;
        }
        $rel = $service->change($model->id, $money,$remark,false, $new_remark);
        if ($rel) {
            return $this->response()->success('操作成功')->refresh();
        }
        return $this->response()->error('操作失败')->refresh();

    }

    public function form()
    {
        $this->text('balance', '增加金额')->placeholder('增加金额')->attribute(['autocomplete' => 'off']);
        $this->password('password', '提交密码')->placeholder('提交密码');
        $this->text('remark', '备注')->placeholder('备注：比如什么活动,限制20字以内');
        $token = csrf_token();
        Cache::set('update_balance_token', $token);
        $this->hidden('csrf_token', '')->default($token);
    }


}
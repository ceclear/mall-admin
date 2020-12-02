<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\WithdrawalAlert;
use App\Admin\Extensions\WithdrawalExporter;
use App\Admin\Forms\WithdrawalSetting;
use App\Models\UserCards;
use App\Models\UsersInfo;
use App\Models\Withdrawal;
use App\Services\BalanceService;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Box;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Yansongda\Pay\Exceptions\BusinessException;
use Yansongda\Pay\Pay;

class WithdrawalLogController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '提现记录';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Withdrawal());

        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->like('trade_no', __('订单号'));
            $filter->equal('uid', __('用户ID'));
            $filter->equal('status', '状态')->select(Withdrawal::$status);
            $filter->between('created_at', '创建时间')->datetime();
            $filter->between('updated_at', '更新时间')->datetime();
        });

        $grid->model()->latest();

        $grid->column('id', __('Id'))->sortable();
        $grid->column('trade_no', __('交易订单号'));
        $grid->column('out_trade_no', __('外部交易订单号'));
        $grid->column('uid', __('用户昵称(UID)'))->display(function ($uid) {
            return UsersInfo::where('id', $uid)->value('nickname') . "（<a href='/admin/users/show-logs?uid={$uid}'>{$uid}</a>）";
        });
        $grid->column('real_name', __('实名姓名'));
        $grid->column('id_card', __('身份证号码'));
        $grid->column('mobile', __('联系电话'));
        $grid->column('amount', __('提现数量'));
        $grid->column('withdrawal_type', __('提现平台'))->display(function ($withdrawal_type) {
            return $withdrawal_type == 1 ? '微信' : '支付宝';
        });;
        $grid->column('alipay', __('支付宝账号'));
        $grid->column('wx_openid', __('微信OPENID'));
        $grid->column('name', __('类型'));
        $grid->column('status', __('状态'))->display(function ($status) {
            return Withdrawal::$status[$status];
        });
        $grid->column('remark', __('备注'));
        $grid->column('manage.name', __('操作人'));
        $grid->column('created_at', __('创建时间'));
        $grid->column('updated_at', __('修改时间'));

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();

            $actions->append(new WithdrawalAlert($actions->getKey(), $actions->row->trade_no, $actions->row->status));
        });

        $grid->exporter(new WithdrawalExporter());


        $grid->header(function ($query) {
            $all = (clone $query)->sum('amount');
            $success = (clone $query)->where('status', Withdrawal::STATUS_SUCCESS)->sum('amount');
            $pendding = (clone $query)->where('status', Withdrawal::STATUS_PENDING)->sum('amount');
            $fail = (clone $query)->whereIn('status', [Withdrawal::STATUS_FAILED, Withdrawal::STATUS_REFUSE])->sum('amount');
            $job = (clone $query)->where('status', Withdrawal::STATUS_JOB)->sum('amount');
            return '<lable style="color: #00a0e9">总金额：' . $all . '</lable>&nbsp;&nbsp;<lable style="color: #555299">待处理金额：' . $pendding . '</lable>&nbsp;&nbsp;<lable style="color: #00a65a">提现成功金额：' . $success . '</lable>&nbsp;&nbsp;<lable style="color: #9f191f">提现失败金额：' . $fail . '</lable>&nbsp;&nbsp;<lable style="color: #00a65a">待打款金额：' . $job . '</lable>';
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Withdrawal::findOrFail($id));


        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Withdrawal());


        return $form;
    }

    /**
     * 处理提现
     * @param Request $request
     * @return Response
     */
    public function change(Request $request)
    {
        $status = $request->get('status');
        $id = $request->get('id');
        $remark = $request->get('remark', '');

        if (Cache::has('withdrawal_change_' . $id)) {
            return Response::create(json_encode([
                'code' => 400,
                'msg' => '30秒内仅能处理一次'
            ]), 200);
        }

        Cache::put('withdrawal_change_' . $id, 1, 30);

        try {
            $withdrawal = Withdrawal::find($id);

            if ($status == 1) {//同意提现
                $users = UsersInfo::find($withdrawal->uid);

                try {
                    if ($withdrawal->withdrawal_type == 0) {//支付宝
                        $this->alipay($users, $withdrawal);
                    } else {//微信
                        $this->wechat($users, $withdrawal);
                    }
                } catch (\Exception $e) {
                    return Response::create(json_encode([
                        'code' => 1,
                        'msg' => $e->getMessage()
                    ]), 200);
                }

                return Response::create(json_encode([
                    'code' => 0,
                    'msg' => 'success'
                ]), 200);
            } elseif ($status == 2) {
                BalanceService::change($withdrawal->uid, $withdrawal->amount, '提现失败');
            } elseif ($status == 3) {
                BalanceService::change($withdrawal->uid, $withdrawal->amount, '拒绝提现');
            }

            $withdrawal->status = $status;
            $withdrawal->remark = $remark;
            $withdrawal->operator = \Encore\Admin\Facades\Admin::user()->id;
            $withdrawal->save();

            return Response::create(json_encode([
                'code' => 0,
                'msg' => 'success'
            ]), 200);
        } catch (\Exception $e) {
            return Response::create(json_encode([
                'code' => 1,
                'msg' => $e->getMessage()
            ]), 200);
        }
    }

    private function alipay($user, $log)
    {
        $order = [
            'out_biz_no' => $log->trade_no,
            'trans_amount' => $log->amount,
            'product_code' => 'TRANS_ACCOUNT_NO_PWD',
            'payee_info' => [
                'identity' => $log->alipay,
                'identity_type' => 'ALIPAY_LOGON_ID',
            ],
        ];

        $alipay = Pay::alipay(config('pay.alipay'));

        $update = [];

        try {
            $result = $alipay->transfer($order);

            if (isset($result->status) && $result->status == 'SUCCESS') {
                $update = [
                    'status' => Withdrawal::STATUS_SUCCESS,
                    'remark' => '付款成功',
                    'out_trade_no' => $result->order_id,
                    'completed_at' => $result->trans_date,
                ];
            }

        } catch (BusinessException $e) {
            $result = $alipay->find([
                'out_trade_no' => $log->trade_no,
            ], 'transfer');

            if (isset($result->status) && $result->status == 'SUCCESS') {

                $update = [
                    'out_trade_no' => $result->order_id,
                    'status' => Withdrawal::STATUS_SUCCESS,
                    'completed_at' => $result->trans_date,
                    'remark' => '付款成功',
                ];
            }
        }

        if ($update) {
            $update['operator'] = \Encore\Admin\Facades\Admin::user()->id;
            $log->update($update);
        }
    }

    /**
     * 微信提现
     *
     * @param $user
     * @param $log
     * @throws BusinessException
     * @throws \Yansongda\Pay\Exceptions\BusinessException
     */
    private function wechat($user, $log)
    {
        $order = [
            'type' => 'app',
            'partner_trade_no' => $log->trade_no,              //商户订单号
            'openid' => $user->wx_openid,                        //收款人的openid
            'check_name' => 'NO_CHECK',            //NO_CHECK：不校验真实姓名\FORCE_CHECK：强校验真实姓名
            'amount' => intval($log->amount * 100),                       //企业付款金额，单位为分
            'desc' => '链信省钱提现',                  //付款说明
        ];

        $wechat = Pay::wechat(config('pay.wechat'));

        try {
            $result = $wechat->transfer($order);
            $update = [
                'status' => Withdrawal::STATUS_SUCCESS,
                'remark' => '付款成功',
                'out_trade_no' => $result['payment_no'],
                'completed_at' => $result['payment_time'],
            ];

        } catch (\Yansongda\Pay\Exceptions\BusinessException $e) {
            if (in_array($e->raw['err_code'], ['SEND_FAILED', 'SYSTEMERROR'])) {
                $queryResult = $wechat->find($log->trade_no, 'transfer');

                if ('SUCCESS' === $queryResult['status']) {
                    $update = [
                        'out_trade_no' => $queryResult['detail_id'],
                        'status' => Withdrawal::STATUS_SUCCESS,
                        'completed_at' => $queryResult['payment_time'],
                        'remark' => '付款成功',
                    ];
                } else {
                    throw $e;
                }
            } else {
                // 常用错误不上报到 sentry
                foreach (Withdrawal::$wechatAutoDeny as $code => $msg) {
                    if (strpos($e->getMessage(), $code) !== false) {
                        $e = new BusinessException($e->getMessage(), $e->getCode());
                    }
                }

                throw $e;
            }
        }

        if ($update) {
            $update['operator'] = \Encore\Admin\Facades\Admin::user()->id;
            $log->update($update);
        }
    }

    public function setting(Content $content)
    {
        $content->row(new WithdrawalSetting());

        // 如果有从后端返回的数据，那么从session中取出，显示在表单下面
        if ($result = session('result')) {
            $content->row('<pre>' . json_encode($result) . '</pre>');
        }

        return $content;
    }
}

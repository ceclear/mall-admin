<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\RechargeExporter;
use App\Models\PayLogs;
use App\Models\RechargeOrder;
use App\Models\UserCoupons;
use App\Services\RechargeService;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Yansongda\Pay\Pay;

class RechargeOrderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '话费充值列表';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new RechargeOrder());

        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->like('order_sn', __('订单号'));
            $filter->equal('phone', __('手机号'));
            $filter->equal('uid', __('UID'));
            $filter->equal('use_ticket', __("是否使用优惠券"))->select([0 => '未使用', 1 => '已使用']);
            $filter->equal('status', __("充值状态"))->select(RechargeOrder::$status);
        });

        $grid->model()->latest();

        $grid->column('id', __('ID'));
        $grid->column('uid', __('用户ID'));
        $grid->column('order_sn', __('订单编号'));
        $grid->column('phone', __('手机号'));
        $grid->column('amount', __('充值面额'));
        $grid->column('use_ticket', __('是否使用话费券'))->display(function ($useTicket) {
            return $useTicket ? '是' : '否';
        });
        $grid->column('ticket_amount', __('话费券金额'));
        $grid->column('pay_amount', __('实付金额'));
        $grid->column('payment', __('支付方式'))->display(function ($payment) {
            return RechargeOrder::$payment[$payment];
        });
        $grid->column('status', __('状态'))->display(function ($status) {
            return RechargeOrder::$status[$status];
        });
        $grid->column('reason', __('Reason'));
        $grid->column('created_at', __('创建时间'));
        $grid->column('updated_at', __('更新时间'));

        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();

            $actions->append(new \App\Admin\Extensions\RechargeOrder($actions->getKey(), $actions->row->order_sn, $actions->row->status));
        });

        $grid->exporter(new RechargeExporter());

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
        $show = new Show(RechargeOrder::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('uid', __('Uid'));
        $show->field('order_sn', __('Order sn'));
        $show->field('phone', __('Phone'));
        $show->field('amount', __('Amount'));
        $show->field('use_ticket', __('Use ticket'));
        $show->field('ticket_amount', __('Ticket amount'));
        $show->field('pay_amount', __('Pay amount'));
        $show->field('payment', __('Payment'));
        $show->field('status', __('Status'));
        $show->field('reason', __('Reason'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new RechargeOrder());

        $form->number('uid', __('Uid'));
        $form->text('order_sn', __('Order sn'));
        $form->mobile('phone', __('Phone'));
        $form->decimal('amount', __('Amount'));
        $form->switch('use_ticket', __('Use ticket'));
        $form->decimal('ticket_amount', __('Ticket amount'))->default(0.00);
        $form->decimal('pay_amount', __('Pay amount'))->default(0.00);
        $form->switch('payment', __('Payment'));
        $form->switch('status', __('Status'));
        $form->text('reason', __('Reason'));

        return $form;
    }

    public function refund(Request $request)
    {
        $recharge = RechargeOrder::find($request->id);

        if (!$recharge) {
            return Response::create(json_encode([
                'code' => 1,
                'msg' => '找不到充值订单'
            ]), 200);
        }

        if (!in_array($recharge->status, [RechargeOrder::PAY_SUCCESS, RechargeOrder::RECHARGE_FAIL])) {
            return Response::create(json_encode([
                'code' => 1,
                'msg' => '该充值订单不允许退回'
            ]), 200);
        }

        $rechargeService = new RechargeService();
        $orderStatusRes = $rechargeService->sta($recharge->order_sn);

        $allowRefund = false;
        if (!$orderStatusRes['result'] || $orderStatusRes['result']['game_state'] == 9) {
            $allowRefund = true;
        }

        if (!$allowRefund) {
            return Response::create(json_encode([
                'code' => 400,
                'msg' => '该充值订单不允许退回'
            ]), 200);
        }

        $pay = Pay::alipay(config('pay.alipay'));
        if ($recharge->payment == 2) {
            $pay = Pay::wechat(config('pay.wechat'));

            $fee = bcmul($recharge->pay_amount, 100, 0);
            $order = [
                'type' => 'app',
                'out_trade_no' => $recharge->order_sn,
                'out_refund_no' => $recharge->order_sn,
                'total_fee' => $fee,
                'refund_fee' => $fee,
                'refund_desc' => $recharge->phone . '充值退款',
            ];

        } else {
            $order = [
                'out_trade_no' => $recharge->order_sn,
                'refund_amount' => $recharge->pay_amount,
            ];
        }

        DB::beginTransaction();
        try {
            if ($recharge->pay_amount > 0) {
                $result = $pay->refund($order);

                $refundStatus = $recharge->payment == 2 ? $result->result_code == 'SUCCESS' : $result->code == 10000;

                if (!$refundStatus) {
                    throw new \Exception('退回状态返回异常');
                }
            }

            $payLog = PayLogs::where('order_sn', $recharge->order_sn)->first();
            $payLog->status = PayLogs::PAY_REFUND;
            $payLog->save();

            $recharge->status = RechargeOrder::PAY_REFUND;
            $recharge->save();

            //退回优惠券
            if ($recharge->tickets) {
                $coupons = json_decode($recharge->tickets, true);
                UserCoupons::whereIn('id', $coupons)->update([
                    'use_time' => null,
                    'status' => UserCoupons::STATUS_USEABLE
                ]);
            }

            DB::commit();

            return Response::create(json_encode([
                'code' => 0,
                'msg' => '资金已退回'
            ]), 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return Response::create(json_encode([
                'code' => 1,
                'msg' => '资金退回失败'
            ]), 200);
        }
    }
}

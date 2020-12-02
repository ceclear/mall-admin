<?php

namespace App\Admin\Controllers;

use App\Models\Withdrawal;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;

class WithdrawalAmountController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '提现金额';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Withdrawal());

        $grid->model()->select(DB::raw('status, SUM(amount) as amounts, count(id) as counts'))->groupBy('status');

        $grid->column('type', __('区间'))->display(function ($type) {
            return '';
        });
        $grid->column('status', __('状态'))->display(function ($status) {
            return Withdrawal::$status[$status];
        });
        $grid->column('amounts', __('金额'));
        $grid->column('counts', __('人数'));

        $grid->disableActions();

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

        $show->field('id', __('Id'));
        $show->field('uid', __('Uid'));
        $show->field('trade_no', __('Trade no'));
        $show->field('amount', __('Amount'));
        $show->field('status', __('Status'));
        $show->field('remark', __('Remark'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('out_trade_no', __('Out trade no'));
        $show->field('real_name', __('Real name'));
        $show->field('mobile', __('Mobile'));
        $show->field('alipay', __('Alipay'));
        $show->field('operator', __('Operator'));

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

        $form->number('uid', __('Uid'));
        $form->text('trade_no', __('Trade no'));
        $form->decimal('amount', __('Amount'))->default(0.000);
        $form->switch('status', __('Status'));
        $form->text('remark', __('Remark'));
        $form->text('out_trade_no', __('Out trade no'));
        $form->text('real_name', __('Real name'));
        $form->mobile('mobile', __('Mobile'));
        $form->text('alipay', __('Alipay'));
        $form->number('operator', __('Operator'));

        return $form;
    }
}

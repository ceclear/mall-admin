<?php

namespace App\Admin\Controllers;

use App\Models\UserBenefitTotal;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UbtController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\UserBenefitTotal';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new UserBenefitTotal());

        $grid->column('id', __('Id'));
        $grid->column('uid', __('Uid'));
        $grid->column('ym', __('Ym'));
        $grid->column('amount', __('Amount'));
        $grid->column('ext_amount', __('Ext amount'));
        $grid->column('amount_subsidy', __('Amount subsidy'));
        $grid->column('amount_team', __('Amount team'));
        $grid->column('total_amount', __('Total amount'));
        $grid->column('status', __('Status'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->column('per_amount', __('Per amount'));
        $grid->column('per_ext_amount', __('Per ext amount'));
        $grid->column('per_amount_subsidy', __('Per amount subsidy'));
        $grid->column('per_amount_team', __('Per amount team'));
        $grid->column('per_total_amount', __('Per total amount'));
        $grid->column('pay_order_num', __('Pay order num'));
        $grid->column('settle_order_num', __('Settle order num'));
        $grid->column('ymd', __('Ymd'));
        $grid->column('pay_order_team_num', __('Pay order team num'));
        $grid->column('settle_order_team_num', __('Settle order team num'));
        $grid->column('share_pay_order_num', __('Share pay order num'));
        $grid->column('share_settle_order_num', __('Share settle order num'));
        $grid->column('share_amount', __('Share amount'));
        $grid->column('share_pre_amount', __('Share pre amount'));
        $grid->column('my_total_amount', __('My total amount'));
        $grid->column('my_pre_total_amount', __('My pre total amount'));
        $grid->column('order_source', __('Order source'));
        $grid->column('user_level', __('User level'));

        $grid->actions(function ($actions) {
            $actions->disableDelete();
         //   $actions->disableEdit();
        });
        $grid->filter(function ($filter) {

            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->equal('uid', 'uid');
            $filter->equal('ym', 'ym');
            $filter->equal('ymd', 'ymd');

        });
        $grid->disableCreateButton();
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
        $show = new Show(UserBenefitTotal::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('uid', __('Uid'));
        $show->field('ym', __('Ym'));
        $show->field('amount', __('Amount'));
        $show->field('ext_amount', __('Ext amount'));
        $show->field('amount_subsidy', __('Amount subsidy'));
        $show->field('amount_team', __('Amount team'));
        $show->field('total_amount', __('Total amount'));
        $show->field('status', __('Status'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('per_amount', __('Per amount'));
        $show->field('per_ext_amount', __('Per ext amount'));
        $show->field('per_amount_subsidy', __('Per amount subsidy'));
        $show->field('per_amount_team', __('Per amount team'));
        $show->field('per_total_amount', __('Per total amount'));
        $show->field('pay_order_num', __('Pay order num'));
        $show->field('settle_order_num', __('Settle order num'));
        $show->field('ymd', __('Ymd'));
        $show->field('pay_order_team_num', __('Pay order team num'));
        $show->field('settle_order_team_num', __('Settle order team num'));
        $show->field('share_pay_order_num', __('Share pay order num'));
        $show->field('share_settle_order_num', __('Share settle order num'));
        $show->field('share_amount', __('Share amount'));
        $show->field('share_pre_amount', __('Share pre amount'));
        $show->field('my_total_amount', __('My total amount'));
        $show->field('my_pre_total_amount', __('My pre total amount'));
        $show->field('order_source', __('Order source'));
        $show->field('user_level', __('User level'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new UserBenefitTotal());

        $form->number('uid', __('Uid'));
        $form->text('ym', __('Ym'));
        $form->decimal('amount', __('Amount'))->default(0.00000000);
        $form->decimal('ext_amount', __('Ext amount'))->default(0.00000000);
        $form->decimal('amount_subsidy', __('Amount subsidy'))->default(0.00000000);
        $form->decimal('amount_team', __('Amount team'))->default(0.00000000);
        $form->decimal('total_amount', __('Total amount'))->default(0.00000000);
        $form->switch('status', __('Status'));
        $form->decimal('per_amount', __('Per amount'))->default(0.00000000);
        $form->decimal('per_ext_amount', __('Per ext amount'))->default(0.00000000);
        $form->decimal('per_amount_subsidy', __('Per amount subsidy'))->default(0.00000000);
        $form->decimal('per_amount_team', __('Per amount team'))->default(0.00000000);
        $form->decimal('per_total_amount', __('Per total amount'))->default(0.00000000);
        $form->number('pay_order_num', __('Pay order num'));
        $form->number('settle_order_num', __('Settle order num'));
        $form->date('ymd', __('Ymd'))->default(date('Y-m-d'));
        $form->number('pay_order_team_num', __('Pay order team num'));
        $form->number('settle_order_team_num', __('Settle order team num'));
        $form->number('share_pay_order_num', __('Share pay order num'));
        $form->number('share_settle_order_num', __('Share settle order num'));
        $form->number('share_amount', __('Share amount'));
        $form->number('share_pre_amount', __('Share pre amount'));
        $form->decimal('my_total_amount', __('My total amount'))->default(0.00000000);
        $form->decimal('my_pre_total_amount', __('My pre total amount'))->default(0.00000000);
        $form->switch('order_source', __('Order source'))->default(1);
        $form->number('user_level', __('User level'))->default(1);

        return $form;
    }
}

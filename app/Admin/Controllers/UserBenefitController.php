<?php

namespace App\Admin\Controllers;

use App\Models\UsersBenefit;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UserBenefitController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\UsersBenefit';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new UsersBenefit());

        $grid->column('id', __('Id'));
        $grid->column('beneficiary_uid', __('Beneficiary uid'));
        $grid->column('contributor_uid', __('Contributor uid'));
        $grid->column('contributor_level', __('Contributor level'));
        $grid->column('beneficiary_level', __('Beneficiary level'));
        $grid->column('beneficiary_stratum', __('Beneficiary stratum'));
        $grid->column('order_sn', __('Order sn'));
        $grid->column('order_source', __('Order source'));
        $grid->column('order_benefit_amount', __('Order benefit amount'));
        $grid->column('order_pay_price', __('Order pay price'));
        $grid->column('amount', __('Amount'));
        $grid->column('amount_subsidy', __('Amount subsidy'));
        $grid->column('amount_ext', __('Amount ext'));
        $grid->column('total_amount', __('Total amount'));
        $grid->column('rate', __('Rate'));
        $grid->column('team_colonel_rate', __('Team colonel rate'));
        $grid->column('team_colonel_amount', __('Team colonel amount'));
        $grid->column('team_colonel_num', __('Team colonel num'));
        $grid->column('team_senior_colonel_rate', __('Team senior colonel rate'));
        $grid->column('team_senior_colonel_amount', __('Team senior colonel amount'));
        $grid->column('team_senior_colonel_num', __('Team senior colonel num'));
        $grid->column('order_pay_ym', __('Order pay ym'));
        $grid->column('order_pay_date', __('Order pay date'));
        $grid->column('order_earning_date', __('Order earning date'));
        $grid->column('order_earning_ym', __('Order earning ym'));
        $grid->column('order_earning_time', __('Order earning time'));
        $grid->column('order_pay_time', __('Order pay time'));
        $grid->column('order_status', __('Order status'));
        $grid->column('is_settle', __('Is settle'));
        $grid->column('is_per_settle', __('is_per_settle'));
        $grid->column('is_direct_settle', __('Is direct settle'));
        $grid->column('type', __('Type'));
      //  $grid->column('extends_json', __('Extends json'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->column('amount_base', __('Amount base'));
        $grid->column('is_per_settle', __('Is per settle'));

        $grid->filter(function ($filter) {

            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->equal('order_sn', '订单号');
            $filter->equal('beneficiary_uid', '收益人uid');

        });
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
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
        $show = new Show(UsersBenefit::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('beneficiary_uid', __('Beneficiary uid'));
        $show->field('contributor_uid', __('Contributor uid'));
        $show->field('contributor_level', __('Contributor level'));
        $show->field('beneficiary_level', __('Beneficiary level'));
        $show->field('beneficiary_stratum', __('Beneficiary stratum'));
        $show->field('order_sn', __('Order sn'));
        $show->field('order_source', __('Order source'));
        $show->field('order_benefit_amount', __('Order benefit amount'));
        $show->field('order_pay_price', __('Order pay price'));
        $show->field('amount', __('Amount'));
        $show->field('amount_subsidy', __('Amount subsidy'));
        $show->field('amount_ext', __('Amount ext'));
        $show->field('total_amount', __('Total amount'));
        $show->field('rate', __('Rate'));
        $show->field('team_colonel_rate', __('Team colonel rate'));
        $show->field('team_colonel_amount', __('Team colonel amount'));
        $show->field('team_colonel_num', __('Team colonel num'));
        $show->field('team_senior_colonel_rate', __('Team senior colonel rate'));
        $show->field('team_senior_colonel_amount', __('Team senior colonel amount'));
        $show->field('team_senior_colonel_num', __('Team senior colonel num'));
        $show->field('order_pay_ym', __('Order pay ym'));
        $show->field('order_pay_date', __('Order pay date'));
        $show->field('order_earning_date', __('Order earning date'));
        $show->field('order_earning_ym', __('Order earning ym'));
        $show->field('order_earning_time', __('Order earning time'));
        $show->field('order_pay_time', __('Order pay time'));
        $show->field('order_status', __('Order status'));
        $show->field('is_settle', __('Is settle'));
        $show->field('is_direct_settle', __('Is direct settle'));
        $show->field('type', __('Type'));
        $show->field('extends_json', __('Extends json'))->json();
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('amount_base', __('Amount base'));
        $show->field('is_per_settle', __('Is per settle'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new UsersBenefit());

        $form->number('beneficiary_uid', __('Beneficiary uid'));
        $form->number('contributor_uid', __('Contributor uid'));
        $form->switch('contributor_level', __('Contributor level'));
        $form->switch('beneficiary_level', __('Beneficiary level'));
        $form->switch('beneficiary_stratum', __('Beneficiary stratum'));
        $form->text('order_sn', __('Order sn'));
        $form->switch('order_source', __('Order source'));
        $form->decimal('order_benefit_amount', __('Order benefit amount'));
        $form->decimal('order_pay_price', __('Order pay price'));
        $form->decimal('amount', __('Amount'))->default(0.00000000);
        $form->decimal('amount_subsidy', __('Amount subsidy'))->default(0.00000000);
        $form->decimal('amount_ext', __('Amount ext'))->default(0.00000000);
        $form->decimal('total_amount', __('Total amount'))->default(0.00000000);
        $form->decimal('rate', __('Rate'))->default(0.000);
        $form->decimal('team_colonel_rate', __('Team colonel rate'))->default(0.000);
        $form->decimal('team_colonel_amount', __('Team colonel amount'))->default(0.00000000);
        $form->number('team_colonel_num', __('Team colonel num'));
        $form->decimal('team_senior_colonel_rate', __('Team senior colonel rate'))->default(0.000);
        $form->decimal('team_senior_colonel_amount', __('Team senior colonel amount'))->default(0.00000000);
        $form->number('team_senior_colonel_num', __('Team senior colonel num'));
        $form->text('order_pay_ym', __('Order pay ym'));
        $form->date('order_pay_date', __('Order pay date'))->default(date('Y-m-d'));
        $form->date('order_earning_date', __('Order earning date'))->default(date('Y-m-d'));
        $form->text('order_earning_ym', __('Order earning ym'));
        $form->number('order_earning_time', __('Order earning time'));
        $form->number('order_pay_time', __('Order pay time'));
        $form->switch('order_status', __('Order status'));
        $form->switch('is_settle', __('Is settle'));
        $form->switch('is_direct_settle', __('Is direct settle'));
        $form->switch('type', __('Type'))->default(1);
        $form->textarea('extends_json', __('Extends json'));
        $form->decimal('amount_base', __('Amount base'));
        $form->switch('is_per_settle', __('Is per settle'));

        return $form;
    }
}

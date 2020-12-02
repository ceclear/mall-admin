<?php

namespace App\Admin\Controllers;

use App\Models\FreeActivity;
use Carbon\Carbon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\MessageBag;

class FreeActivityController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\FreeActivity';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new FreeActivity());

        $grid->actions(function ($actions) {
            // 去掉查看
            $actions->disableView();
        });

        $grid->column('id', __('Id'));
        $grid->column('title', __('页面title'));
        $grid->column('tb_goods', __('淘宝商品'));
        $grid->column('pdd_goods', __('拼多多商品'));
        $grid->column('img', __('推荐活动缩略图'))->image();
        $grid->column('uri', __('推荐活动地址'));
        $grid->column('rule', __('新人免单规则'));
        $grid->column('start_time', __('开始时间'));
        $grid->column('end_time', __('结束时间'));
        $grid->column('created_at', __('创建时间'));
        $grid->column('updated_at', __('修改时间'));

        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new FreeActivity());

        $form->text('title', __('页面title'));
        $form->textarea('tb_goods', __('淘宝商品'));
        $form->textarea('pdd_goods', __('拼多多商品'));
        $form->image('img', __('推荐活动缩略图'));
        $form->text('uri', __('推荐活动地址'));
        $form->textarea('rule', __('新人免单规则'));
        $form->datetime('start_time', __('开始时间'))->default(date('Y-m-d H:i:s'));
        $form->datetime('end_time', __('结束时间'))->default(date('Y-m-d H:i:s'));

        $form->saving(function (Form $form) {
            if (!$form->start_time || !$form->end_time) {
                throw new \Exception('开始和结束时间必填');
            }
            if (strtotime($form->start_time) >= strtotime($form->end_time)) {
                throw new \Exception('开始时间不能大于等于结束时间');
            }
        });

        return $form;
    }
}

<?php

namespace App\Admin\Controllers;

use App\Models\Alert;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class AlertController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '弹框管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Alert());
        $grid->actions(function ($actions) {
            // 去掉查看
            $actions->disableView();
        });
        $grid->filter(function($filter){
            $filter->disableIdFilter();
            $filter->like('title', '标题');
            $filter->equal('jump_type', '跳转类型')->select(Alert::$jumpType);
            $filter->equal('status', '状态')->select(['关闭', '开启']);
        });
        $grid->column('id', __('Id'));
        $grid->column('title', __('标题'));
        $grid->column('thumbnail', __('缩略图'))->image();
        $grid->column('jump_type', __('跳转类型'))->display(function ($type) {
            return Alert::$jumpType[$type];
        });
        $grid->column('jump_id', __('跳转ID'));
        $grid->column('jump_url', __('跳转URL'));
        $grid->column('sort', __('排序'));
        $grid->column('status', __('状态'))->display(function ($status) {
            return $status == 0 ? "关闭":"开启";
        });
        $grid->column('begin_time', __('开始时间'));
        $grid->column('end_time', __('结束时间'));

        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Alert());

        $form->text('title', __('标题'));
        $form->file('thumbnail', __('缩略图'));
        $form->select('jump_type', '跳转类型')->options(Alert::$jumpType);
        $form->text('jump_id', __('跳转ID'));
        $form->text('jump_url', __('跳转URL'));
        $form->text('sort', __('排序'))->default(0);
        $form->switch('status', __('状态'));
        $form->datetimeRange('begin_time', 'end_time', '开始时间-结束时间');

        return $form;
    }
}

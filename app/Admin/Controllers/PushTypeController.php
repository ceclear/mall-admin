<?php

namespace App\Admin\Controllers;

use App\Models\PushType;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class PushTypeController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '推送类型';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new PushType());

        $grid->column('id', __('ID'));
        $grid->column('name', __('类型名称'));
        $grid->column('is_index', __('是否首页页面'))->display(function ($is_index) {
            return $is_index == 1 ? '是' : '否';
        });
        $grid->column('url', __('地址'));
        $grid->column('param', __('参数'));
        $grid->column('status', __('状态'))->display(function ($status) {
            return PushType::$status[$status];
        });
        $grid->column('created_at', __('创建时间'));
        $grid->column('updated_at', __('修改时间'));

        $grid->actions(function ($actions) {
            $actions->disableView();
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
        $show = new Show(PushType::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('is_index', __('Is index'));
        $show->field('url', __('Url'));
        $show->field('param', __('Param'));
        $show->field('status', __('Status'));
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
        $form = new Form(new PushType());

        $form->text('name', __('类型名称'))->required();
        $form->radio('is_index', __('是否首页'))->options([1 => '是', 0 => '否'])->default(0)->required();
        $form->text('url', __('地址'));
        $form->text('param', __('参数'));
        $form->radio('status', '状态')->options([0 => '保存', 1 => '发布', 2 => '关闭'])->default(0)->required();

        return $form;
    }
}

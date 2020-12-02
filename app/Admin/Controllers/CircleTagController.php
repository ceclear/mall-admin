<?php

namespace App\Admin\Controllers;

use App\Models\CircleTags;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CircleTagController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '标签管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CircleTags());

        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->like('name', __('标签名称'));
            $filter->equal('status', __("状态"))->select(CircleTags::$status);
        });

        $grid->column('id', __('Id'))->sortable();
        $grid->column('name', __('标签名称'));
        $grid->column('status', __("状态"))->display(function ($status) {
            return CircleTags::$status[$status];
        })->label(CircleTags::$status_color);
        $grid->column('created_at', __('创建时间'));
        $grid->column('updated_at', __('修改时间'));

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
        $show = new Show(CircleTags::findOrFail($id));



        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CircleTags());

        $form->text('name', '标签名称')->required();
        $form->radio('status', '状态')->options([0 => '保存', '1'=> '发布'])->default(0)->required();

        return $form;
    }
}

<?php

namespace App\Admin\Controllers;

use App\Models\SpecialTyper;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class SpecialTyperController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\SpecialTyper';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SpecialTyper());

        $grid->column('id', __('ID'))->sortable();
        $grid->column('title', __('分类名称'));
        $grid->column('sort', __('排序'))->sortable()->replace(['' => '-']);
        $grid->column('status', '状态')->display(function ($status) {
            if ($status == 1)
                return '发布';
            elseif ($status == 2)
                return '保存';
            else
                return '关闭';
        });
        $grid->column('created_at', __('新增时间'));
        $grid->column('updated_at', __('修改时间'));
        // filter($callback)方法用来设置表格的简单搜索框
        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->like('title','分类名称');
            $filter->equal('status', '状态')->select(['1' => '发布', '2'=> '保存', '3'=> '关闭']);
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
        $show = new Show(SpecialTyper::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('title', __('分类名称'));
        $show->field('sort', __('排序'));
        $show->field('status', __('状态 1=发布，2=保存；3=关闭'));
        $show->field('created_at', __('新增时间'));
        $show->field('updated_at', __('修改时间'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new SpecialTyper());

        $form->text('title', __('分类名称'))->rules('required');
        $form->text('sort', __('排序'));
        $form->radio('status',  __('状态'))->options(['1' => '发布', '2'=> '保存', '3'=> '关闭'])->default('2');#1=发布，2=保存；3=关闭

        return $form;
    }
}

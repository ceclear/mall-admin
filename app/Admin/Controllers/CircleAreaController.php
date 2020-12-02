<?php

namespace App\Admin\Controllers;

use App\Models\CircleAreas;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CircleAreaController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '分区管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CircleAreas());

        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->equal('type', __('板块'))->select(CircleAreas::$type);
            $filter->like('name', __('分区名称'));
            $filter->equal('status', __("状态"))->select(CircleAreas::$status);
        });

        $grid->column('id', __('Id'))->sortable();
        $grid->column('name', __('分区名称'));
        $grid->column('type', __("板块"))->display(function ($type) {
            return CircleAreas::$type[$type];
        });
        $grid->column('sort', __('排序'))->sortable()->replace(['' => '-']);
        $grid->column('status', __("状态"))->display(function ($status) {
            return CircleAreas::$status[$status];
        })->label(CircleAreas::$status_color);
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
        $show = new Show(CircleAreas::findOrFail($id));



        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CircleAreas());

        $form->select('type', '板块')->options(CircleAreas::$type)->required();
        $form->text('name', '分区名称')->required();
        $form->text('sort', __('排序'))->placeholder('值越小越靠前');
        $form->radio('status', '状态')->options([0 => '保存', '1'=> '发布'])->default(0)->required();
        return $form;
    }
}

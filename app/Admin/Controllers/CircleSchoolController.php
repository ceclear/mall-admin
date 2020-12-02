<?php

namespace App\Admin\Controllers;

use App\Models\CircleAccounts;
use App\Models\CircleAreas;
use App\Models\CircleArticles;
use App\Models\CircleTags;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Carbon;

class CircleSchoolController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '链信学院管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CircleArticles());

        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->like('nickname', __('账号昵称'));
            $filter->equal('status', __("状态"))->select(CircleArticles::$status);
        });

        $grid->model()->latest();

        $grid->column('id', __('Id'));
        $grid->column('title', __('标题'));
        $grid->column('area.name', __('分区'));
        $grid->column('avatar', __('缩略图'))->image("", 30, 30);;
        $grid->column('online_word', __('在线文档地址'));
        $grid->column('is_top', __('是否顶置'))->display(function ($is_top) {
            return $is_top == 1 ? '是' : '否';
        });
        $grid->column('release_time', __('发布时间'));
        $grid->column('status', __("状态"))->display(function ($status) {
            return CircleArticles::$status[$status];
        })->label(CircleArticles::$status_color);
        $grid->column('created_at', __('创建时间'));
        $grid->column('updated_at', __('修改时间'));

        $grid->model()->where('type', CircleAreas::PLATE_SCHOOL);

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
        $show = new Show(CircleArticles::findOrFail($id));



        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CircleArticles());

        $form->select('area_id', '分区')->options(CircleAreas::toNode(CircleAreas::getArea(CircleAreas::PLATE_SCHOOL)))->required();
        $form->text('title', '标题')->required();
        $form->textarea('info.copy_writing', '描述')->rows(10);
        $form->text('online_word', '在线文档地址')->required();
        $form->image('avatar', '缩略图')->uniqueName()->required();
        $form->radio('is_top', '是否顶置')->options([0 => '否', '1'=> '是'])->default(0);
        $form->text('top_day', '置顶天数')->placeholder('请填写置顶天数，如不填写则只置顶1天');
        $form->datetime('release_time', '发布时间')->format('YYYY-MM-DD')->default(Carbon::today());
        $form->radio('status', '状态')->options([0 => '保存', '1'=> '发布'])->default(0)->required();

        $form->saving(function ($form) {
            $form->model()->type = CircleAreas::PLATE_SCHOOL;
        });

        return $form;
    }
}

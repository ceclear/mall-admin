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

class CircleMaterialController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '营销素材管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CircleArticles());

        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->like('nickname', __('账号昵称'));
            $filter->equal('status', __("状态"))->select(CircleArticles::$status);
        });

        $grid->model()->latest();

        $grid->column('id', __('Id'));
        $grid->column('account.nickname', __('发布账号'));
        $grid->column('area_id', __('分区'))->display(function ($area_id) {
            $data = CircleAreas::whereIn('id', $area_id)->get();
            $str = "";
            $count = $data->count();
            foreach ($data as $k => $d) {
                if ($k == ($count - 1)) {
                    $str .= $d->name;
                } else {
                    $str .= $d->name . "|";
                }
            }
            return $str;
        });
        $grid->column('tag.name', __('标签'));
        $grid->column('info.material', __('缩略图'))->image("", 30, 30);
        $grid->column('is_top', __('是否顶置'))->display(function ($is_top) {
            return $is_top == 1 ? '是' : '否';
        });
        $grid->column('release_time', __('发布时间'));
        $grid->column('status', __("状态"))->display(function ($status) {
            return CircleArticles::$status[$status];
        })->label(CircleArticles::$status_color);
        $grid->column('created_at', __('创建时间'));
        $grid->column('updated_at', __('修改时间'));

        $grid->model()->where('type', CircleAreas::PLATE_MATERIAL);

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
        $method = request()->route()->getActionMethod();

        $form = new Form(new CircleArticles());

        $form->multipleSelect('area_id', '分区')->options(CircleAreas::toNode(CircleAreas::getArea(CircleAreas::PLATE_MATERIAL)))->required();
        $form->select('tag_id', '标签')->options(CircleTags::toNode(CircleTags::getTag()));
        $form->select('account_type', '账号类型')->options(CircleAccounts::$type)->load('account_id', '/admin/circle/accounts/account');
        if ($method == 'edit') {
            $id = request()->route()->parameter('material');
            $article = CircleArticles::find($id);

            $option = [
                $article->account_id => $article->account->nickname
            ];

            $form->select('account_id', '发布账号')->options($option)->required();
        } else {
            $form->select('account_id', '发布账号')->required();
        }
        $form->UEditor('info.copy_writing', '文案');
        //$form->multipleImage('info.material', __('缩略图'))->uniqueName()->removable()->required();
        $form->table('material', '缩略图', function ($table) {
            $table->number('sort', '排序')->min(1);
            $table->image('image', '图片')->uniqueName();
        });
        $form->radio('is_top', '是否顶置')->options([0 => '否', '1' => '是'])->default(0);
        $form->text('top_day', '置顶天数')->placeholder('请填写置顶天数，如不填写则只置顶1天');
        $form->datetime('release_time', '发布时间')->format('YYYY-MM-DD')->default(Carbon::today());
        $form->radio('status', '状态')->options([0 => '保存', '1' => '发布'])->default(0)->required();

        $form->saving(function ($form) {
            $form->model()->type = CircleAreas::PLATE_MATERIAL;
        });

        return $form;
    }
}

<?php

namespace App\Admin\Controllers;

use App\Models\Circle;
use App\Models\CircleAccounts;
use App\Models\CircleAreas;
use App\Models\CircleArticleInfo;
use App\Models\CircleArticles;
use App\Models\CircleTags;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Carbon;

class CircleSelectController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '链信优选管理';

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
            $filter->like('nickname', __('发布账号'));
            $filter->equal('recommend_good', __("商品ID/链接"));
            $filter->equal('area_id', __("分区"))->select(Circle::toNode(CircleAreas::getArea(CircleAreas::PLATE_SELECT)));
            $filter->date('release_time', __("发布时间"));
            $filter->equal('status', __("状态"))->select(CircleArticles::$status);
        });

        $grid->model()->latest();

        $grid->column('id', __('Id'));
        $grid->column('account.nickname', __('发布账号'));
        $grid->column('area.name', __('分区'));
        $grid->column('tag.name', __('标签'));
        $grid->column('flash_time_start', __('秒杀开始时间'));
        $grid->column('flash_time_end', __('秒杀结束时间'));
        $grid->column('recommend_type', __('推荐类型'))->display(function ($recommend_type) {
            return CircleArticles::$recommend_type[$recommend_type];
        });
        $grid->column('recommend_good', __('商品ID/链接'))->display(function ($recommend_good) {
            return strlen($recommend_good) >= 20 ? substr($recommend_good, 0, 20) . '...' : $recommend_good;
        });
        $grid->column('info.material', __('素材'))->image("", 30, 30);
        $grid->column('is_top', __('是否顶置'))->display(function ($is_top) {
            return $is_top == 1 ? '是' : '否';
        });
        $grid->column('release_time', __('发布时间'));
        $grid->column('status', __("状态"))->display(function ($status) {
            return CircleArticles::$status[$status];
        })->label(CircleArticles::$status_color);
        $grid->column('created_at', __('创建时间'));
        $grid->column('updated_at', __('修改时间'));

        $grid->model()->where('type', CircleAreas::PLATE_SELECT);

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
        ini_set("memory_limit", "-1");

        $method = request()->route()->getActionMethod();

        if ($method == 'update') {
            $id = request()->route()->parameter('select');

            $infos = request()->get('info');

            foreach ($infos as $key => $info) {
                if ($info == '_file_del_') {
                    CircleArticleInfo::where('article_id', $id)->update([
                        $key => null
                    ]);
                }
            }
        }

        $form = new Form(new CircleArticles());

        $form->select('area_id', '分区')->options(CircleAreas::toNode(CircleAreas::getArea(CircleAreas::PLATE_SELECT)))->required();
        $form->select('tag_id', '标签')->options(CircleTags::toNode(CircleTags::getTag()));
        $form->select('account_type', '账号类型')->options(CircleAccounts::$type)->load('account_id', '/admin/circle/accounts/account');
        if ($method == 'edit') {
            $id = request()->route()->parameter('select');

            $article = CircleArticles::find($id);

            $option = [
                $article->account_id => $article->account->nickname
            ];

            $form->select('account_id', '发布账号')->options($option)->required();
        } else {
            $form->select('account_id', '发布账号')->required();
        }
        $form->UEditor('info.copy_writing', '文案');
        $form->dateRange('flash_time_start', 'flash_time_end', '秒杀时间')->help('可不选择，如未选择时间，则前台不展示秒杀时间。');;
        $form->select('recommend_type', '推荐类型')->options(CircleArticles::$recommend_type)->required();
        $form->text('recommend_good', '推荐商品')->placeholder('请根据类型填写商品ID/链接')->required();
        $form->radio('show_video', '是否展示商品视频')->options([0 => '否', '1' => '是'])->default(0);
        $form->file('info.video', '视频')->uniqueName()->removable();
        $form->image('show_cover', '视频/活动封面')->help('选择展示商品视频和推荐类型为活动页时请选择')->uniqueName()->removable();
        $form->text('show_title', '活动标题')->help('推荐类型为活动页时请填写');
        //$form->multipleImage('info.material', '素材')->uniqueName();
        $form->table('material', '素材', function ($table) {
            $table->number('sort', '排序')->min(1);
            $table->image('image', '图片')->uniqueName();
        });
        $form->radio('is_top', '是否顶置')->options([0 => '否', '1' => '是'])->default(0)->required();
        $form->text('top_day', '置顶天数')->placeholder('请填写置顶天数，如不填写则只置顶1天');
        $form->datetime('release_time', '发布时间')->format('YYYY-MM-DD')->default(Carbon::today())->help('如需设置指定发布时间，请将状态选择为保存状态');
        $form->radio('status', '状态')->options([0 => '保存', '1' => '发布'])->default(0)->required()->help("选择发布则立即发布，如需定时发布，请选择保存状态");

        $form->saving(function ($form) {
            $form->model()->flash_time_end = Carbon::parse($form->flash_time_end)->endOfDay();
            $form->model()->type = CircleAreas::PLATE_SELECT;
        });

        return $form;
    }
}

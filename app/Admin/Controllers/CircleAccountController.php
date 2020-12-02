<?php

namespace App\Admin\Controllers;

use App\Models\CircleAccounts;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CircleAccountController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '发布账号管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CircleAccounts());

        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->equal('type', __('账号类型'))->select(CircleAccounts::getType());
            $filter->like('nickname', __('账号昵称'));
            $filter->equal('status', __("状态"))->select(CircleAccounts::$status);
        });

        $grid->column('id', __('Id'))->sortable();
        $grid->column('nickname', __('账号昵称'));
        $grid->column('avatar', __('头像'))->image("", 30, 30);
        $grid->column('type', __("账号类型"))->display(function ($type) {
            return CircleAccounts::$type[$type];
        });
        $grid->column('status', __("状态"))->display(function ($status) {
            return CircleAccounts::$status[$status];
        })->label(CircleAccounts::$status_color);
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
        $show = new Show(CircleAccounts::findOrFail($id));



        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CircleAccounts());

        $form->select('type', '账号类型')->options(CircleAccounts::$type)->required();
        $form->text('nickname', '账号昵称')->required();
        $form->image('avatar', '头像')->uniqueName()->required();
        $form->radio('status', '状态')->options([0 => '保存', '1'=> '发布'])->default(0)->required();

        return $form;
    }

    /**
     * 根据分区查询账号
     * @param Request $request
     * @return mixed
     */
    public function account(Request $request)
    {
        $type = $request->get('q');

        return CircleAccounts::where('type', $type)->get(['id', DB::raw('nickname as text')]);
    }
}

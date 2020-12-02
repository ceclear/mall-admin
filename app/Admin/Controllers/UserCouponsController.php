<?php

namespace App\Admin\Controllers;

use App\Models\UserCoupons;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UserCouponsController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header(trans('coupon'))
            ->description(trans('admin.description'))
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header(trans('admin.detail'))
            ->description(trans('admin.description'))
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header(trans('admin.edit'))
            ->description(trans('admin.description'))
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header(trans('admin.create'))
            ->description(trans('admin.description'))
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new UserCoupons);

        $grid->id('ID');
        $grid->uid('uid');
        $grid->type('优惠券类型')->display(function ($v){
            return UserCoupons::$type[$v];
        });
        $grid->amount('面额');
        $grid->valid_begin('有效期开始日期');
        $grid->valid_end('有效期结束日期');
        $grid->use_time('使用时间');
        $grid->status('status');
        $grid->created_at(trans('admin.created_at'));
        $grid->updated_at(trans('admin.updated_at'));
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableview();
        });
        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->equal('uid', 'uid');
            $filter->equal('amount', '面额');
            $filter->between('valid_begin', '有效期开始日期')->date();
            $filter->between('valid_end', '有效期结束日期')->date();
            $filter->between('use_time', '使用时间')->datetime();
        });
        $grid->disableCreateButton();
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
        $show = new Show(UserCoupons::findOrFail($id));
        $show->id('ID');
        $show->uid('uid');
        $show->type('type');
        $show->amount('amount');
        $show->valid_begin('valid_begin');
        $show->valid_end('valid_end');
        $show->use_time('use_time');
        $show->status('status');
        $show->created_at(trans('admin.created_at'));
        $show->updated_at(trans('admin.updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new UserCoupons);

        $form->display('ID');
        $form->text('uid', 'uid');
        $form->text('type', 'type');
        $form->text('amount', 'amount');
        $form->text('valid_begin', 'valid_begin');
        $form->text('valid_end', 'valid_end');
        $form->text('use_time', 'use_time');
        $form->text('status', 'status');
        $form->display(trans('admin.created_at'));
        $form->display(trans('admin.updated_at'));

        return $form;
    }
}

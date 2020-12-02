<?php

namespace App\Admin\Controllers;


use App\Http\Controllers\Controller;
use App\Models\RejectManage;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class RejectManageController extends Controller
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
            ->header('ios上架隐藏功能管理')
            ->description('ios上架隐藏功能管理')
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
        $grid = new Grid(new RejectManage());
        $grid->model()->orderBy("id", "desc");
        $grid->disableRowSelector();
        $grid->id('ID');
        $grid->yc_key('隐藏健');
        $grid->switch('隐藏状态')->display(function ($status) {
            return $status == 1 ? '隐藏中' : '启用中';
        });
        $grid->status('状态')->display(function ($status) {
            return $status == 1 ? '正常' : '禁用';
        })->label([1 => 'info', 0 => 'danger']);
        $grid->created_at(trans('admin.created_at'));
        $grid->updated_at(trans('admin.updated_at'));
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
        $show = new Show(RejectManage::findOrFail($id));

        $show->id('ID');
        $show->yc_key('隐藏健');
        $show->field('switch','隐藏状态')->as(function ($status) {
            return $status == 1 ? "隐藏中": "启用中";
        })->label();
        $show->status('状态')->as(function ($status) {
            return $status == 1 ? '正常' : '禁用';
        })->label();
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
        $form = new Form(new RejectManage());
        $form->display('id', 'ID');
        $form->text('yc_key', '隐藏健');
        $form->switch('switch', '隐藏状态')->default(1);
        $form->switch('status', '状态')->default(1);
//        $form->display(trans('admin.created_at'));
//        $form->display(trans('admin.updated_at'));

        return $form;
    }
}

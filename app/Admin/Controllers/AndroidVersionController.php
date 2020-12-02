<?php

namespace App\Admin\Controllers;

use App\Models\AndroidVersion;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class AndroidVersionController extends Controller
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
            ->header('安卓版本控制')
            ->description('安卓版本控制')
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
        $grid = new Grid(new AndroidVersion);
        $grid->model()->orderBy("id", "desc");
        $grid->disableRowSelector();
        $grid->id('ID');
        $grid->version('版本');
        $grid->type('APP类型')->display(function ($v){
            return $v==1?'安卓':'IOS';
        });
        $grid->is_force('强制更新')->display(function ($v) {
            return $v == 1 ? '是' : '否';
        });
        $grid->update_record('更新日志');
        $grid->file_url('更新文件')->display(function ($file) {
            if(!$file){
                return '';
            }
            $file = env('OSS_URL') . '/' . $file;
            return "<a style='cursor: pointer' href='" . $file . "'>下载</a>";
        });
        $grid->create_user('创建者');
        $grid->status('状态')->display(function ($status) {
            return $status == 1 ? '正常' : '禁用';
        })->label([1 => 'info', 0 => 'danger']);
        $grid->created_at(trans('admin.created_at'));
        $grid->updated_at(trans('admin.updated_at'));
        $grid->filter(function ($filter) {

            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->equal('is_force', '强制更新')->select(function () {
                return ['否', '是'];
            });
            $filter->like('version', '版本号');
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
        $show = new Show(AndroidVersion::findOrFail($id));

        $show->id('ID');
        $show->version('version');
        $show->is_force('is_force');
        $show->update_record('update_record');
        $show->create_user('create_user');
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
        $form = new Form(new AndroidVersion);
        $form->display('id', 'ID');
        $form->text('version', '版本号');
        $form->switch('is_force', '强制更新');
        $form->textarea('update_record', '更新日志');
        $form->file('file_url', '更新文件');
        $form->text('create_user', '创建者')->readonly()->default(Admin::user()->username);
        $form->select('type', 'APP类型')->options([1=>'安卓',2=>'IOS']);
        $form->switch('status', '状态')->default(1);
//        $form->display(trans('admin.created_at'));
//        $form->display(trans('admin.updated_at'));

        return $form;
    }
}

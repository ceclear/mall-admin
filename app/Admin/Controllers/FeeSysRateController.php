<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\EnableRow;
use App\Models\FeeSysRate;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class FeeSysRateController extends Controller
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
            ->header('返利设置')
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
        $grid = new Grid(new FeeSysRate);
        $grid->disableRowSelector();
        $grid->id('ID');
        $grid->rate('返利比例');
        $grid->status('状态')->display(function ($status) {
            return $status == 1 ? '正常' : '禁用';
        })->label([1=>'info',0=>'danger']);
        $grid->created_at(trans('admin.created_at'));
        $grid->updated_at(trans('admin.updated_at'));
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
            $changeStatus = $actions->row['status'] == 1 ? 0 : 1;
            $changeShow   = $actions->row['status'] == 1 ? '禁用' : '启用';
            $changeIcon   = $actions->row['status'] == 1 ? 'fa-remove' : 'fa-check';
            $actions->append(new EnableRow($actions->getKey(), 'api/fee-sys-rate', $changeStatus, $changeShow, $changeIcon));//自定义操作
        });
        $grid->disableFilter();
        $grid->disableExport();
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
        $show = new Show(FeeSysRate::findOrFail($id));

        $show->id('ID');
        $show->rate('rate');
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
        $form = new Form(new FeeSysRate);

        $form->display('id', 'ID');
        $form->text('rate', '返利比例');
        $form->switch('status', '状态');
        $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));
        return $form;
    }

    /**
     * 更改状态
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus(Request $request)
    {
        $id    = $request->input('id', 0);
        $value = $request->input('value', 0);
        $rel =FeeSysRate::where('id', $id)->update(['status' => $value]);
        if($rel)
            return $this->responseJson(1);
        return $this->responseJson(0);
    }
}

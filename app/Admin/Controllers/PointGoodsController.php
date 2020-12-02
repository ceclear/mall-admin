<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\EnableRow;
use App\Http\Controllers\Controller;
use App\Models\Point;
use App\Models\PointGoods;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;


class PointGoodsController extends Controller
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
        return "暂未开放";
        return $content
            ->header('秒杀商品')
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
        $grid = new Grid(new PointGoods());
        $grid->disableRowSelector();
        $grid->id('ID');
        $grid->open_point('场次');
        $grid->total('展示数量');
        $grid->status('状态')->display(function ($status) {
            return $status == 1 ? '正常' : '禁用';
        })->label([1 => 'info', 0 => 'danger']);
        $grid->created_at(trans('admin.created_at'));
        $grid->updated_at(trans('admin.updated_at'));
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
            $actions->append('<a href="/admin/" class="grid-row-edit" style="margin-right: 10px"><i class="fa fa-bars"></i>商品列表</a>');
            $changeStatus = $actions->row['status'] == 1 ? 0 : 1;
            $changeShow   = $actions->row['status'] == 1 ? '禁用' : '启用';
            $changeIcon   = $actions->row['status'] == 1 ? 'fa-remove' : 'fa-check';
            $actions->append(new EnableRow($actions->getKey(), 'point-status', $changeStatus, $changeShow, $changeIcon));//自定义操作
        });
        $grid->disableFilter();
        $grid->disableExport();
        if (\Illuminate\Support\Facades\Request::has('type')) {
            $grid->model()->where('pos_id', \Illuminate\Support\Facades\Request::input('type'));
        }
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
        $show = new Show(AdPosition::findOrFail($id));

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
        $form = new Form(new PointGoods());

        $form->display('id', 'ID');
        $form->text('open_point','场次');
        $form->text('total','展示数量');
        $form->switch('status', '状态');
//        $form->display('created_at', trans('admin.created_at'));
//        $form->display('updated_at', trans('admin.updated_at'));
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
        $rel   = PointGoods::where('id', $id)->update(['status' => $value]);
        if ($rel)
            return $this->responseJson(1);
        return $this->responseJson(0);
    }


}

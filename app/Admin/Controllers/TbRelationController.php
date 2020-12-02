<?php

namespace App\Admin\Controllers;


use App\Http\Controllers\Controller;

use App\Models\TbRelationUser;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;


class TbRelationController extends Controller
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
            ->header('淘宝绑定管理')
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
        $grid = new Grid(new TbRelationUser());
        $grid->model()->orderBy('id', 'desc');
        $grid->disableRowSelector();
        $grid->id('UID')->sortable();
        $grid->relation_id('渠道ID');
        $grid->special_id('special_id');
        $grid->tb_nickname('tb_nickname');
        $grid->tb_user_id('tb_user_id');
        $grid->created_at('created_at');
        $grid->updated_at('updated_at');
        $grid->actions(function ($actions) {
            $actions->disableView();
            if (Admin::user()->id != 14) {
                $actions->disableDelete();
            }
//            $actions->disableDelete();
            $actions->disableEdit();
        });
        $grid->filter(function ($filter) {

            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->equal('id', 'uid');
            $filter->equal('tb_user_id', 'tb_user_id');
            $filter->equal('special_id', 'special_id');
            $filter->equal('relation_id', 'relation_id');
            $filter->group('tb_nickname','nickname',function ($group) {
                $group->equal('全');
                $group->like('模糊');
            });
            $filter->between('created_at', '创建时间')->datetime();
        });
        $grid->disableCreateButton();
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
        $show = new Show(TbRelationUser::findOrFail($id));

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
        $form = new Form(new TbRelationUser());

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
        $rel   = UsersInfo::where('id', $id)->update(['status' => $value]);
        if ($rel)
            return $this->responseJson(1);
        return $this->responseJson(0);
    }


}

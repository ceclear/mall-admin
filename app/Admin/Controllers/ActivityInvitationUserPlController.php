<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\UpdateActivity;
use App\Models\ActivityInvitationUserPl;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class ActivityInvitationUserPlController extends Controller
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
            ->header(trans('活动统计表'))
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
        $grid = new Grid(new ActivityInvitationUserPl);
//        $grid->id('ID');
        $grid->uid('用户');
        $grid->prize_num_total('总获得抽奖数')->sortable();
        $grid->prize_num('可用抽奖数')->sortable();
        $grid->prize_num_already('已用抽奖数')->sortable();
        $grid->total_hf_amount('累计话费')->sortable();
        $grid->hf_amount('当前话费，在达到50后会转移到话费券 这个数会-50')->sortable();
        $grid->created_at(trans('admin.created_at'));
        $grid->updated_at(trans('admin.updated_at'));
//        $grid->setActionClass(Grid\Displayers\DropdownActions::class);
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableview();
//            $actions->add(new UpdateActivity());
        });
        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->equal('uid', 'uid');
            $filter->equal('prize_num_total', '总获得抽奖数');
            $filter->equal('prize_num', '可用抽奖数');
            $filter->equal('prize_num_already', '已用抽奖数');
        });
//        $grid->disableCreateButton();
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
        $show = new Show(ActivityInvitationUserPl::findOrFail($id));

        $show->id('ID');
        $show->name('name');
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
        $form = new Form(new ActivityInvitationUserPl);

//        $form->display('ID');
        $form->text('uid', 'uid')->attribute(['autocomplete' => 'off']);
        $form->number('prize_num', '可用抽奖数')->min(1)->default(1);
//        $form->display(trans('admin.created_at'));
//        $form->display(trans('admin.updated_at'));
        $form->setAction('updatePrizeNum');
        return $form;
    }

    public function updatePrizeNum(Request $request)
    {
        $uid    = $request->input('uid');
        $amount = $request->input('prize_num');
        $rel    = ActivityInvitationUserPl::addPrizeNum($uid,$amount);
        if($rel){
            admin_success("操作成功");
            return back();
        }
        admin_error("操作失败");
        return back();
    }
}

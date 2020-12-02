<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\PushAlert;
use App\Models\CircleAreas;
use App\Models\PushList;
use App\Models\PushType;
use App\Services\PushService;
use Encore\Admin\Admin;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class PushListController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '推送列表';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new PushList());

        $grid->column('id', __('Id'));
        $grid->column('title', __('标题'));
        $grid->column('desc', __('描述'));
        $grid->column('type.name', __('类型'));
        $grid->column('platform', __('推送平台'))->display(function ($platform) {
            return PushList::$plant[$platform];
        });
        $grid->column('push_time', __('预发送时间'));
        $grid->column('status', __('状态'))->display(function ($status) {
            return PushList::$status[$status];
        });
        $grid->column('manage.name', __('发布者'));
        $grid->column('created_at', __('创建时间'));
        $grid->column('updated_at', __('修改时间'));

        $grid->actions(function ($actions) {
            $actions->append(new PushAlert($actions->getKey(), $actions->row->title));
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
        $show = new Show(PushList::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('type_id', __('Type id'));
        $show->field('data_id', __('Data id'));
        $show->field('title', __('Title'));
        $show->field('desc', __('Desc'));
        $show->field('push_time', __('Push time'));
        $show->field('platform', __('Platform'));
        $show->field('status', __('Status'));
        $show->field('publisher', __('Publisher'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new PushList());

        $form->select('type_id', '类型')->options(PushType::toNode(PushType::whereStatus(PushType::STATUS_PUSH)->get()))->required();
        $form->text('data_id', __('数据ID'));
        $form->text('brand_id', '品牌ID')->placeholder('品牌ID')->help('仅在推送品牌闪购时此参数有效');
        $form->text('title', __('标题'))->required();
        $form->textarea('desc', __('描述'));
        $form->datetime('push_time', __('推送时间'))->default(date('Y-m-d H:i:s'))->required();
        $form->select('platform', '推送平台')->options(PushList::$plant)->required();
        $form->radio('status', __('状态'))->options([0 => '保存', 1 => '发布'])->default(0)->required();

        $form->saving(function ($form) {
            $form->model()->publisher = \Encore\Admin\Facades\Admin::user()->id;
        });

        return $form;
    }

    public function now(Request $request)
    {
        $id = $request->id;

        if (!$id) {
            return Response::create(json_encode([
                'code' => 1,
                'msg' => '参数错误'
            ]), 200);
        }

        $task = PushList::find($id);

        if (!$task) {
            return Response::create(json_encode([
                'code' => 1,
                'msg' => '找不到推送数据'
            ]), 200);
        }

        PushService::pushTask($task);

        return Response::create(json_encode([
            'code' => 0,
            'msg' => '推送成功'
        ]), 200);
    }
}

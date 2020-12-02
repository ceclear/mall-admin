<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\EnableRow;
use App\GlobalConstant;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;


class ActivityController extends Controller
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
            ->header('活动管理')
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
//        $head = '活动模板' . \request('type', 1) . '编辑';
//        return $content
//            ->header($head)
//            ->description(trans('admin.description'))
//            ->body($this->form()->edit($id));

        $form = $this->form();
        $form->edit($id);
        $type = $form->model()->type;
        $head = '活动模板' . $type;
        if ($type != 4) {
            $form->color('bg_color', '背景色')->value($form->model()->bg_color);
        }
        $arr = $form->model()->getExtendJson('board');
        if ($arr) {
            if (in_array($type, [1, 2, 3])) {
                Activity::templateA($form, $arr);
            }
            if ($type == 4) {
                Activity::templateB($form, $arr);
            }
            if ($type == 5) {
                Activity::templateC($form, $arr);
            }

        }
        return $content
            ->header($head)
            ->description(trans('admin.description'))
            ->body($form);
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        $head = '活动模板' . \request('type', 1);
        return $content
            ->header($head)
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
        $type = \request('type');
        $grid = new Grid(new Activity());
        $grid->model()->where('type', $type)->orderBy('id', 'desc');
        $grid->disableRowSelector();
        $grid->id('ID');
        $grid->column('type', '模板');
        $grid->column('title', '页面title');

//        $grid->column('title', '页面title')->expand(function ($model) {
//           return Activity::showExtendsJson($model);
//        });
        $grid->column('head_pic', '头图')->lightbox(['width' => 100, 'height' => 50]);
        $grid->bg_color('背景色');
        Activity::showColumns($grid, $type);
        $grid->status('状态')->display(function ($status) {
            return $status == 1 ? '正常' : '禁用';
        })->label([1 => 'info', 0 => 'danger']);
        $grid->created_at(trans('admin.created_at'));
        $grid->updated_at(trans('admin.updated_at'));

        $grid->actions(function ($actions) {
            $actions->disableView();
            $changeStatus = $actions->row['status'] == 1 ? 0 : 1;
            $changeShow   = $actions->row['status'] == 1 ? '' : '';
            $changeIcon   = $actions->row['status'] == 1 ? 'fa-toggle-on' : 'fa-toggle-off';
            $actions->append(new EnableRow($actions->getKey(), 'activity-status', $changeStatus, $changeShow, $changeIcon));//自定义操作
        });
        $grid->disableCreateButton();
        $grid->disableFilter();
        $grid->disableExport();
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append('<a href="/admin/activity/create?type=1" class="btn btn-sm btn-info">添加活动模板1</a>');
            $tools->append('<a href="/admin/activity/create?type=2" class="btn btn-sm btn-info">添加活动模板2</a>');
            $tools->append('<a href="/admin/activity/create?type=3" class="btn btn-sm btn-info">添加活动模板3</a>');
            $tools->append('<a href="/admin/activity/create?type=4" class="btn btn-sm btn-info">添加活动模板4</a>');
            $tools->append('<a href="/admin/activity/create?type=5" class="btn btn-sm btn-info">添加活动模板5</a>');
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
        $show = new Show(Activity::findOrFail($id));

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
        $type = \request('type', 1);
        $form = new Form(new Activity());
        $form->hidden('type')->default($type);
        $form->display('id', 'ID');
        $form->text('title', 'title')->required()->attribute(['autocomplete' => 'off']);
        $form->image('head_pic', '头图')->required()->uniqueName();
        $form->UEditor('rule', '活动规则(不填则不显示)');
        $form->switch('status', '状态')->default(1);
        if ($type != 4 && $form->isCreating()) {
            $form->color('bg_color', '背景色')->required()->attribute(['autocomplete' => 'off']);
        }
        Activity::createForm($form, $type);
        $form->hidden('id')->value($form->id ?? 0);
        $form->saving(function (Form $form) {
            Activity::formatFormField($form, $form->type);
        });
        $platArr = GlobalConstant::getSourceMap();
        for ($i = 1; $i <= Activity::BASE_NUM; $i++) {
            foreach ($platArr as $key => $item) {
                $form->ignore(['title_sub_' . $i, 'gids_' . $i . '_' . $key]);
            }
//            $form->ignore(['title_sub_' . $i, 'gids_' . $i]);
        }
        $form->ignore(['title_sub', 'gids']);
        for ($i = 1; $i <= Activity::MO_BAN_4; $i++) {
            foreach ($platArr as $key => $item) {
                $form->ignore(['pro_img_' . $i, 'pro_id_' . $i, 'pro_line_color_' . $i, 'gids_' . $i.'_'.$key,'gids_'.$key]);
            }
//            $form->ignore(['pro_img_' . $i, 'pro_id_' . $i, 'pro_line_color_' . $i, 'gids_' . $i]);
        }
        for ($i = 1; $i <= Activity::MO_BAN_5; $i++) {
            foreach ($platArr as $key => $item) {
//                $form->ignore(['pro_img_' . $i, 'target_type_' . $i, 'target_value_' . $i]);
                $form->ignore(['pro_img_' . $i, 'target_type_' . $i, 'target_value_' . $i,'gids_'.$key]);
            }
        }
        $form->tools(function (Form\Tools $tools) {
            $tools->disableList();
        });
        $form->saved(function (Form $form) use ($type) {
            return redirect('/admin/activity?type=' . $type);
        });
        return $form;
    }


    public function changeStatus(Request $request)
    {
        $id    = $request->input('id', 0);
        $value = $request->input('value', 0);
        $rel   = Activity::where('id', $id)->update(['status' => $value]);
        if ($rel)
            return $this->responseJson(1);
        return $this->responseJson(0);
    }

}

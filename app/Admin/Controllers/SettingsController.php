<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\EnableRow;
use App\Models\FeeSysRate;
use App\Http\Controllers\Controller;
use App\Models\Settings;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
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
            ->header('设置')
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
        $form = $this->form();
        $form->edit($id);
        if ($form->model()->key == 'tutor_qr') {
            $form->text('key', '键')->value($form->model()->key)->readonly();
            if(strpos($form->model()->value,'http')!==false){
                $form->image('value', '二维码')->value($form->model()->value);
            }else{
                $form->image('value', '二维码');
            }

        } else {
            $form->text('key', '键')->value($form->model()->key);
            $form->text('value', '值')->value($form->model()->value);
        }
        return $content
            ->header(trans('admin.edit'))
            ->description(trans('admin.description'))
            ->body($form);
//        return $content
//            ->header(trans('admin.edit'))
//            ->description(trans('admin.description'))
//            ->body($this->form()->edit($id));
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
        $grid = new Grid(new Settings());
        $grid->disableRowSelector();
        $grid->id('ID');
        $grid->key('键');
        $grid->key_desc('键值说明');
        $grid->column('value')->display(function ($title, $column) {
            // 如果这一列的status字段的值等于1，直接显示title字段
            if ($this->key == "tutor_qr" && strpos($this->value,'http')!==false) {
                return $column->lightbox(['width' => 100, 'height' => 40]);
            }
            // 否则显示为editable
            return $title;

        });

        $grid->created_at(trans('admin.created_at'));
        $grid->updated_at(trans('admin.updated_at'));
        $grid->actions(function ($actions) {
            $actions->disableView();
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
        $show = new Show(Settings::findOrFail($id));

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
        $form = new Form(new Settings());

        $form->display('id', 'ID');
        if ($form->isCreating()) {
            $form->text('key', '键');
            $form->text('value', '值');
        }

        $form->text('key_desc', '说明');

//        $form->display('created_at', trans('admin.created_at'));
//        $form->display('updated_at', trans('admin.updated_at'));
        $form->saving(function (Form $form) {
            Cache::forget('launch_ad_config');
            if ($form->model()->key == 'tutor_qr'&&$form->isEditing()) {
                $form->model()->value = env('OSS_URL') . '/images/' . $_FILES['value']['name'];
            } else {
                $form->model()->value = \request('value');
            }

        });
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
        $rel   = FeeSysRate::where('id', $id)->update(['status' => $value]);
        if ($rel)
            return $this->responseJson(1);
        return $this->responseJson(0);
    }
}

<?php

namespace App\Admin\Controllers;

use App\Models\ErrorLogs;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ErrorLogsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'ErrorLogs';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ErrorLogs());

        $grid->column('id', __('Id'));
        $grid->column('app_id', __('App id'));
        $grid->column('type', __('Type'));
        $grid->column('err_msg', __('Err msg'));
        $grid->column('extends_json', __('Extends json'))->display(function ($value){
            if($value){
                $content=json_decode($value,true);
                return '<pre><code>'.json_encode($content, JSON_UNESCAPED_UNICODE  ).'</code></pre>';
            }
            return $value;
        });
        $grid->filter(function ($filter) {
            $filter->equal('type', '错误类型');
        });
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->model()->orderBy("id", "desc");
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
        $show = new Show(ErrorLogs::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('app_id', __('App id'));
        $show->field('type', __('Type'));
        $show->field('err_msg', __('Err msg'));
        $show->field('extends_json', __('Extends json'))->json();
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
        $form = new Form(new ErrorLogs());

        $form->text('app_id', __('App id'));
        $form->text('type', __('Type'));
        $form->textarea('err_msg', __('Err msg'));
        $form->textarea('extends_json', __('Extends json'));

        return $form;
    }
}

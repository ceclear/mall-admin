<?php

namespace App\Admin\Controllers;

use App\Models\SharePosters;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

class SharePostersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\SharePosters';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SharePosters());
        $grid->actions(function ($actions) {
            // 去掉查看
            $actions->disableView();
        });
        $grid->column('id', __('ID'));
        $grid->column('poster', __('海报'))->image();
        $grid->column('status', __('状态'))->display(function ($s){
            return $s==0?'关闭':'开启';
        });
        $grid->column('type', __('类型'))->display(function ($s){
            return SharePosters::$type[$s];
        });
        $grid->column('qr_url', __('二维码地址'));
        $grid->column('created_at', __('创建时间'));
        $grid->column('updated_at', __('修改时间'));

        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new SharePosters());

        $form->file('poster', __('海报'));
        $form->switch('status', __('状态'))->default(1);
        $form->text('qr_url', __('二维码地址'));
        $form->select('type', __('类型'))->options(SharePosters::$type);
        return $form;
    }

    /*public function collection(Request $request)
    {
        $poster = $request->file('poster');print_r($poster->getExtension());exit;
        $poster->storeAs(public_path(), 'test.'.$poster->getExtension());exit;
        $path = $poster->getRealPath().'/'.$poster->getFilename();//print_r($poster->getFilename());exit;
        $manager = new ImageManager();
        $manager->make($path)->resize(200, 200);exit;
    }*/
}

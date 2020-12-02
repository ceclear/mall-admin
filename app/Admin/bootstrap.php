<?php

use Encore\Admin\Facades\Admin;
use App\Admin\Extensions\Nav;
use App\Admin\Actions;
use Encore\Admin\Form;
use App\Admin\Extensions\Form\ExtraImage;
/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

Form::forget(['map', 'editor']);
Form::init(function (Form $form) {

    $form->disableEditingCheck();

    $form->disableCreatingCheck();

    $form->disableViewCheck();

    $form->tools(function (Form\Tools $tools) {
        $tools->disableDelete();
        $tools->disableView();
//        $tools->disableList();
    });
});

Admin::navbar(function (\Encore\Admin\Widgets\Navbar $navbar) {
    $navbar->right(Nav\Link::make('Settings', 'forms/settings'));
    $navbar->right(new Actions\ClearCache());

//    $navbar->left(Nav\Shortcut::make([
//        '添加用户' => 'users/create',
//    ], 'fa-plus')->title('快捷操作'));

//    $navbar->left(new Nav\Dropdown());
});
Admin::js('/js/layer/layer.js');
Admin::js('/js/jquery.cookie.js');
Admin::favicon('/img/favicon.ico');
Encore\Admin\Form::extend('image', ExtraImage::class); // 重新注册新的 Image 组件

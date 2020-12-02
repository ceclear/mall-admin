<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\WithdrawalAlert;
use App\Models\UsersInfo;
use App\Models\Withdrawal;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WithdrawalTaxController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '扣税记录';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Withdrawal());

        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->like('name', __('分区名称'));
        });

        $grid->column('id', __('Id'))->sortable();
        $grid->column('uid', __('用户昵称(UID)'))->display(function ($uid) {
            return UsersInfo::where('id',$uid)->value('nickname')."（{$uid}）";
        });
        $grid->column('real_name', __('实名姓名'));
        $grid->column('id_card', __('身份证号码'));
        $grid->column('mobile', __('联系电话'));
        $grid->column('amount', __('提现数量'));
        $grid->column('status', __('状态'))->display(function ($status) {
            return Withdrawal::$status[$status];
        });
        $grid->column('created_at', __('创建时间'));
        $grid->column('updated_at', __('修改时间'));

        $grid->disableActions();

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
        $show = new Show(Withdrawal::findOrFail($id));



        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Withdrawal());



        return $form;
    }
}

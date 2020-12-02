<?php

namespace App\Admin\Controllers;

use App\Models\Goods;
use App\Models\NewPeopleFreeLogs;
use App\Models\Order;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class NewPoepleLogsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '未免单原因';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new NewPeopleFreeLogs());
        $grid->disableActions();
        $grid->filter(function($filter){
            $filter->equal('uid', 'uid');
            $filter->equal('order_sn', 'order_sn');
        });

        $grid->column('id', __('Id'))->sortable();
        $grid->column('uid', __('Uid'));
        $grid->column('price', __('支付金额'));
        $grid->column('order_sn', __('订单号'));
        $grid->column('source', __('来源'))->display(function ($v) {
            return Goods::getSourceMap($v);
        })->label([Goods::SOURCE_TB => 'info', Goods::SOURCE_JD => 'danger', Goods::SOURCE_PDD => 'warning']);
        $grid->column('is_free', __('是否免单'))->display(function ($v){
            switch ($v){
                case 0:$str='非免单';break;
                case 1:$str='免单';break;
                case 3:$str='失去资格';break;
                default:$str='已返佣';break;
            }
            return $str;
        });;
        $grid->column('status', __('订单状态'))->display(function ($v) {
            return Order::getStatusMap($v);
        });
        $grid->column('goods_item_id', __('商品id'));
        $grid->column('goods_item_num', __('商品数量'));
        $grid->column('reason', __('原因'));
        $grid->column('new_price', __('最新价格'));
        $grid->column('created_at', __('创建时间'));
        $grid->column('updated_at', __('更新时间'));

        return $grid;
    }
}

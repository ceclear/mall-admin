<?php

namespace App\Admin\Controllers;


use App\Admin\Extensions\EnableRow;
use App\GlobalConstant;
use App\Http\Controllers\Controller;
use App\Models\ExecQueue;
use App\Models\Goods;
use App\Models\GoodsCat;
use App\Models\Order;

use App\Services\OrderService;
use App\TimestampBetween;

use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;


class OrderController extends Controller
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
            ->header('订单列表')
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
        $grid = new Grid(new Order);
        $grid->model()->orderBy('order.id', 'desc');
        $grid->disableRowSelector();
        $grid->model()->leftJoin('users_benefit',function (JoinClause $join){
            $join->on('order.order_sn','=','users_benefit.order_sn');
            $join->on('order.source','=','users_benefit.order_source');
            $join->on(function(JoinClause $j){
                $j->orOn('order.uid','=','users_benefit.beneficiary_uid');
                $j->orOn('order.share_uid','=','users_benefit.beneficiary_uid');
            });
          /*
            $join->on(function($j){
                $j->raw("if(`order`.uid > 0, `order.uid`=users_benefit.beneficiary_uid', `order.share_uid`=users_benefit.beneficiary_uid')");
            });*/
        })->leftJoin('tb_relation_user','tb_relation_user.id','=','uid')->select([
            "order.*",
            "users_benefit.total_amount",
            "tb_relation_user.created_at as tb_created_at"
        ]);

        $grid->order_sn('订单号');
        $grid->column("", "手动绑定")->display(function(){
            if($this->extends_json){
                $val=json_decode($this->extends_json,true);
                return @$val['is_user_bind'] ? "是" : "否";
            }
        });
        $grid->extends_json('京东订单号')->display(function ($value){
            if($this->source == Goods::SOURCE_JD){
                if($this->extends_json){
                    $val=json_decode($this->extends_json,true);
                    return isset($val['jd_order_sn'])?$val['jd_order_sn']:'';
                }
                return '';
            }
            return '';
        });
        $grid->status('订单状态')->display(function ($v) {
            return Order::getStatusMap($v);
        })->sortable();
        $grid->refund_tag('是否维权')->display(function ($v) {
            return Order::getRefundTagMap($v);
        });
        $grid->cash_back('是否返利')->display(function ($v) {
            return Order::STATUS_FAN_LI[$v];
        });
        $grid->uid('用户ID')->sortable();
        $grid->p_uid('父级');
        $grid->share_uid('分享者ID');
        $grid->column('userInfo.nickname', '用户昵称');
        $grid->column('userInfo.phone', '手机');
        $grid->goods_item_id('商品ID');
        $grid->goods_title('商品名称')->limit(20);
        $grid->pay_price('单价');
        $grid->goods_item_num('数量');
        $grid->source('订单平台')->display(function ($v) {
            return Goods::getSourceMap($v);
        })->label([Goods::SOURCE_TB => 'info', Goods::SOURCE_JD => 'danger', Goods::SOURCE_PDD => 'warning']);
        $grid->pay_price('支付金额');
        $grid->total_commission_rate('佣金比例');
        $grid->user_self_fee_rate("自购佣金比例");
        $grid->system_fee_rate("系统比例");
        $grid->pre_fee('获得淘宝预估收益');
//        $grid->total_commission_fee('用户获得收益');
        $grid->column('total_amount','用户获得收益');
        $grid->column('sys_settlement','系统结算');
        $grid->is_free('新人免单')->display(function ($v){
            switch ($v){
                case 0:$str='非免单';break;
                case 1:$str='免单';break;
                case 3:$str='失去资格';break;
                default:$str='已返佣';break;
            }
            return $str;
        });//0非免单，1免单，2已返佣
        $grid->created_at('订单同步时间')->sortable();
        $grid->pay_time('支付时间')->display(function ($v) {
            return $v ? date('Y-m-d H:i:s', $v) : '';
        })->sortable();
        $grid->settle_time('结算时间')->display(function ($v) {
            return $v ? date('Y-m-d H:i:s', $v) : '';
        })->sortable();
        $grid->column('tb_created_at', '淘宝绑定时间')->sortable();
        $grid->qh_final_price('免单金额');
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            if ( $actions->row->refund_tag == Order::REFUND_TAG_NOT && $actions->row->status == Order::STATUS_FEE_SUCCESS ) {
                $actions->append(new EnableRow($actions->getKey(), '/admin/order/setRefundTag', Order::REFUND_TAG_ING, "标记维权", ""));//自定义操作
            } elseif ( $actions->row->refund_tag == Order::REFUND_TAG_ING ) {
                $actions->append(new EnableRow($actions->getKey(), '/admin/order/setRefundTag', Order::REFUND_TAG_SUCCESS, "  维权成功  ", ""));//自定义操作
                $actions->append(new EnableRow($actions->getKey(), '/admin/order/setRefundTag', Order::REFUND_TAG_FAIL, "维权失败", ""));//自定义操作
            }
        });
        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->equal('userInfo.phone', '手机号');
//            $filter->equal('order_sn', '订单号');
            $filter->where(function ($query){
                if(!empty($this->input)){
                    $query->where('order.order_sn',$this->input);
                }
            },'订单号','text');
            $filter->equal('order_parent_sn', '主订单号');
            $filter->equal('uid', '用户ID');
            $filter->equal('share_uid', '分享者ID');
            $filter->equal('goods_item_id', '商品ID');
//            $filter->gt('is_free', '免单订单');
            $filter->gt('is_free', '是否免单')->select(function () {
                return ['全部', '免单'];
            });
            $filter->equal('status', '状态')->select(function () {
                return Order::getStatusMap();
            });
            $filter->equal('cash_back', '返利')->select(function () {
                return Order::STATUS_FAN_LI;
            });
            $filter->equal('source', '订单来源')->select(function () {
                return GlobalConstant::getSourceMap();
            });
//            $filter->between('pay_time', '支付时间')->datetime();
            $filter->use(new TimestampBetween('pay_time', '支付时间'))->datetime();
            $filter->use(new TimestampBetween('settle_time', '结算时间'))->datetime();
            $filter->between('tbBind.created_at', '淘宝绑定时间')->datetime();

        });
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->enableHotKeys();
        return $grid;
    }

    public function setRefundTag(Request $request)
    {
        $id = $request->input("id");
        $refundTag = $request->input("value");
        if ( !$id ) {
            return $this->responseJson(self::Response_failed_code, "无效的ID");
        }
        if ( !Order::getRefundTagMap($refundTag) ) {
            return $this->responseJson(self::Response_failed_code, "无效的标签");
        }
        $order = Order::where("id", $id)->first();
        if ( !$order ) {
            return $this->responseJson(self::Response_failed_code, "无效的订单编号");
        }
        if ( $order->status != Order::STATUS_FEE_SUCCESS ) {
            return $this->responseJson(self::Response_failed_code, "该订单未结算不能标记维权");
        }
        /*if ( $refundTag == Order::REFUND_TAG_ING && $order->cash_back != GlobalConstant::IS_YES ) {
            return $this->responseJson(self::Response_failed_code, "该订单未返利不能标记维权");
        }*/
        if ( $refundTag != Order::REFUND_TAG_ING && $order->refund_tag !=  Order::REFUND_TAG_ING ) {
            return $this->responseJson(self::Response_failed_code, "该订单不是维权订单");
        }

        if ( ExecQueue::addOrderRefund($order->order_sn, $refundTag) ) {
            return $this->responseJson(self::Response_success_code, "已添加至执行队列");
        }
        return $this->responseJson(self::Response_failed_code, ExecQueue::Error());
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Order::findOrFail($id));

        $show->id('ID');
        $show->order_sn('订单号');
        $show->status('订单状态')->as(function ($v) {
            return Order::getStatusMap($v);
        })->sortable();
        $show->cash_back('是否返利')->as(function ($v) {
            return Order::STATUS_FAN_LI[$v];
        });
        $show->uid('用户ID');
        $show->uid('用户昵称');
        $show->goods_item_id('商品ID');
        $show->goods_title('商品名称')->limit(20);
        $show->pay_price('单价');
        $show->goods_item_num('数量');
        $show->source('订单平台')->as(function ($v) {
            return Goods::getSourceMap($v);
        });
        $show->pay_price('支付金额');
        $show->total_commission_rate('佣金比例');
        $show->pre_fee('获得淘宝预估收益');
        $show->total_commission_fee('用户获得收益');
        $show->created_at('下单时间');
        $show->pay_time('支付时间')->as(function ($v) {
            return $v ? date('Y-m-d H:i:s', $v) : '';
        });
        $show->settle_time('结算时间')->as(function ($v) {
            return $v ? date('Y-m-d H:i:s', $v) : '';
        });
        $show->extends_json()->json();
        $show->field('tbBind', '淘宝绑定时间')->as(function ($tb) {
            return $tb ? $tb->created_at->toDateTimeString() : '';
        });
        $show->panel()->tools(function ($tools) {
            $tools->disableDelete();
            $tools->disableEdit();
        });
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
        $form = new Form(new Order());
        return $form;
    }

    protected function statics(Content $content)
    {
        $cat_parent = GoodsCat::where("level", 1)->where("status", GlobalConstant::IS_YES)->get()->pluck('name', 'id');
        $data       = ['partition' => GlobalConstant::getSystemPartitionMap(), 'cat_parent' => $cat_parent];
        return $content
            ->header('订单统计')
            ->description(trans('admin.description'))
            ->body(view('admin.Order.statics', $data))->render();

    }

    protected function staticsData(Request $request, OrderService $orderService)
    {
        $param       = $request->input();
        $rank_list   = $orderService->rankList($param);
        $top_statics = $orderService->topStatics($param);
        $day_statics = $orderService->dayStatics($param);
        $data        = [
            'rank_list'   => $rank_list,
            'top_statics' => $top_statics,
            'day_statics' => $day_statics,
        ];
        return $this->responseJson(self::Response_success_code, '', $data);
    }

}

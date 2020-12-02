<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\EnableRow;
use App\Http\Controllers\Controller;
use App\Models\ExecQueue;
use App\Models\UserBenefitTotal;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;

class UserBenefitTotalController extends Controller
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\UserBenefitTotal';

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('用户结算')
            ->description(trans('admin.description'))
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new UserBenefitTotal());
        $grid->disableCreateButton();
        $ym = \request()->get("ym");
        if ( $ym ) {
            $gTime = "ymd";
            $model = $grid->model()->groupBy("ymd")->selectRaw("sum(total_amount) as total_amount");
            $model->where("ym", $ym);
        } else {
            $model = $grid->model()->groupBy("ym")->selectRaw("sum(total_amount) as total_amount");
            $gTime = "ym";
        }
        $model->selectRaw($gTime);
        $model->selectRaw("sum(per_total_amount) as per_total_amount");
        $model->selectRaw("count(distinct uid) as cu");
        $model->selectRaw("count(distinct case when user_level = 1 then uid else null end) as pcu");
        $model->selectRaw("count(distinct case when user_level = 2 then uid else null end) as ccu");
        $model->selectRaw("count(distinct case when user_level = 3 then uid else null end) as sccu");

        $model->selectRaw("sum(case when user_level = 1 then total_amount else 0 end) as pAmount");
        $model->selectRaw("sum(case when user_level = 2 then total_amount else 0 end) as cAmount");
        $model->selectRaw("sum(case when user_level = 3 then total_amount else 0 end) as scAmount");

        $model->selectRaw("sum(case when user_level = 1 then per_total_amount else 0 end) as pre_pAmount");
        $model->selectRaw("sum(case when user_level = 2 then per_total_amount else 0 end) as pre_cAmount");
        $model->selectRaw("sum(case when user_level = 3 then per_total_amount else 0 end) as pre_scAmount");
        $model->selectRaw("sum(settle_order_num) as settle_order_num");
        $model->selectRaw("sum(pay_order_num) as pay_order_num");
        $model->orderBy($gTime, "desc");
        $grid->column($gTime, "时间");
        $grid->column('total_amount', __('结算总金额'));
        $grid->column('per_total_amount', __('预估总金额'));
        $grid->column("cu", "总人数");
        $grid->column("pcu", "合伙人数");
        $grid->column("ccu", "团长人数");
        $grid->column("sccu", "高级团长人数");

        $grid->column('pAmount', __('合伙人金额'));
        $grid->column('cAmount', __('团长金额'));
        $grid->column('scAmount', __('高级团长金额'));

        $grid->column('pre_pAmount', __('预估合伙人金额'));
        $grid->column('pre_cAmount', __('预估团长金额'));
        $grid->column('pre_scAmount', __('预估高级团长金额'));

        $grid->column('settle_order_num', __('结算订单数'));
        $grid->column('pay_order_num', __('付款订单数'));

        $grid->actions(function ($actions) use ($gTime) {
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();
            if ( $gTime == "ym" ) {
                $actions->append('<a href="/admin/userBenefitTotal?ym='.$actions->row->ym.'" class="grid-row-edit" style="margin-right: 10px"><i class="fa fa-bars"></i>详情</a>');
                $actions->append(new EnableRow($actions->getKey(), '/admin/userBenefitTotal/settlement', $actions->row->ym, "结算", "fa-check"));//自定义操作
            }
        });

        return $grid;
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
            ->body("OK");
    }

    public function settlement(Request $request)
    {   $ym = $request->get("value");
        if ( strtotime($ym) >= strtotime(date("Y-m")) ) {
            return $this->responseJson(self::Response_failed_code, "只能结算小于当前月份的数据");
        }
        if ( ExecQueue::addUsersBenefitTotalSettlementQueue($ym, "ym") ) {
            return $this->responseJson(self::Response_success_code, "已加入执行队列");
        }
        return $this->responseJson(self::Response_failed_code, ExecQueue::Error());
    }

}

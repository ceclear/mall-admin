<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\EnableRow;
use App\Http\Controllers\Controller;
use App\Models\ExecQueue;
use App\Models\UserBenefitTotal;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExecQueueController extends Controller
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '任务队列';

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('队列任务')
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
        $grid = new Grid(new ExecQueue());
        $grid->disableCreateButton();
        $model = $grid->model();
        $model->orderBy("exec_sort", "desc");
        $model->orderBy("id", "desc");
        $grid->column('id', __('ID'));
        $grid->column('unique_id', __('唯一标示'));
        $grid->column('type', __('任务类型'));
        $grid->column("status")->display(function ($val) {
            return ExecQueue::getStatusMap($val);
        });
        $grid->column("msg", "描述信息");
        $grid->column("created_at", "创建时间");
        $grid->column("updated_at", "更新时间");
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->append(new EnableRow($actions->getKey(), '/admin/execQueue/restExec', $actions->row->id, "重新执行", "fa-check"));//自定义操作
        });

        return $grid;
    }

    public function restExec(Request $request)
    {
        $c = (new ExecQueue())->getConnectionName();
        $c = \DB::connection($c);
        $c->beginTransaction();
        $execQueue = ExecQueue::lockForUpdate()->where("id", $request->get("value"))->first();
        if ( !$execQueue ) {
            $c->rollBack();
            return $this->responseJson(self::Response_failed_code, "未找到数据");
        }
        $execQueue->status = ExecQueue::STATUS_REST;
        if ( $execQueue->save() ) {
            $c->commit();
            return $this->responseJson(self::Response_success_code);
        }
        $c->rollBack();
        return $this->responseJson(self::Response_failed_code, "操作失败");
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
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(ExecQueue::findOrFail($id));

        $show->id('ID');
        $show->unique_id('唯一标示');
        $show->type('类型');
        $show->exec_sort('执行优先级');
        $show->status('订单状态')->as(function ($v) {
            return ExecQueue::getStatusMap($v);
        })->sortable();
        $show->extends_json("数据")->json();
        $show->created_at(trans('admin.created_at'));
        $show->updated_at(trans('admin.updated_at'));

        return $show;
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

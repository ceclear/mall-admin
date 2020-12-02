<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\EnableRow;
use App\Http\Controllers\Controller;
use App\Models\FeeCfg;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;


class FeeCfgController extends Controller
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
            ->header('佣金设置')
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
        $grid = new Grid(new FeeCfg());
        $grid->disableRowSelector();
        $grid->id('ID');
        $grid->type('级别')->display(function ($type) {
            switch ($type) {
                case 1:
                    return '合伙人';
                    break;
                case 2:
                    return '团长';
                    break;
                case 3:
                    return '高级团长';
                    break;
                default:
                    return '全部';
                    break;
            }
        });
        $grid->condition('达成条件')->display(function ($condition) {
            return self::conditionToStr($this->type, $condition);
        });
        $grid->self_fee_rate('产品自购')->display(function ($val) {
            return $val . '%';
        });
        $grid->subsidy_fee_rate('自购平台补贴')->display(function ($val) {
            return $val . '%';
        });
        $grid->team_fee_one_rate('团队一级合伙人')->display(function ($val) {
            return $val . '%';
        });
        $grid->team_fee_two_rate('团队二级合伙人')->display(function ($val) {
            return $val . '%';
        });
        $grid->share_fee_rate('分享佣金')->display(function ($val) {
            if (floatval($val) == 0)
                return '-';
            return $val . '%';
        });
        $grid->status('状态')->display(function ($status) {
            return $status == 1 ? '正常' : '禁用';
        })->label([1 => 'info', 0 => 'danger']);
        $grid->created_at(trans('admin.created_at'));
        $grid->updated_at(trans('admin.updated_at'));
        $grid->actions(function ($actions) {
            $actions->disableView();
//            $actions->disableDelete();
            $actions->disableEdit();
            $href = FeeCfg::createEditHref($actions->row['type'], $actions->row['id']);
            $actions->append("<a href=" . $href . " class='grid-row' style='margin-right: 10px'>
<i class='fa fa-edit'></i>&nbsp;&nbsp;编辑
</a>");
            $changeStatus = $actions->row['status'] == 1 ? 0 : 1;
            $changeShow   = $actions->row['status'] == 1 ? '禁用' : '启用';
            $changeIcon   = $actions->row['status'] == 1 ? 'fa-remove' : 'fa-check';
            $actions->append(new EnableRow($actions->getKey(), 'fee-cfg/fee-cfg-status', $changeStatus, $changeShow, $changeIcon));//自定义操作
        });
        $grid->disableCreateButton();
        $grid->disableFilter();
        $grid->disableExport();
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append('<a href="/admin/fee-cfg/create-cfg-partner" class="btn btn-sm btn-info">添加合伙人佣金设置</a>');
            $tools->append('<a href="/admin/fee-cfg/create-cfg-commander?type=2" class="btn btn-sm btn-info">添加团长佣金设置</a>');
            $tools->append('<a href="/admin/fee-cfg/create-cfg-commander?type=3" class="btn btn-sm btn-info">添加高级团长佣金设置</a>');
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
        $show = new Show(FeeCfg::findOrFail($id));

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
        $form = new Form(new FeeCfg());

        $form->display('id', 'ID');
        $form->text('rate', '返利比例');
        $form->switch('status', '状态');
        $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));
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
        $rel   = FeeCfg::where('id', $id)->update(['status' => $value]);
        if ($rel)
            return $this->responseJson(1);
        return $this->responseJson(0);
    }

    protected static function conditionToStr($type, $condition)
    {
        $arr = json_decode($condition, true);
        if ($type == 1)
            return '下载注册好省';

        $str = '';
        if (array_key_exists('colonel_one', $arr)) {
            $str .= '有效一级≥' . $arr['colonel_one'] . '人';
        }
        if (array_key_exists('colonel_two', $arr)) {
            if (!empty($str))
                $str .= ',';
            $str .= '有效二级≥' . $arr['colonel_two'] . '人';
        }
        if (array_key_exists('colonel_one_and_two', $arr)) {
            if (!empty($str))
                $str .= ',';
            $str .= '有效一级和二级≥' . $arr['colonel_one_and_two'] . '人';
        }
        return $str;

    }

    /**
     * 佣金提交
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function partnerFeeCfgSubmit(Request $request)
    {
        $rel = FeeCfg::setRow($request->input());
        if (!$rel) {
            return $this->responseJson(self::Response_failed_code, FeeCfg::Error());
        }
        return $this->responseJson(self::Response_success_code, '提交成功');
    }

    /**
     * 合伙人佣金设置
     * @param Content $content
     * @param Request $request
     * @return string
     */
    public function createPartnerFeeCfg(Content $content, Request $request)
    {
        $id     = $request->input('id', 0);
        $select = FeeCfg::Condition['register'];
        $info   = FeeCfg::find($id);
        $data   = ['title' => $id ? '编辑' : '新增', 'info' => $info, 'arr' => $select];
        return $content
            ->header("合伙人佣金设置")
            ->description('description')
            ->body(view('admin.FeeCfg.create', $data))->render();
    }

    /**
     * 团长，高级团长佣金设置
     * @param Content $content
     * @param Request $request
     * @return string
     */
    public function createCommanderFeeCfg(Content $content, Request $request)
    {
        $id           = $request->input('id', 0);
        $type         = $request->input('type', 2);
        $select1      = array_slice(FeeCfg::COL_Level, 0, 2, true);
        $select2      = FeeCfg::COL_Level;

        $symbol       = FeeCfg::Symbol;
        $info         = FeeCfg::find($id);
        if($info)
            $type=$info['type'];
        $conditionArr = json_decode($info['condition'], true);
        $current      = $end = '';
        if ($conditionArr) {
            $current = key($conditionArr);
            $info['num1']=reset($conditionArr);
            end($conditionArr);
            $end = key($conditionArr);
            $info['num2']=end($conditionArr);
        }
        if ($info['extends_json']) {
            $info['rate_arr'] = json_decode($info['extends_json'], true);
        }
        if($type==3){
            foreach ($select2 as &$item) {
                $item=$item.'团长';
            }
            unset($item);
            foreach ($select1 as &$value) {
                $value=$value.'团长';
            }
            unset($value);
        }
        $data = ['title' => $id ? '编辑' : '新增','type'=>$type, 'info' => $info,'current'=>$current,'end'=>$end, 'select1' => $select1, 'select2' => $select2, 'symbol' => $symbol];
        $view =$type==2?'admin.FeeCfg.commander':'admin.FeeCfg.senior';
        return $content
            ->header($type == 2 ? '团长佣金设置' : '高级团长佣金设置')
            ->description('description')
            ->body(view($view, $data))->render();
    }

    public function commanderFeeCfgSubmit(Request $request)
    {
        $rel = FeeCfg::setCommanderRow($request->input());
        if (!$rel) {
            return $this->responseJson(self::Response_failed_code, FeeCfg::Error());
        }
        return $this->responseJson(self::Response_success_code, '提交成功');
    }

}

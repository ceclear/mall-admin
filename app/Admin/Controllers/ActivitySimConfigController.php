<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\RechargeDisableAlert;
use App\GlobalConstant;
use App\Http\Controllers\Controller;
use App\Models\ActivityInvitationPrizeLog;
use App\Models\ActivitySimConfig;
use App\Models\SaleRank;
use App\Models\SalesTop;
use App\Models\UsersInfo;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Response;

class ActivitySimConfigController extends Controller
{

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'ActivitySimConfig';

    public function saleRank(Content $content)
    {
        $grid = new Grid(new SalesTop());
        $grid->column("id", "ID");
        $grid->column("uid", "UID");
        $grid->column("phone", "手机号")->display(function () {
            $info = UsersInfo::where('id', $this->uid)->first();

            return $info ? $info->phone : '[假数据]';
        });
        $grid->column("nickname", "昵称")->display(function () {
            $info = UsersInfo::where('id', $this->uid)->first();

            return $info ? $info->phone : '[假数据]';
        });
        $grid->column("sale_price", "销售金额")->sortable();
        $grid->column('status', '状态')->display(function ($status) {
            return SalesTop::$status[$status];
        });
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();

            $actions->append(new RechargeDisableAlert($actions->row->id, $this->row->status));
        });
        $grid->disableCreateButton();
        $grid->disableFilter();

        return $content->body($grid);
    }

    public function rechargeRefund(Request $request)
    {
        $id = $request->id;

        DB::beginTransaction();
        try {
            SalesTop::where('id', $id)->update(['status' => 1]);

            DB::commit();

            return Response::create(json_encode([
                'code' => 0,
                'msg' => 'success'
            ]), 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return Response::create(json_encode([
                'code' => 1,
                'msg' => '操作失败'
            ]), 200);
        }
    }

    public function invitationLog(Content $content)
    {
        $grid = new Grid(new ActivityInvitationPrizeLog());
        $grid->model()->orderBy("id", "desc");
        $grid->column("phone", "手机号");
        $grid->column("prize_num", "抽奖次数");
        $grid->column("desc", "中奖奖品");
        $grid->column("hf_amount", "累计话费");
        $grid->column("created_at", "创建时间");
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();
        });
        $grid->filter(function ($filter) {

            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->equal('phone', '手机号');
            $filter->equal('uid', 'uid');
            $filter->equal('prize', '奖品')->select(function () {
                return ActivitySimConfig::getInvitationMap();
            });
        });
        $grid->disableCreateButton();
        $grid->disableExport();
        return $content->body($grid);
    }

    public function invitation(Content $content)
    {
        $invitation = ActivitySimConfig::getRow(ActivitySimConfig::TYPE_INVITATION);
        $form = new Form($invitation);
        $form->datetime("st", "开始时间")->value($invitation->st);
        $form->datetime("et", "截止时间")->value($invitation->et);
        foreach ($invitation->getExtendJson(ActivitySimConfig::EXTENDS_JSON_KEY_RULE) as $key => $item) {
            $form->fieldset($item["desc"], function (Form $form) use ($key, $item) {
                $form->text("json[" . $key . "][count_after_must]", "抽奖必中次数")->value($item["count_after_must"]);
                $form->text("json[" . $key . "][probability]", "中奖概率")->value($item["probability"]);
                $form->hidden("json[" . $key . "][desc]")->value($item["desc"]);
            });
        }
        $form->UEditor("rules_desc", "活动规则")->value($invitation->rules_desc);
        $form->switch('status', '状态')->value($invitation->status);
        $form->setAction("/admin/activitySimConfigs/setInvitation");
        return $content->body($form);
    }

    public function Sale(Content $content)
    {
        $invitation = ActivitySimConfig::getRow(ActivitySimConfig::TYPE_SALE_RANKING_LIST);
        $request = \request();
        if ($request->isMethod("post")) {
            $invitation->st = $request->post("st");
            $invitation->et = $request->post("et");
            $invitation->rules_desc = $request->post("rules_desc");
            $invitation->status = $request->post("status");
            if ($invitation->status == "off") {
                $invitation->status = GlobalConstant::IS_NO;
            } else {
                $invitation->status = GlobalConstant::IS_YES;
            }
            if ($invitation->save()) {
                return admin_success("操作成功");
            }
            return admin_error("操作失败");
        }
        $form = new Form($invitation);
        $form->datetime("st", "开始时间")->value($invitation->st);
        $form->datetime("et", "截止时间")->value($invitation->et);
        $form->UEditor("rules_desc", "活动规则")->value($invitation->rules_desc);
        $form->switch('status', '状态')->value($invitation->status);
        $form->setAction("/admin/activitySimConfigs/sale");
        return $content->body($form);
    }

    public function setInvitation(Request $request)
    {
        if ($request->isMethod("get")) {
            return redirect(admin_url("/activitySimConfigs/invitation"));
        }
        $invitation = ActivitySimConfig::getRow(ActivitySimConfig::TYPE_INVITATION);
        $invitation->st = $request->post("st");
        $invitation->et = $request->post("et");
        $invitation->rules_desc = $request->post("rules_desc");
        $invitation->status = $request->post("status");
        if ($invitation->status == "off") {
            $invitation->status = GlobalConstant::IS_NO;
        } else {
            $invitation->status = GlobalConstant::IS_YES;
        }
        $json = $request->post("json");
        if ($json) {
            $invitation->setExtendJson(ActivitySimConfig::EXTENDS_JSON_KEY_RULE, $request->post("json"));
        } else {
            $invitation->initExtendsJson();
        }
        if ($invitation->save()) {
            return admin_success("操作成功");
        }
        return admin_error("操作失败");
    }
}

<?php

namespace App\Admin\Controllers;


use App\Admin\Actions\LinkHref;
use App\Admin\Actions\UpdateBalance;
use App\Http\Controllers\Controller;
use App\Models\UsersBalanceLog;
use App\Models\UsersGroup;
use App\Models\UsersInfo;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;


class UserController extends Controller
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

//        $grid = new Grid(new UsersInfo);
//        $grid->model()->orderBy('id', 'desc');
//        $grid->disableRowSelector();
//
//        $grid->number('序号');
//        $grid->rows(function ($row, $number) {
//            $row->column('number', $number+1);
//        });
//        $field=['users_info.id as id','users_info.pid as pid','users_info.created_at','users_info.updated_at','phone','level','parents_num','users_info.upgrade_time','parents_num','children_num'];
//        $grid->model()->leftJoin('users_group','users_info.id','users_group.id')
//            ->select($field)->paginate(20);
//        $grid->column('id','链信ID')->sortable();
//        $grid->column('phone','手机号');
//        $grid->column('inviteUser.phone','邀请人手机号');
//        $grid->column('level','等级');
//        $grid->column('children_num','团队总人数');
//        $grid->column('parents_num','父级人数')->sortable();
//        $grid->column('created_at','注册时间');
//        $grid->column('upgrade_time','用户升级时间');
//        $grid->column('updated_at','上次更改时间');
//        $grid->filter(function ($filter) {
//
//            // 去掉默认的id过滤器
//            $filter->disableIdFilter();
//            // 在这里添加字段过滤器
//            $filter->like('phone', '手机号');
//            $filter->like('inviteUser.phone', '邀请人手机号');
//            $filter->equal('level', '等级')->select(function () {
//                return UsersInfo::getSourceMap();
//            });
//            $filter->date('created_at', '注册时间');
//            $filter->date('upgrade_time', '用户升级时间');
//
//        });
//        $grid->disableCreateButton();
//        $grid->disableExport();
        return $content
            ->header('省钱用户管理')
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
        $grid = new Grid(new UsersInfo);
        $grid->model()->orderBy('id', 'desc');
        $grid->disableRowSelector();

//        $grid->number('序号');
//        $grid->rows(function ($row, $number) {
//            $row->column('number', $number+1);
//        });
        $grid->id('UID')->sortable();
        $grid->phone('手机号');
        $grid->level('等级');
        $grid->column('userBalance.balance','余额');
        $grid->column('userBalance.freeze_balance','冻结余额');
        $grid->column('zy_num', '直邀人数')->display(function () {
            return UsersGroup::where('pid', $this->id)->count();
        });
        $grid->column('jy_num', '间邀人数')->display(function () {
            return UsersGroup::where('ppid', $this->id)->count();
        });
        $grid->column('children_num', '团队总人数')->display(function () {
            $info = UsersGroup::find($this->id,['children_num']);
            return $info['children_num'];
        });
        $grid->column('groupParentsNum.parents_num','父级数');
        $grid->column('pid','pid');
        $grid->column('ppid','ppid');
        $grid->column('relationUser.relation_id','渠道关系ID');
        $grid->column('relationUser.special_id','淘宝用户special_id');
        $grid->column('inviteUser.phone', '邀请人手机号');
        $grid->column('wx_openid', 'openid');
        $grid->column('exclusive_code_invite', '省钱邀请专属口令');
        $grid->created_at('注册时间')->sortable();
        $grid->login_times('上次登陆时间')->sortable();
        $grid->first_login_times('首次登陆时间')->sortable();
        $grid->upgrade_time('用户升级时间')->sortable();
        $grid->updated_at(trans('admin.updated_at'));
        $grid->setActionClass(Grid\Displayers\DropdownActions::class);
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
            $actions->disableEdit();
            if(Admin::user()->can('财务')){
                $actions->add(new UpdateBalance());
            }
            $actions->add(new LinkHref());
        });
        $grid->filter(function ($filter) {

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            $filter->column(1/3, function ($filter) {
                $filter->equal('id', 'UID');

                $filter->equal('phone', '手机号');
                $filter->equal('inviteUser.phone', '邀请人手机号');
//                $filter->where(function ($query) {
//                    switch ($this->input) {
//                        case '2':
//                            $query->whereNotNull('first_login_times');
//                            break;
//                        case '1':
//                            $query->whereNull('first_login_times');
//                            break;
//                    }
//                }, '状态', '')->select([
//                    1=>'无效',
//                    2=>'有效'
//                ]);
                $filter->equal('is_valid','状态')->select(function (){
                    return [0=>'无效',1=>'有效'];
                });
                $filter->equal('pid', 'pid');

            });

            $filter->column(1/3, function ($filter) {

                $filter->equal('level', '等级')->select(function () {
                    return UsersInfo::getSourceMap();
                });
                $filter->equal('exclusive_code_invite', '专属口令');
                $filter->between('created_at', '注册时间')->datetime();
                $filter->group('userBalance.freeze_balance','冻结余额',function ($group) {
                    $group->gt('大于');
                });
            });

            $filter->column(1/3, function ($filter) {


                $filter->between('upgrade_time', '上次升级时间')->datetime();
                $filter->between('login_times', '上次登录时间')->datetime();
                $filter->between('first_login_times', '首次登录时间')->datetime();
                $filter->equal('ppid', 'ppid');
            });

            // 在这里添加字段过滤器



        });
        $grid->disableCreateButton();
        $grid->disableExport();
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
        $show = new Show(UsersInfo::findOrFail($id));

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
        $form = new Form(new UsersInfo);

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
        $rel   = UsersInfo::where('id', $id)->update(['status' => $value]);
        if ($rel)
            return $this->responseJson(1);
        return $this->responseJson(0);
    }

    protected function parentGrid(Content $content)
    {
        $grid = new Grid(new UsersGroup());
        $grid->model()->orderBy('parents_num', 'desc');
        $grid->disableRowSelector();

        $grid->number('序号');
        $grid->rows(function ($row, $number) {
            $row->column('number', $number+1);
        });
        $grid->id();
        $grid->pid();
        $grid->ppid();
        $grid->parents_num('父级数量');
        $grid->children_num();
        $grid->updated_at(trans('admin.updated_at'));
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
            $actions->disableEdit();
        });

        $grid->disableCreateButton();
        $grid->disableExport();

        return $content
            ->header('省钱用户管理父级')
            ->description(trans('admin.description'))
            ->body($grid);
    }

    protected function showLogs(Content $content)
    {
        $grid = new Grid(new UsersBalanceLog());
        $grid->model()->orderBy('created_at', 'desc');
        $grid->disableRowSelector();

        $grid->id();
        $grid->uid();
        $grid->amount('增加金额');
        $grid->amount_before_change('变更前金额');
        $grid->remark('备注');
        $grid->operation_log('操作日志');
        $grid->tag('日志标签');
        $grid->updated_at(trans('admin.updated_at'));
        $grid->actions(function ($actions) {

            $actions->disableView();
            $actions->disableDelete();
            $actions->disableEdit();
        });
        $grid->filter(function ($filter) {

            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->equal('uid', 'UID');
            // 在这里添加字段过滤器



        });

        $grid->disableCreateButton();
        $grid->disableExport();

        return $content
            ->header('用户余额日志')
            ->description(trans('admin.description'))
            ->body($grid);
    }

}

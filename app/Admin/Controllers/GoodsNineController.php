<?php

namespace App\Admin\Controllers;


use App\Admin\Actions\EditTop;
use App\Admin\Extensions\EnableRow;
use App\Http\Controllers\Controller;
use App\Models\GoodsCat;
use App\Models\NineGoods;
use App\Services\DingDanXia;
use Carbon\Carbon;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class GoodsNineController extends Controller
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
            ->header('9.9包邮管理')
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
        $grid = new Grid(new NineGoods());
        $grid->model()->orderBy('updated_at', 'desc')->orderBy('id', 'desc');
        $grid->disableRowSelector();
        $grid->id('ID');
        $grid->column('title', '商品名称')->display(function ($text) {
            return str_limit($text, 15);
        });
        $grid->gid('商品ID');
        $grid->column('cat_id_one', '分类')->display(function ($v) {
            return NineGoods::catShow($v);
        });
        $grid->coupon_amount('优惠券面额');
        $grid->zk_final_price('折扣价');
        $grid->qh_final_commission('券后佣金');
        $grid->commission_rate('佣金比例');
        $grid->shop_name('店铺名称');
        $grid->qh_final_price('券后价')->sortable();;
        $grid->coupon_end_time('优惠券截止时间');
//        $grid->column('sort','排序')->sortable()->editable();
        $grid->column('top', '顶置')->display(function ($v) {
//            return $v==0?'<span class="label label-primary">否</span>':'<span class="label label-danger">第'.$v.'位</span>';
            return $v == 0 ? '<span class="label label-primary">否</span>' : '<span class="label label-danger">是</span>';
        })->sortable();
        $grid->column('top_time', __('置顶时间'))->display(function ($v) {
            return $v == 0 ? 0 : date('Y-m-d H:i:s', $v);
        })->sortable();
        $grid->status('状态')->display(function ($status) {
            return $status == 1 ? '正常' : '禁用';
        })->label([1 => 'info', 0 => 'danger']);
        $grid->created_at(trans('admin.created_at'));
        $grid->updated_at(trans('排序时间'))->sortable();
//        $grid->setActionClass(Grid\Displayers\DropdownActions::class);
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableEdit();
//            $actions->add(new EditTop());
            $changeStatus = $actions->row['top'] == 1 ? 0 : 1;
            $changeShow   = $actions->row['top'] == 1 ? '取消' : '置顶';
            $changeIcon   = $actions->row['top'] == 1 ? 'fa-hand-o-down' : 'fa-hand-o-up';
            $actions->append(new EnableRow($actions->getKey(), 'nine-goods-top', $changeStatus, $changeShow, $changeIcon));//自定义操作
        });
        $grid->filter(function ($filter) {

            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->equal('gid', '商品ID');
            $filter->like('title', '商品名称');
            $filter->equal('cat_id_one', '分类')->select(NineGoods::NineCat());
            $filter->equal('top', '顶置')->select(['否', '是']);
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
        $show = new Show(NineGoods::findOrFail($id));

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
        $form   = new Form(new NineGoods());
        $catRet = Cache::get('tbk_cat');
        $catRet = array_column($catRet, "name", "cid");
        $form->select("cat_id_one", "商品分类")->options($catRet);
        $form->disableReset(true);
        $form->text('sort', '排序');
//        $form->setAction("nine-goods-collection");
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
        $info             = NineGoods::find($id);
        $info->top        = $value;
        $info->top_time   = $value == 1 ? time() : null;
        $info->timestamps = false;
        $rel              = $info->save();
        if ($rel)
            return $this->responseJson(1, '操作成功');
        return $this->responseJson(0, '操作失败');
    }

    public function collectionNineGoods(Request $request)
    {
        $cat_id = $request->input('cat_id_one', 0);
        $ddx    = new DingDanXia();
        $rel    = $ddx->getNineGoods(NineGoods::EVERY_NUM, $cat_id);
        if ($rel) {
            foreach ($rel as $item) {
                $item->save();
            }
        }
        admin_success("操作成功");
        return back();
    }
}

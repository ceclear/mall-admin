<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\EnableRow;
use App\Http\Controllers\Controller;
use App\Models\AdPosition;
use App\Models\Advert;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;


class AdvertController extends Controller
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
            ->header('广告设置')
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
        $grid = new Grid(new Advert());
        $grid->disableRowSelector();
        $grid->id('ID');
        $grid->skip_type('跳转类型')->display(function ($type) {
            switch ($type) {
                case 1:
                    return '跳转活动页';
                    break;
                case 2:
                    return '点击跳转';
                    break;
                default:
                    return 'app内跳转';
                    break;
            }
        });
        $grid->product_id('数据ID');
        $grid->name(__('name'));
        $grid->column('ad_position.name', '广告位置');
//        $grid->img_url('图片')->image(env('OSS_HTTP'), 100, 100);
//        $grid->column('img_url','图片')->image('',$width = 100,  $height = 100);
        $grid->column('img_url','图片')->display(function ($pictures) {
            if(stripos($pictures,'http')===false){
                return "";
            }
            return $pictures;

        })->image('', 30, 30);
        $grid->url('跳转链接');
        $grid->sort('排序');
        $grid->status('状态')->display(function ($status) {
            return $status == 1 ? '正常' : '禁用';
        })->label([1 => 'info', 0 => 'danger']);
        $grid->created_at(trans('admin.created_at'));
        $grid->updated_at(trans('admin.updated_at'));
        $grid->actions(function ($actions) {
            $actions->disableView();
//            $actions->disableDelete();
            $changeStatus = $actions->row['status'] == 1 ? 0 : 1;
            $changeShow   = $actions->row['status'] == 1 ? '禁用' : '启用';
            $changeIcon   = $actions->row['status'] == 1 ? 'fa-remove' : 'fa-check';
            $actions->append(new EnableRow($actions->getKey(), 'ad-status', $changeStatus, $changeShow, $changeIcon));//自定义操作
        });
        $grid->disableFilter();
        $grid->disableExport();
        if (\Illuminate\Support\Facades\Request::has('type')) {
            $grid->model()->where('pos_id', \Illuminate\Support\Facades\Request::input('type'));
        }
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append('<a href="/admin/ad-position" class="btn btn-sm btn-info">返回广告位管理</a>');
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
        $show = new Show(AdPosition::findOrFail($id));

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
        $form = new Form(new Advert());

        $form->display('id', 'ID');
        $form->select('skip_type', '跳转类型')->options(Advert::SKIP_TYPE)->required();
        $form->text('product_id', '数据ID')->attribute('placeholder="只有APP内部跳转才填"');
        $form->text('name', 'name')->attribute('placeholder="可不填"');
        $form->select('pos_id', '位置')->options(
            AdPosition::all()->pluck('name','id')
        )->required();
        $form->image('img_url','图片')->required();
        $form->url('url', '跳转链接')->required();
        $form->number('sort', '排序')->default(1)->min(1);
        $form->color('bg_color','背景色')->placeholder('可不填')->attribute(['autocomplete'=>'off']);
        $form->switch('status', '状态')->default(1);
//        $form->display('created_at', trans('admin.created_at'));
//        $form->display('updated_at', trans('admin.updated_at'));
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
        $rel   = Advert::where('id', $id)->update(['status' => $value]);
        if ($rel)
            return $this->responseJson(1);
        return $this->responseJson(0);
    }


}

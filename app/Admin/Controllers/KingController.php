<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\DeleteRow;
use App\Admin\Extensions\EditRow;
use App\Admin\Extensions\EnableRow;
use App\GlobalConstant;
use App\Http\Controllers\Controller;
use App\Models\AdPosition;
use App\Models\Advert;
use App\Models\King;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;


class KingController extends Controller
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
            ->header('金刚区设置')
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
        $grid = new Grid(new King());
        $grid->model()->orderBy('id','desc');
        $grid->disableRowSelector();
        $grid->id('ID');
        $grid->title('文案');
        $grid->name('页面标题');
        $grid->skip('跳转板块')->display(function ($type) {
            return GlobalConstant::getKingMap($type);
        });
        $grid->column('icon', __('图标'))->lightbox(['width' => 100, 'height' => 40]);
        $grid->url('跳转链接');
        $grid->sort('排序');
        $grid->status('状态')->display(function ($status) {
            return $status == 1 ? '正常' : '禁用';
        })->label([1 => 'info', 0 => 'danger']);
        $grid->created_at(trans('admin.created_at'));
        $grid->updated_at(trans('admin.updated_at'));
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->append(new DeleteRow($actions->getKey(), 'king', '', 1, '', 'fa-trash'));
            $actions->append(new EditRow($actions->getKey(), 'king', '', 1, '', 'fa-edit'));
            $changeStatus = $actions->row['status'] == 1 ? 0 : 1;
            $changeShow   = $actions->row['status'] == 1 ? '' : '';
            $changeIcon   = $actions->row['status'] == 1 ? 'fa-toggle-on' : 'fa-toggle-off';
            $actions->append(new EnableRow($actions->getKey(), 'king-status', $changeStatus, $changeShow, $changeIcon));//自定义操作
        });
        $grid->disableFilter();
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
        $show = new Show(King::findOrFail($id));

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
        $form = new Form(new King());

        $form->display('id', 'ID');
        $form->text('title', '文案')->attribute(['autocomplete'=>'off']);
        $form->text('name', '页面标题')->attribute(['autocomplete'=>'off'])->placeholder('可不填');
        $form->select('skip', '跳转板块')->options(GlobalConstant::getKingMap());
        $form->url('url', '跳转链接')->placeholder('可不填');
        $form->image('icon', '图标');
        $form->number('sort', '排序')->min(1)->default(1);
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
        $rel   = King::where('id', $id)->update(['status' => $value]);
        if ($rel)
            return $this->responseJson(1);
        return $this->responseJson(0);
    }


}

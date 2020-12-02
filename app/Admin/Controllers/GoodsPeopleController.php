<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\EnableRow;
use App\Http\Controllers\Controller;
use App\Models\GoodsPeople;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;


class GoodsPeopleController extends Controller
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
            ->header('爆款管理')
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
            ->header('编辑爆款')
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
            ->header('新建爆款')
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
        $grid = new Grid(new GoodsPeople());
        $grid->model()->orderBy('id','desc');
        $grid->disableRowSelector();
        $grid->id('ID');
        $grid->gid();

        $grid->column('pic', '动图')->lightbox(['width' => 100, 'height' => 100]);
        $grid->column('video', '视频')->display(function ($v) {
            return "<video src='" . $v . "' controls  preload='auto' style='width: 130px;height: 100px' class='vid'></video>";
        });
        $grid->extends_json('评论数量')->display(function ($v) {
            $v=json_decode($v,true);
            return $v['comments']['comment_num'] ?? 0;
        })->expand(function ($model)  {
        return GoodsPeople::showArrExtends(['昵称', '评论'], $model);
    });
        $grid->sort('排序');
        $grid->status('状态')->display(function ($status) {
            return $status == 1 ? '正常' : '禁用';
        })->label([1 => 'info', 0 => 'danger']);
        $grid->created_at(trans('admin.created_at'));
        $grid->updated_at(trans('admin.updated_at'));
        $grid->disableExport();
        $grid->filter(function ($filter) {

            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->equal('gid', '商品ID');
            $filter->equal('status', '状态')->select(function () {
                return ['禁用', '正常'];
            });
        });
        $grid->actions(function ($actions) {
//            $actions->disableDelete();
            $changeStatus = $actions->row['status'] == 1 ? 0 : 1;
            $changeShow   = $actions->row['status'] == 1 ? '' : '';
            $changeIcon   = $actions->row['status'] == 1 ? 'fa-toggle-on' : 'fa-toggle-off';
            $actions->append(new EnableRow($actions->getKey(), 'goods-people-status', $changeStatus, $changeShow, $changeIcon));//自定义操作
        });
        $grid->expandFilter();
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
        $show = new Show(GoodsPeople::findOrFail($id));

        $show->id('ID');
        $show->gid('gid');
        $show->pic('图片')->image();
        $show->video('视频');
        $show->comment_json('评论')->unescape()->as(function ($v) {
            return "<pre>{$v}</pre>";
        });
        $show->status('状态')->as(function ($v) {
            return $v == 1 ? '正常' : '禁用';
        });
        $show->sort('排序');
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
        $form = new Form(new GoodsPeople());

        $form->display('id', 'ID');
        $form->text('gid')->attribute('autocomplete="off"')->required();
        $form->image('pic', '动图')->required();
        $form->file('video', '视频')->required();
        $form->number('sort', '排序')->required()->min(0)->default(0);
        $form->switch('status', '状态')->default(1);
        $form->textarea('extends_json','评论')->rows(50)->customFormat(function ($value) {
            $arr     = json_decode($value, true);
            $comment = $arr['comments']['comment'];
            $comment = array_column($comment, 'content');
            $comment = implode("\r\n", $comment);
            return $comment;
        })->placeholder('必填,换行即一条')->required();
        $form->saving(function (Form $form) {
            $v    = str_replace("\r\n", ' ', $form->extends_json);
            $arr  = explode(' ', $v);
            $json = [];
            foreach ($arr as $item) {
                if ($item) {
                    $data['nickname'] = GoodsPeople::createNickName();
                    $data['content']  = $item;
                    $json[]           = $data;
                }
            }
            $model = $form->model();
            $model->setExtendJson('comments', ['comment_num' => count($arr), 'comment' => $json]);
            $form->extends_json = $model->extends_json;
            if(!$form->file('video')){
                $form->video=$model->video;
            }
        });

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
        $rel   = GoodsPeople::where('id', $id)->update(['status' => $value]);
        if ($rel)
            return $this->responseJson(1);
        return $this->responseJson(0);
    }


}

<?php


namespace App\Admin\Controllers;


use App\GlobalConstant;
use App\Http\Controllers\Controller;
use App\Models\GoodsCat;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;

class GoodsCatController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content, Request $request)
    {
        $grid = new Grid(new GoodsCat());
        $grid->model()->when(true, function($query) use ($request){
            if ( !$request->get("id") && !$request->get("pid") ) {
                $query->where("level", 1);
            }
        })->orderBy("sort", "asc");
        $grid->column("id", "ID")->sortable();
        $grid->column('icon','图标')->display(function ($pictures) {
            if(stripos($pictures,'http')!==false){
                return "";
            }
            return $pictures;

        })->image('', 30, 30);
        $grid->column("name", "分类");
        $grid->column("sort", "排序");
        $grid->column("status", "状态")->display(function($status){
            return $status == GlobalConstant::IS_YES ? "正常" : "禁用";
        });
        $grid->column("is_grab", "采集状态")->display(function($status){
            return $status == GlobalConstant::IS_YES ? "采集" : "不采集";
        });
        $grid->column("created_at", "创建时间");
        $grid->column("updated_at", "修改时间");
        $grid->filter(function(Grid\Filter $filter){
            $filter->equal("pid", "父级id");
            $filter->like("name", "分类名称");
        });
        $grid->actions(function(Grid\Displayers\Actions $actions){
            $actions->disableView();
            if ( $actions->row->level == 1 ) {
                $url = "goods-cat?pid=" . $actions->row->getKey();
                $actions->append('<a href="'.$url.'"><i class="fa fa-eye"></i>下级分类</a>');

            }
        });/*->tools(function(Grid\Tools $tools){
            $tools->append(new ButtonJump("/", "跳转"));
        });*/
        return $content
            ->header('商品分类')
            ->description(trans('admin.description'))
            ->body($grid);
    }

    public function create(Content $content)
    {
        return $content
            ->header(trans('admin.create'))
            ->description(trans('admin.description'))
            ->body($this->form());
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
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new GoodsCat);
        $form->display("id", "ID");
        $parentCat = GoodsCat::where("level", 1)->select(["id", "name"])->get()->toArray();
        $parentCat = array_column($parentCat, "name", "id");
        $form->select("pid", "父级分类 【不选则为顶级分类】")->options($parentCat)->default(0);
        $form->text("name", "分类名称")->required();
        $form->number("sort", "排序")->min(0)->default(100);
        $form->radio("status", "状态")->options(GoodsCat::getStatusMap())->default(GlobalConstant::IS_YES);
        $states = [
            'on'  => ['value' => 1, 'text' => '采集', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => '关闭', 'color' => 'danger'],
        ];

        $form->switch('is_grab', '采集')->states($states)->default(1);
        $form->image('icon','图标');
        $form->hidden("level")->default(1);
        //临时的
        $form->saving(function(Form $form){
            if ( !$form->pid ) {
                $form->pid = 0;
            }
            if ( $form->isCreating() ||  $form->model()->isDirty("pid") ) {
                if ( $form->pid > 0 ) {
                    $pCat = GoodsCat::where("id", $form->pid)->where("level", 1)->select("level")->first();
                    if ( !$pCat ) {
                        return back()->with([
                            new MessageBag([
                                "message" => "无效的父级分类"
                            ])
                        ]);
                    }
                    $form->level = $pCat->level + 1;
                } else {
                    $form->level = 1;
                }
            }
        });
        return $form;
    }

    public function getCat(Request $request)
    {
        $q = $request->input("q");
        return  GoodsCat::where("pid", $q)->select(DB::raw("id, name as text"))->orderBy("sort", "asc")->get();
    }
}
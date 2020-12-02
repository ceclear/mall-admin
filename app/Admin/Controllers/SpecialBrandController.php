<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\SpecialBrandTop;
use App\Admin\Extensions\EnableRow;
use App\Admin\Extensions\SpecialBrandOption;
use App\Models\Goods;
use App\Models\SpecialBrand;
use App\Models\SpecialTyper;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class SpecialBrandController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\SpecialBrand';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SpecialBrand());
        $grid->model()->latest();
        $grid->column('id', __('Id'))->sortable();
        $grid->column('title', __('专场名称'));
        $grid->column('logo', "LOGO")->display(function ($logo) {
            if (stripos($logo, "http") === false) {
                return "";
            }
            return $logo;
        })->image("", 50, 50);

        $grid->column('seckill_time_start', __('秒杀开始时间'));
        $grid->column('seckill_time_end', __('秒杀结束时间'));
        $grid->column('getSpecialType.title', __('专场类型'));
//        $grid->column('goods_id', __('商品ID'))->display(function ($goods_id) {
//            return implode(',',$goods_id);
//        });
        $grid->column('goods_id', __('商品ID'))->display(function ($goods_id) {
            $goods_id = explode(',', $goods_id);
            $gArr = array_chunk($goods_id, 5);
            $c = count($gArr);
            $t = '';
            foreach ($gArr as $k => $g) {
                if (($k + 1) < $c) {
                    $t .= implode(',', $g) . "<br/>";
                } else {
                    $t .= implode(',', $g);
                }
            }
            return $t;
        });
        $grid->column('min_price', __('最低价格'));
        $grid->column('min_discount', __('最低折扣'));
        $grid->column('sort', __('排序'))->sortable()->replace(['' => '-']);
        $grid->column('is_top', __('是否置顶'))->display(function ($is_top) {
            return $is_top ? '是' : '否';
        });
        $grid->column('status', __('状态'))->display(function ($status) {
            switch ($status) {
                case 1:
                    return '发布';
                    break;
                case 2:
                    return '保存';
                    break;
                case 3:
                    return '关闭';
                    break;
                default:
                    return '';
                    break;
            }
        });
        $grid->column('created_at', __('创建时间'));
        $grid->column('updated_at', __('更新时间'));

        // filter($callback)方法用来设置表格的简单搜索框
        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->like('title', '专场名称');
            $filter->equal('status', '状态')->select(['1' => '发布', '2' => '保存', '3' => '关闭']);
            $filter->equal('getSpecialType.special_type_id', '专场类型')->select(function () {
                return SpecialTyper::all()->pluck('title', 'id');
            });
            $filter->between('seckill_time_start', '秒杀开始时间')->date();
            $filter->between('seckill_time_end', '秒杀结束时间')->date();
        });

        $grid->actions(function ($actions) {
            $changeStatus = $actions->row['is_top'] == 1 ? 0 : 1;
            $changeShow = $actions->row['is_top'] == 1 ? '取消置顶' : '置顶';
            $changeIcon = $actions->row['is_top'] == 1 ? 'fa-hand-o-down' : 'fa-hand-o-up';
            $actions->append(new EnableRow($actions->getKey(), 'change-top', $changeStatus, $changeShow, $changeIcon));//自定义操作
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
        $show = new Show(SpecialBrand::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('专场名称'));
        $show->field('logo', __('Logo'));
        $show->field('seckill_time_start', __('秒杀开始时间'));
        $show->field('seckill_time_end', __('秒杀结束时间'));
        $show->field('special_type_id', __('专场类型'))->as(function ($special_type_id) {
            return SpecialTyper::where('id', $special_type_id)->pluck('title');
        })->label();

//        $show->field('goods_id', '商品ID')->as(function ($goods_id) {
//            $goodsList = Goods::find($goods_id);
//            if ($goodsList) {
//                $keyed = $goodsList->mapWithKeys(function ($item) {
//                    return [$item['id'] => $item['id']];
//                });
//
//                return $keyed->all();
//            }
//        })->label();
        $show->field('goods_id', __('商品ID'));

        $show->field('min_price', __('最低价格'));
        $show->field('min_discount', __('最低折扣'));
        $show->field('sort', __('排序'));
        $show->field('status', __('状态'))->as(function ($status) {
            switch ($status) {
                case 1:
                    return '发布';
                    break;
                case 2:
                    return '保存';
                    break;
                case 3:
                    return '关闭';
                    break;
                default:
                    return '';
                    break;
            }
        })->label();
        $show->field('created_at', __('创建日期'));
        $show->field('updated_at', __('修改日期'));


        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new SpecialBrand());

        $form->text('title', __('专场名称'))->rules('required');
        $form->image('logo', __('Logo'))->uniqueName()->absolutePath(true)->rules('required');
        $form->datetimeRange('seckill_time_start', 'seckill_time_end', __('秒杀时间'));
        $specialTyper = SpecialTyper::all()->pluck('title', 'id');
        $form->select('special_type_id', __('专场类型'))->options($specialTyper)->rules('required');


//        $form->multipleSelect('goods_id','商品ID-自己表的ID')->options(function ($goods_id) {
//            $goodsList = Goods::find($goods_id);
//            if ($goodsList) {
//                $keyed = $goodsList->mapWithKeys(function ($item) {
//                    return [$item['id'] => $item['title'] . '-' . $item['id']];
//                });
//                return $keyed->all();
//            }
//        })->ajax('/admin/brand-f-b/special-brands/get-goods');

        $form->textarea('goods_id', __('商品ID'))->rules('required');

        $form->text('sort', __('排序'))->placeholder('值越小越排在前面');
        $form->radio('status', __('状态'))->options(['1' => '发布', '2' => '保存', '3' => '关闭'])->default('2');#1=发布，2=保存；3=关闭
        $form->hidden('min_price');
        $form->hidden('min_discount');
        //保存前回调
        $form->saving(function (Form $form) {
            $goodsIdArr = explode(',', $form->goods_id);
            //  计算最低金额和最低折扣率
            $goodsIdArr = array_filter($goodsIdArr);
            $goodsInfo = Goods::whereIn('gid', $goodsIdArr)->select('reserve_price', 'zk_final_price', 'qh_final_price')->get();
            if ($goodsInfo->isEmpty()) {
                admin_error("商品不存在！");
                return back();
            }
            $zkFinalPrice = [];
            $discount = [];
            foreach ($goodsInfo as $value) {
//                array_push($discount,(round($value->zk_final_price/$value->reserve_price,2))*10);
                array_push($discount, number_format($value->qh_final_price / $value->zk_final_price * 10, 2));
                array_push($zkFinalPrice, $value->zk_final_price);
            }
            $minPrice = min($zkFinalPrice);
            $minDiscount = min($discount);
            $form->min_price = $minPrice;
            $form->min_discount = $minDiscount;
        });

        return $form;
    }

    public function getGoods(Request $request)
    {
        $q = $request->get('q');
        return Goods::where('id', '=', $q)->paginate(null, ['id', 'title as text']);
    }

    /**
     * 更改状态
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus(Request $request)
    {
        $id = $request->input('id', 0);
        $value = $request->input('value', 0);
        $info = SpecialBrand::find($id);
        $info->is_top = $value;
        $info->top_time = $value == 1 ? date('Y-m-d H:i:s') : null;
        $info->timestamps = false;
        $rel = $info->save();
        if ($rel) {
            return response()->json([
                "code" => 1,
                "msg" => '操作成功',
                "data" => []
            ]);
        }
        return response()->json([
            "code" => 0,
            "msg" => '操作失败',
            "data" => []
        ]);
    }
}

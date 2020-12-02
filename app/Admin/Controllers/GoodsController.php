<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\CeclearDelete;
use App\Admin\Extensions\DeleteRow;
use App\Admin\Extensions\EditRow;
use App\Admin\Extensions\EnableRow;
use App\GlobalConstant;
use App\Models\Goods;
use App\Models\GoodsCat;
use App\Models\GoodsPreview;
use App\Services\DingDanXia;
use App\TimestampBetween;
use Carbon\Carbon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoodsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\Goods';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */


    protected function grid()
    {
        $grid = new Grid(new Goods());
        $grid->model()->orderBy("id", "desc");
//        $grid->column('id', __('Id'));
        $grid->column('title', __('商品名称'))->display(function ($val) {
            return str_limit($val, 10);
        });
        $grid->column('show_title', '修改后商品名称')->editable('textarea');
        $grid->column('source', "来源")->using(Goods::getSourceMap());
        $grid->column('gid', __('Gid'));
        $grid->column('show_pic', __('主图'))->lightbox(['width' => 100, 'height' => 40]);
        $grid->column('catOne.name', "一级");
        $grid->column('catTwo.name', "二级");
        $grid->column('zk_final_price', __('商品折扣价'));
        $grid->column('coupon_amount', __('优惠券'));
        $grid->column('qh_final_price', __('劵后价'))->sortable();;
        $grid->column('partition', __('分区'))->display(function ($val) {
            return GlobalConstant::getSystemPartitionMap($val);
        });
        $grid->column('point_time','秒杀时间')->display(function ($v) {
            return $v == 0 ? '--' : date('Y-m-d', $v);
        })->sortable();
        $grid->column('point_id','场次')->display(function ($v){
            if($v){
                return GlobalConstant::getPointMap($v);
            }
            return '--';
        });

        $grid->column('qh_final_commission', __('券后佣金'));
        $grid->column('commission_rate', __('佣金比率'));
        $grid->column('shop_name', __('店铺'));
//        $grid->column('sort', __('排序'));
        $grid->column('is_auto', __('自动'))->display(function ($v) {
            return $v == 1 ? '<span class="label label-primary">手动</span>' : '<span class="label label-info">自动</span>';
        });
        $grid->column('video', __('视频'))->display(function ($v) {
            return empty($v) ? '<span class="label label-warning">无</span>' : '<span class="label label-default">有</span>';
        });
        $grid->column('sort_time', __('排序时间'))->display(function ($v) {
            return $v == 0 ? 0 : date('Y-m-d H:i:s', $v);
        })->sortable();
        $grid->column('created_at', __('创建时间'))->sortable();
        $grid->column('updated_at', __('修改时间'))->sortable();
        $grid->column('top_time', __('置顶时间'))->sortable();
        $grid->column('top', '顶置')->display(function ($v) {
            return $v == 0 ? '<span class="label label-primary">否</span>' : '<span class="label label-danger">是</span>';
        })->sortable();
        $grid->actions(function ($actions) {
         //   $actions->disableView();
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->append(new DeleteRow($actions->getKey(), 'goods', '', 1, '', 'fa-trash'));
            $actions->append(new EditRow($actions->getKey(), 'goods', '', 1, '', 'fa-edit'));
            $changeStatus = $actions->row['top'] == 1 ? 0 : 1;
            $changeShow   = $actions->row['top'] == 1 ? '' : '';
            $changeIcon   = $actions->row['top'] == 1 ? 'fa-hand-o-down' : 'fa-hand-o-up';
            $actions->append(new EnableRow($actions->getKey(), 'goods-top', $changeStatus, $changeShow, $changeIcon));//自定义操作
        });
        $grid->filter(function ($filter) {

            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->equal('gid', '商品ID');
            $filter->like('title', '商品名称');
            $filter->equal('partition', '分区')->select(function () {
                return GlobalConstant::getSystemPartitionMap();
            });
            $filter->equal('source', '来源')->select(function () {
                return Goods::getSourceMap();
            });
            $filter->equal('is_auto', '自动采集')->select(function () {
                return [1 => '手动', 2 => '自动'];
            });
            $filter->equal('cat_id_one', '一级分类')->select(function () {
                return GoodsCat::where("level", 1)->where("status", GlobalConstant::IS_YES)->get()->pluck('name', 'id');
            })->load("cat_id_two", "/admin/goods-cat/getCat");
            $filter->equal('cat_id_two', '二级分类')->select();
            $filter->where(function ($query) {
                switch ($this->input) {
                    case '2':
                        $query->whereNotNull('video');
                        break;
                    case '1':
                        $query->whereNull('video');
                        break;
                }
            }, '视频', '')->select([
                1 => '无',
                2 => '有'
            ]);
            $filter->use(new TimestampBetween('point_time', '秒杀时间'))->date();
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
        $show = new Show(Goods::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('source', __('Source'));
        $show->field('gid', __('Gid'));
        $show->field('cat_id_one', __('Cat id one'));
        $show->field('cat_id_two', __('Cat id two'));
        $show->field('pict_url', __('Pict url'));
        $show->images_url('images_url', __('Images url'))->json();
        $show->field('title', __('Title'));
        $show->field('reserve_price', __('Reserve price'));
        $show->field('zk_final_price', __('Zk final price'));
        $show->field('qh_final_price', __('Qh final price'));
        $show->field('qh_final_commission', __('Qh final commission'));
        $show->field('commission_rate', __('Commission rate'));
        $show->field('coupon_start_time', __('Coupon start time'));
        $show->field('coupon_end_time', __('Coupon end time'));
        $show->field('coupon_amount', __('Coupon amount'));
        $show->field('item_description', __('Item description'));
        $show->field('volume', __('Volume'));
        $show->field('shop_url', __('Shop url'));
        $show->field('shop_icon', __('Shop icon'));
        $show->field('shop_name', __('Shop name'));
        $show->field('sort', __('Sort'));
        $show->field('partition', __('Partition'));
        $show->field('goods_desc', __('Goods desc'));
        $show->extends_json('扩展')->json();
        $show->field('shop_nick', __('Shop nick'));
        $show->field('pop_url', __('Pop url'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        /*  $ddx = new DingDanXia();
          $ddx->getTbTestGIds();*/
        $form = new Form(new Goods());
        $form->select("source", "采集平台")->options(Goods::getCollectionSourceMap());
        $form->select("partition", "商品分区")->options(GlobalConstant::getSystemPartitionMap());
        $catRet = GoodsCat::where("level", 1)->where("status", GlobalConstant::IS_YES)->select(["id", "name"])->get()->toArray();
        $catRet = array_column($catRet, "name", "id");
        $form->select("cat_id_one", "商品一级分类")->options($catRet)->load("cat_id_two", "/admin/goods-cat/getCat");
        $form->select("cat_id_two", "二级分类");
        $form->text('ids', "商品ID")->placeholder("多个商品id用英文逗号分隔")->attribute('autocomplete', 'off');
        $form->date('pre_date', "发布时间")->placeholder("预发布时间,填写了则到时采集并上架")->attribute(['autocomplete' => 'off'])->width('270px');
        $form->select('point_id', '秒杀点,此选项填写后请务必填写上面时间')->options(GlobalConstant::getPointMap());
        $form->hidden('show_title', '自定义名称');
        $form->image('show_pic', '自定义主图')->setDisplay(false);
        $form->switch('is_special', '特殊商品');
        $form->number("sort", "排序")->default(100)->placeholder("数值越小越靠前");
        $form->setAction("collection");
        return $form;
    }

    public function collection(Request $request)
    {
        //这里就不限制脚本的执行时间了
        set_time_limit(0);
        $ids        = $request->post("ids");
        $ids        = explode(",", $ids);
        $ddx        = new DingDanXia();
        $source     = $request->post("source");
        $is_special = $request->post('is_special') == 'off' ? false : true;
        $pre_date   = $request->post('pre_date');
        if (!$source || !Goods::getSourceMap($source)) {
            admin_error("无效的来源平台");
            return back();
        }
        if (!$ids) {
            admin_error('请传入商品');
            return back();
        }
        $catOne    = $request->post("cat_id_one");
        $catTwo    = $request->post("cat_id_two");
        $sort      = $request->post("sort");
        $partition = intval($request->post("partition", 0));
        $point     = $request->post('point_id');
        if ($pre_date && !$point) {
            return self::addPreGoods($ids, ['source' => $source, 'cat_id_one' => $catOne, 'cat_id_two' => $catTwo, 'partition' => $partition, 'is_special' => $is_special ? 1 : 0, 'pre_date' => $pre_date]);
        }

        $i   = 0;
        $str = '';
        foreach ($ids as $item) {
            try {
                $rel = $ddx->getGoodsByGid([trim($item)], $source, $is_special);
                if (!$rel) {
                    $str .= '商品ID【' . $item . '】<font color="red">失败</font><br>';
                    continue;
                } else {
                    $str .= '商品ID【' . $item . '】成功<br>';
                }
                $i++;
                $goods             = reset($rel);
                $goods->cat_id_one = $catOne ?? 0;
                $goods->cat_id_two = $catTwo ?? 0;
                $goods->sort       = $sort + $i;
                $goods->partition  = $partition;
                $goods->sort_time  = time();
                $goods->is_auto    = 1;
                if ($pre_date && $point) {
                    $goods->point_id   = $point;
                    $goods->point_time = strtotime($pre_date);
                }
                if (!$goods->coupon_start_time) {
                    $goods->coupon_amount = 0;
                }
                switch ($source) {
                    case Goods::SOURCE_TB:
                        $rs = $ddx->getTbDetailImages($goods->gid);
                        break;
                    case Goods::SOURCE_PDD:
                        $rs = $ddx->getPddDetailImages($goods->gid);
                        break;
                    default:
                        $rs = $ddx->getJdDetailImages($goods->gid);
                        break;
                }
                if ($rs) {
                    $goods->detail_images = $rs;
                    if($source==Goods::SOURCE_PDD){
                        $goods->images_url=json_encode($rs);
                    }
                }
                $goods->save();
            } catch (\Exception $exception) {
                $str .= '商品【' . $item . '】失败<br>';
                continue;
            }

        }
        admin_info('采集结果提示', $str);
        return back();
    }

    public function changeTop(Request $request)
    {
        $id                 = $request->input('id', 0);
        $value              = $request->input('value', 0);
        $update['top']      = $value;
        $update['top_time'] = $value == 1 ? Carbon::now()->toDateTimeString() : null;
        $rel                = Goods::where('id', $id)->update($update);
        if ($rel)
            return response()->json(["code" => 1, "msg" => '操作成功']);
        return response()->json(["code" => 0, "msg" => '操作失败']);
    }

    //添加预采集
    public static function addPreGoods($gid, array $condition)
    {
        if (!is_array($gid)) {
            $gid = explode(',', $gid);
        }
        if (!$gid) {
            admin_error('操作提示', '请输入正确的商品ID');
            return back();
        }
        DB::beginTransaction();
        try {
            foreach ($gid as $item) {
                foreach ($condition as $key => $value) {
                    $data[$key] = $value;
                }
                $data['gid']       = $item;
                $data['publisher'] = Admin::user()->username;
                GoodsPreview::updateOrCreate($data);
            }
            DB::commit();
            admin_info('添加预发布成功,预发布时间' . $condition['pre_date']);
            return back();
        } catch (\Exception $exception) {
            DB::rollBack();
            admin_error('添加预发布成功出错', $exception->getMessage());
            return back();
        }
    }

    //预采集列表
    protected function calculate(Content $content)
    {
        $grid = new Grid(new GoodsPreview());
        $grid->model()->orderBy('pre_date', 'desc');
        $grid->disableRowSelector();
        $grid->id();
        $grid->gid('商品');
        $grid->column('goodInfo.title', '名称');
        $grid->column('catOne.name', "一级分类");
        $grid->column('catTwo.name', "二级分类");
        $grid->column('source', "来源")->using(Goods::getSourceMap());
        $grid->column('partition', __('分区'))->display(function ($val) {
            return GlobalConstant::getSystemPartitionMap($val);
        });
        $grid->pre_date('预采集时间')->sortable();
        $grid->column('is_special', '是否特殊商品')->using(['否', '是']);
        $grid->column('success', '状态')->using(['未采集', '成功', '失败'])->sortable();
        $grid->remark('备注');
        $grid->publisher('采集填写人')->sortable();
        $grid->created_at(trans('admin.created_at'));
        $grid->updated_at(trans('admin.updated_at'))->sortable();
        $grid->setActionClass(Grid\Displayers\DropdownActions::class);
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->add(new CeclearDelete());
        });
        $grid->filter(function ($filter) {

            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->equal('gid', '商品ID');
            $filter->equal('partition', '分区')->select(function () {
                return GlobalConstant::getSystemPartitionMap();
            });
            $filter->equal('source', '来源')->select(function () {
                return Goods::getSourceMap();
            });

            $filter->equal('cat_id_one', '一级分类')->select(function () {
                return GoodsCat::where("level", 1)->where("status", GlobalConstant::IS_YES)->get()->pluck('name', 'id');
            })->load("cat_id_two", "/admin/goods-cat/getCat");
            $filter->equal('cat_id_two', '二级分类')->select();
            $filter->equal('success', '状态')->select(function () {
                return ['未采集', '成功', '失败'];
            });
            $filter->equal('publisher', '采集填写人')->select(function () {

                return DB::table('admin_users')->get()->pluck('username', 'username');
            });
            $filter->equal('pre_date','预采集时间')->date();
        });
        $grid->disableCreateButton();
        $grid->disableExport();

        return $content
            ->header('预采集管理')
            ->description(trans('admin.description'))
            ->body($grid);
    }

    public function edit($id, Content $content)
    {
        return $content
            ->header(trans('admin.edit'))
            ->description(trans('admin.description'))
            ->body($this->updateShowPic()->edit($id));
    }

    public function updateShowPic()
    {
        $form = new Form(new Goods());
        $form->text('show_title', '商品名称');
        $form->image('show_pic', '自定义主图')->removable();
        return $form;
    }
}

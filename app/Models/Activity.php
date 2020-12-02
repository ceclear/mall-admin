<?php

namespace App\Models;

use App\GlobalConstant;
use Encore\Admin\Form;
use Encore\Admin\Widgets\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * App\Models\Activity
 *
 * @property int $id
 * @property int $type 活动模板类型
 * @property string|null $title title
 * @property string $head_pic 头图
 * @property string|null $bg_color 背景色
 * @property string|null $font_color 活动规则字体颜色
 * @property string|null $rule 活动规则字体颜色
 * @property string|null $extends_json 扩展
 * @property int|null $status 状态1正常0禁用
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Activity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Activity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Activity query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Activity whereBgColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Activity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Activity whereExtendsJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Activity whereFontColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Activity whereHeadPic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Activity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Activity whereRule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Activity whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Activity whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Activity whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Activity whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Activity extends Model
{
    use commonTrait;
    protected $connection = 'lx-mall';
    protected $table = 'activity';

    const BASE_NUM = 4;
    const MO_BAN_4 = 10;
    const MO_BAN_5 = 5;

    const SKIP_TYPE = [
        '0'          => '请选择跳转类型',
        'third'      => '第三方活动',
        'template_1' => '活动模板1',
        'template_2' => '活动模板2',
        'template_3' => '活动模板3',
        'template_4' => '活动模板4',
        'gid'        => '商品ID(拼多多,京东,淘宝)',
    ];

    public static function getTypeMap($key = "ALL")
    {
        $ret = self::SKIP_TYPE;
        if ($key === "ALL") {
            return $ret;
        }
        return Arr::get($ret, $key, false);
    }

    public static function createForm($form, $type = 1)
    {
        if (in_array($type, [1, 2, 3])) {
            if ($form->isCreating()) {
                self::templateA($form);
            }
        }
        if ($type == 4) {
            if ($form->isCreating()) {
                self::templateB($form);
            }
        }
        if ($type == 5) {
            if ($form->isCreating()) {
                self::templateC($form);
            }
        }

    }

    public static function formatFormField(Form $form, $type = 1)
    {
        $arr     = [];
        $platArr = GlobalConstant::getSourceMap();
        if (in_array($type, [1, 2, 3])) {
            for ($i = 1; $i <= Activity::BASE_NUM; $i++) {
                $key_title = 'title_sub_' . $i;
                $gidArr    = [];
                foreach ($platArr as $key => $item) {
                    $key_gid      = 'gids_' . $i . '_' . $key;
                    $gidArr[$key] = request($key_gid);
                }

                $data              = [];
                $data['title_sub'] = request($key_title);
                $data['gids']      = $gidArr;
                $arr[]             = $data;
            }
        }
        if ($type == 4) {
            $first_gids = [];
//            $arr['first_floor'] = ['title_sub' => request('title_sub'), 'gids' => request('gids')];
            foreach ($platArr as $key => $item) {
                $first_gids[$key] = request('gids_' . $key);
            }
            $arr['first_floor'] = ['title_sub' => request('title_sub'), 'gids' => $first_gids];
            $model              = self::find(request('id'));
            if (!$model) {
                $model = new self();
            }
            $old = $model->getExtendJson('board.second_floor');
            for ($i = 1; $i <= Activity::MO_BAN_4; $i++) {
                $key_pro_img  = 'pro_img_' . $i;
                $key_pro_id   = 'pro_id_' . $i;
                $key_pro_line = 'pro_line_color_' . $i;
//                $key_gid               = 'gids_' . $i;
                $gidArr = [];
                foreach ($platArr as $key => $item) {
                    $key_gid      = 'gids_' . $i . '_' . $key;
                    $gidArr[$key] = request($key_gid);
                }
                $data                  = [];
                if(($file=request()->file($key_pro_img))){
                    $imgField = $form->builder()->field($key_pro_img);
                    if ( !$imgField ) {
                        $form->pushField($form->image($key_pro_img));
                    }
                    $ret = $form->builder()->field($key_pro_img)->prepare($file);
                } else {
                    $ret = $old[$i - 1]['pro_img'];
                }
//                $data['pro_img']       = $_FILES[$key_pro_img]['name'] ? env('OSS_URL') . '/images/' . $_FILES[$key_pro_img]['name'] : $old[$i - 1]['pro_img'];
                $data['pro_img']       = $ret;
                $data['pro_id']        = request($key_pro_id);
                $data['pro_line']      = request($key_pro_line);
                $data['gids']          = $gidArr;
                $arr['second_floor'][] = $data;
            }
        }
        if ($type == 5) {
            $first_gids = [];
            foreach ($platArr as $key => $item) {
                $first_gids[$key] = request('gids_' . $key);
            }
            $arr['first_floor'] = ['title_sub' => request('title_sub'), 'gids' => $first_gids];
            $model              = self::find(request('id'));
            if (!$model) {
                $model = new self();
            }
            $old = $model->getExtendJson('board.second_floor');
            for ($i = 1; $i <= Activity::MO_BAN_5; $i++) {
                $key_pro_img      = 'pro_img_' . $i;
                $key_target_type  = 'target_type_' . $i;
                $key_target_value = 'target_value_' . $i;
                $gidArr           = [];
                foreach ($platArr as $key => $item) {
                    $key_gid      = 'gids_' . $i . '_' . $key;
                    $gidArr[$key] = request($key_gid);
                }
                $data                  = [];
                if(($file=request()->file($key_pro_img))){
                    $imgField = $form->builder()->field($key_pro_img);
                    if ( !$imgField ) {
                        $form->pushField($form->image($key_pro_img));
                    }
                    $ret = $form->builder()->field($key_pro_img)->prepare($file);
                } else {
                    $ret = $old[$i - 1]['pro_img'];
                }
//                $data['pro_img']       = $_FILES[$key_pro_img]['name'] ? env('OSS_URL') . '/images/' . $_FILES[$key_pro_img]['name'] : $old[$i - 1]['pro_img'];
                $data['pro_img']       = $ret;
                $data['target_type']   = request($key_target_type);
                $data['target_value']  = request($key_target_value);
                $data['gids']          = $gidArr;
                $arr['second_floor'][] = $data;
            }
        }
        $form->model()->setExtendJson('board', $arr);
        $form->extends_json = $form->model()->extends_json;
    }

    public static function templateC($form, $value = [])
    {
        $plat = GlobalConstant::getSourceMap();
        if ($value) {
            $form->text('title_sub', '标题')->value($value['first_floor']['title_sub'])->attribute(['autocomplete' => 'off']);
//            $form->textarea('gids', '商品ID')->placeholder('商品ID用英文逗号隔开，只填写10个商品ID')->value($value['first_floor']['gids']);
            foreach ($plat as $key => $item) {
                $form->textarea('gids_' . $key, $item . '商品ID')->placeholder('商品ID用英文逗号隔开，只填写10个商品ID')->value($value['first_floor']['gids'][$key]);
            }
            foreach ($value['second_floor'] as $key => $item) {
                $form->fieldset('广告位' . ($key + 1), function (Form $form) use ($key, $item,$plat) {
                    $form->image('pro_img_' . ($key + 1), '图片')->uniqueName()->value($item['pro_img']);
                    $form->select('target_type_' . ($key + 1), '跳转链接')->options(
                        self::SKIP_TYPE
                    )->value($item['target_type']);
                    $form->text('target_value_' . ($key + 1), '')->value($item['target_value']);
                    foreach ($plat as $k => $val) {
                        $form->textarea('gids_' . ($key+1) . '_' . $k, $val . '商品ID')->value($item['gids'][$k]);
                    }

                })->collapsed();
            }
        } else {
            $form->text('title_sub', '标题')->attribute(['autocomplete' => 'off']);
//            $form->textarea('gids', '商品ID')->placeholder('商品ID用英文逗号隔开，只填写10个商品ID');
            foreach ($plat as $key => $item) {
                $form->textarea('gids_' . $key, $item . '商品ID')->placeholder('商品ID用英文逗号隔开');
            }
            for ($i = 1; $i <= Activity::MO_BAN_5; $i++) {

                $form->fieldset('广告位' . $i, function (Form $form) use ($i, $plat) {
                    $form->image('pro_img_' . $i, '图片');
                    $form->select('target_type_' . $i, '跳转链接')->options(
                        self::SKIP_TYPE
                    );
                    $form->text('target_value_' . $i, '')->placeholder('如果跳转类型是商品ID此栏不填');
                    foreach ($plat as $k => $val) {
//                    $form->textarea('gids_' . ($key + 1), '商品ID')->placeholder('商品ID用英文逗号隔开,只填写3个商品ID')->value($item['gids']);
                        $form->textarea('gids_' . $i . '_' . $k, $val . '商品ID')->placeholder('商品ID用英文逗号隔开,只填写3个商品ID');
                    }

                })->collapsed();


            }
        }

    }

    public static function templateB($form, $value = [])
    {
        $plat = GlobalConstant::getSourceMap();
        if ($value) {
            $form->text('title_sub', '标题')->value($value['first_floor']['title_sub'])->attribute(['autocomplete' => 'off']);
//            $form->textarea('gids', '商品ID')->placeholder('商品ID用英文逗号隔开，只填写10个商品ID')->value($value['first_floor']['gids']);
            foreach ($plat as $key => $item) {
                $form->textarea('gids_' . $key, $item . '商品ID')->placeholder('商品ID用英文逗号隔开，只填写10个商品ID')->value($value['first_floor']['gids'][$key]);
            }

            foreach ($value['second_floor'] as $key => $item) {
                $form->fieldset('品牌专场' . ($key + 1), function (Form $form) use ($key, $item, $plat) {
                    $form->image('pro_img_' . ($key + 1), '品牌专场图片')->value($item['pro_img']);
                    $form->text('pro_id_' . ($key + 1), '品牌专场ID')->width('30%')->value($item['pro_id']);
                    $form->color('pro_line_color_' . ($key + 1), '线框颜色')->width('30%')->value($item['pro_line']);
                    foreach ($plat as $k => $val) {
//                    $form->textarea('gids_' . ($key + 1), '商品ID')->placeholder('商品ID用英文逗号隔开,只填写3个商品ID')->value($item['gids']);
                        $form->textarea('gids_' . ($key + 1) . '_' . $k, $val . '商品ID')->placeholder('商品ID用英文逗号隔开,只填写3个商品ID')->value($item['gids'][$k]);
                    }
                })->collapsed();
            }
        } else {
            $form->text('title_sub', '标题1')->attribute(['autocomplete' => 'off']);
//            $form->textarea('gids', '商品ID')->placeholder('商品ID用英文逗号隔开，只填写10个商品ID');
            foreach ($plat as $key => $item) {
                $form->textarea('gids_' . $key, $item . '商品ID')->placeholder('商品ID用英文逗号隔开');
            }
            for ($i = 1; $i <= Activity::MO_BAN_4; $i++) {

                $form->fieldset('品牌专场' . $i, function (Form $form) use ($i, $plat) {
                    $form->image('pro_img_' . $i, '品牌专场图片');
                    $form->text('pro_id_' . $i, '品牌专场ID')->width('30%')->attribute(['autocomplete' => 'off']);
                    $form->color('pro_line_color_' . $i, '线框颜色')->width('30%')->attribute(['autocomplete' => 'off']);
//                    $form->textarea('gids_' . $i, '商品ID')->placeholder('商品ID用英文逗号隔开,只填写3个商品ID');
                    foreach ($plat as $key => $item) {
                        $form->textarea('gids_' . $i . '_' . $key, $item . '商品ID')->placeholder('商品ID用英文逗号隔开');
                    }
                })->collapsed();


            }
        }

    }

    public static function templateA($form, $value = [])
    {
        $plat = GlobalConstant::getSourceMap();
        if ($value) {
            foreach ($value as $key => $item) {
                $form->fieldset('模块' . ($key + 1), function (Form $form) use ($key, $item, $plat) {
                    $form->text('title_sub_' . ($key + 1), '标题' . ($key + 1))->value($item['title_sub']);
                    foreach ($plat as $k => $val) {
                        $form->textarea('gids_' . ($key + 1) . '_' . $k, $val . '商品ID')->value($item['gids'][$k]);
                    }
                })->collapsed();
            }
        } else {
            for ($i = 1; $i <= Activity::BASE_NUM; $i++) {
                $form->fieldset('模块' . $i, function (Form $form) use ($i, $plat) {
                    $form->text('title_sub_' . $i, '标题' . $i)->attribute(['autocomplete' => 'off']);
                    foreach ($plat as $key => $item) {
                        $form->textarea('gids_' . $i . '_' . $key, $item . '商品ID')->placeholder('商品ID用英文逗号隔开');
                    }

                })->collapsed();
            }
        }

    }

    public static function showExtendsJson($model)
    {
        $arr = json_decode($model->extends_json, true);
        if (in_array($model->type, [1, 2, 3])) {
            $data = $arr['board'];
            foreach ($data as $key => $item) {
                if (!$item['title_sub']) {
                    unset($data[$key]);
                }
            }
            return new Table(['标题', '商品'], $data);
        } elseif ($model->type == 4) {
            $data = $arr['board']['second_floor'];
            foreach ($data as $key => $item) {
                if (!$item['pro_id']) {
                    unset($data[$key]);
                }
                unset($data[$key]['pro_img']);
            }
            return new Table(['专场ID', '线框颜色', '商品'], $data);
        } else {
            return '';
        }
    }

    public static function showColumns($grid, $type = 1)
    {
        if (in_array($type, [1, 2, 3])) {
            for ($i = 0; $i < self::BASE_NUM; $i++) {
                $grid->column('标题' . ($i + 1))->display(function () use ($i) {
                    $arr = $this->getExtendJson('board');
                    return $arr[$i]['title_sub'] ?? '-';
                });
//                    ->modal('详情', function ($model) use ($i, $type) {
////                    return self::showArrExtends(['标题', '商品'], $model, $i, $type);
//                });
            }
        }
        if ($type == 4) {
            $grid->column('标题')->display(function () {
                $arr = $this->getExtendJson('board.first_floor');
                return $arr['title_sub'] ?? '-';
            });
//            $grid->column('商品')->display(function () {
//                $arr = $this->getExtendJson('board.first_floor');
//                return $arr['gids'] ?? '-';
//            });
            for ($i = 0; $i < self::MO_BAN_4; $i++) {
                $grid->column('专场图片' . ($i + 1))->display(function () use ($i) {
                    $arr = $this->getExtendJson('board.second_floor');
                    return $arr[$i]['pro_img'] ?? '';
                })->lightbox(['width' => 100, 'height' => 50]);
//                    ->modal('专场' . ($i + 1) . '详情', function ($model) use ($i, $type) {
//                    return self::showArrExtends(['专场ID', '线框颜色', '商品'], $model, $i, $type);
//                });
            }
        }
        if ($type == 5) {
            $grid->column('标题')->display(function () {
                $arr = $this->getExtendJson('board.first_floor');
                return $arr['title_sub'] ?? '-';
            });
//            $grid->column('商品')->display(function () {
//                $arr = $this->getExtendJson('board.first_floor');
//                return $arr['gids'] ?? '-';
//            });
            for ($i = 0; $i < self::MO_BAN_5; $i++) {
                $grid->column('广告位图片' . ($i + 1))->display(function () use ($i) {
                    $arr = $this->getExtendJson('board.second_floor');
                    return $arr[$i]['pro_img'] ?? '';
                })->lightbox(['width' => 100, 'height' => 50]);
//                    ->modal('广告位' . ($i + 1) . '详情', function ($model) use ($i, $type) {
//                    return self::showArrExtends(['链接类型', '链接值'], $model, $i, $type);
//                });
            }
        }

    }

    public static function showArrExtends($key, $model, $i, $type)
    {
        $arr = [];
        if (in_array($type, [1, 2, 3])) {
            $arr = $model->getExtendJson('board');
        }
        if ($type == 4 || $type == 5) {
            $arr = $model->getExtendJson('board.second_floor');
        }
        $data = $arr[$i]['gids'];
        if ($type == 4 || $type == 5) {
            unset($data['pro_img']);
        }
        if ($type == 5) {
            if ($data['target_type']) {
                $data['target_type'] = self::getTypeMap($data['target_type']);
            }

        }
        return new Table($key, [$data]);
    }

}

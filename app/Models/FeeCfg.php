<?php

namespace App\Models;

use App\Errors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * App\Models\FeeCfg
 *
 * @property int $id
 * @property int $type 1合伙人 2团长 3高级团长
 * @property float $self_fee_rate 自购佣金
 * @property float $subsidy_fee_rate 自购佣金 平台补贴 self_fee_rate+subsidy_fee_rate为实际获得佣金
 * @property float $team_fee_one_rate 团队一级合伙人佣金 比列
 * @property float $team_fee_two_rate 团队二级合伙人佣金 比列
 * @property float $team_fee_infinite_rate 团队二级以外合伙人（二级向下无限极）佣金 比列
 * @property float $share_fee_rate 分享佣金比列
 * @property string $condition
 *                 达成条件 register 表示是否需要注册 1注册 2不注册
 *                 colonel_one 直属一级团长数量大于等于x
 *                 colonel_two 二级团长数量大于等于x
 *                 colonel_one_and_two 直属一级团长或者二级团长数量大于等于x
 *                 condition:{
 *                     register:1,
 *                     colonel_one:0,
 *                     colonel_two:0,
 *                     colonel_one_and_two:0,
 *                 }
 * @property string $extends_json
 *                 扩展字段
 *                 //根据级别的不通里面的字段可能会不同
 *                 rates:{
 *                     //团长级别的设置
 *                     colonel_one_rate:0,//一级有人成为团长 平台给的额外补贴百分比
 *                     colonel_two_rate:0,//二级有人成为团长 平台给的额外补贴百分比
 * 
 *                     //高级团长的
 *                     colonel_rate:0,//团长的补贴 无限极
 *                     senior_colonel_rate:0,//高级团长的补贴 无限极
 * 
 *                     //团队里每出现一个高级团长时的自增项
 *                     senior_colonel_rate_step:{
 *                         self_fee_rate_increment:0,//自购佣金
 *                         self_top:10,//封顶数
 *                         team_fee_rate_increment:0,//团队佣金
 *                         team_top:10,//封顶数
 * 
 *                     }
 *                 }
 * @property int $status 1正常 0禁用
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeeCfg newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeeCfg newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeeCfg query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeeCfg whereCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeeCfg whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeeCfg whereExtendsJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeeCfg whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeeCfg whereSelfFeeRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeeCfg whereShareFeeRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeeCfg whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeeCfg whereSubsidyFeeRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeeCfg whereTeamFeeInfiniteRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeeCfg whereTeamFeeOneRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeeCfg whereTeamFeeTwoRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeeCfg whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FeeCfg whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FeeCfg extends Model
{
    use Errors;
    protected $connection = 'lx-mall';
    protected $table = 'fee_cfg';

    protected $fillable = ['type', 'self_fee_rate', 'subsidy_fee_rate', 'team_fee_one_rate', 'team_fee_two_rate', 'share_fee_rate', 'condition'];
    protected $attributes = array(
        'extends_json' => '',
    );
    const Condition = ['register' => [1 => '下载注册链信省钱']];
    const COL_Level = ['colonel_one' => '一级', 'colonel_two' => '二级', 'colonel_one_and_two' => '一级和二级'];
    const FeeType = [1, 2, 3];//1合伙人 2团长 3高级团长
    const Symbol = [1 => '>='];


    public static $rules = [
        "type" => "required|integer",
        "self_fee_rate" => "required",
        "subsidy_fee_rate" => "required",
        "team_fee_one_rate" => "required",
        "team_fee_two_rate" => "required",
        "share_fee_rate" => "required",
        "condition" => "required",
    ];

    public static $rulesMessage = [
        "type.required" => "佣金类型不能为空",
        "type.integer" => "佣金类型必须为数字",
        "self_fee_rate.required" => "自购佣金比例不能为空",
        "self_fee_rate.lt" => "自购佣金比例不能大于等于100",
        "subsidy_fee_rate.required" => "自购佣金平台补贴比例不能为空",
        "subsidy_fee_rate.lt" => "自购佣金平台补贴比例不能大于等于100",
        "team_fee_one_rate.required" => "团队一级合伙人佣金比例不能为空",
        "team_fee_one_rate.lt" => "团队一级合伙人佣金比例不能大于等于100",
        "team_fee_two_rate.required" => "团队二级合伙人佣金比例不能为空",
        "team_fee_two_rate.lt" => "团队二级合伙人佣金比例不能大于等于100",
        "share_fee_rate.required" => "分享佣金比例不能为空",
        "share_fee_rate.lt" => "分享佣金比例不能大于等于100",
        "condition.required" => "达成条件不能为空",
    ];

    public static function attributeNames($key = null)
    {
        $ret = [
            "type" => "佣金类型",
            "self_fee_rate" => "自购佣金比例",
            "subsidy_fee_rate" => "自购佣金平台补贴比例",
            "team_fee_one_rate" => "团队一级合伙人佣金比例",
            "team_fee_two_rate" => "团队二级合伙人佣金比例",
            "condition" => "达成条件",
        ];
        if (!$key) {
            return false;
        }
        return Arr::get($ret, $key, $key);
    }

    public static function setRow($params)
    {
        $id               = $params['id'];
        $fee_cfg          = static::find($id);
        $params['status'] = $params['status'] == true ? 1 : 0;
        if (!$fee_cfg) {
            $fee_cfg = new self();
        }
        $fee_cfg->type              = $params['type'];
        $fee_cfg->self_fee_rate     = $params['self_fee_rate'];
        $fee_cfg->subsidy_fee_rate  = $params['subsidy_fee_rate'];
        $fee_cfg->team_fee_one_rate = $params['team_fee_one_rate'];
        $fee_cfg->team_fee_two_rate = $params['team_fee_two_rate'];
        $fee_cfg->share_fee_rate    = $params['share_fee_rate'];
        $fee_cfg->condition         = json_encode(['register' => $params['condition']]);
        $fee_cfg->status            = $params['status'];
        //创建验证器
        $validator = \Validator::make($fee_cfg->attributes, self::$rules, self::$rulesMessage);
        if ($validator->fails()) {
            $errors = $validator->errors();
            self::Error($errors->first());
            return false;
        }
        return $fee_cfg->save();
    }

    public static function setCommanderRow($params)
    {
        $id               = $params['id'];
        $fee_cfg          = static::find($id);
        $params['status'] = $params['status'] == true ? 1 : 0;
        if (!$fee_cfg) {
            $fee_cfg = new self();
        }
        $fee_cfg->type                   = $params['type'];
        $fee_cfg->self_fee_rate          = $params['self_fee_rate'];
        $fee_cfg->subsidy_fee_rate       = $params['subsidy_fee_rate'];
        $fee_cfg->team_fee_one_rate      = $params['team_fee_one_rate'];
        $fee_cfg->team_fee_two_rate      = $params['team_fee_two_rate'];
        $fee_cfg->team_fee_infinite_rate = $params['team_fee_infinite_rate'];
        $fee_cfg->share_fee_rate         = $params['share_fee_rate'];
        $fee_cfg->condition              = $params['condition1'] && $params['condition2'] ? json_encode([$params['condition1'] => $params['num1'], $params['condition2'] => $params['num2']]) : '';
        $fee_cfg->status                 = $params['status'];
        //团长
        if (!empty($params['colonel_one_rate']) && !empty($params['colonel_two_rate'])) {
            $fee_cfg->extends_json = json_encode(['rates' => ['colonel_one_rate' => number_format($params['colonel_one_rate'], 2), 'colonel_two_rate' => number_format($params['colonel_two_rate'], 2)]]);
        }
        //高级团长
        if (!empty($params['colonel_rate']) && !empty($params['senior_colonel_rate'])) {
            $rates                             = ['colonel_rate' => number_format($params['colonel_rate'], 2), 'senior_colonel_rate' => number_format($params['senior_colonel_rate'], 2)];
            $rates['senior_colonel_rate_step'] =
                [
                    'self_fee_rate_increment' => $params['self_fee_rate_increment'],
                    'self_top' => $params['self_top'],
                    'team_fee_rate_increment' => $params['team_fee_rate_increment'],
                    'team_top' => $params['team_top']
                ];
            $fee_cfg->extends_json             = json_encode(['rates'=>$rates]);
        }
        //创建验证器
        $validator = \Validator::make($fee_cfg->attributes, self::$rules, self::$rulesMessage);
        if ($validator->fails()) {
            $errors = $validator->errors();
            self::Error($errors->first());
            return false;
        }
        return $fee_cfg->save();
    }


    protected static function createEditHref($type, $id)
    {
        switch ($type) {
            case 1:
                return '/admin/fee-cfg/create-cfg-partner?id=' . $id;
            default:
                return '/admin/fee-cfg/create-cfg-commander?id=' . $id;
        }
    }
}

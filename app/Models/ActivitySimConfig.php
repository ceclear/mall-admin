<?php

namespace App\Models;

use App\Errors;
use App\GlobalConstant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * App\Models\ActivitySimConfig
 *
 * @property int $id
 * @property int $type 活动类型
 * @property int $st 活动开始时间
 * @property int $et 活动结束时间
 * @property int $status 启用禁用状态 1启用 0禁用
 * @property string|null $extends_json 活动规则json 用于程序处理
 * @property string|null $rules_desc 规则描述
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivitySimConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivitySimConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivitySimConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivitySimConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivitySimConfig whereEt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivitySimConfig whereExtendsJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivitySimConfig whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivitySimConfig whereRulesDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivitySimConfig whereSt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivitySimConfig whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivitySimConfig whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivitySimConfig whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ActivitySimConfig extends Model
{
    use commonTrait, Errors;

    protected $table = "activity_sim_config";
    protected $connection = "lx-mall";

    const TYPE_INVITATION = 1;
    const TYPE_SALE_RANKING_LIST = 2;

    const INVITATION_PRIZE_HF_45_47 = "hf45_47";
    const INVITATION_PRIZE_HF_01 = "hf01";
    const INVITATION_PRIZE_HF_02 = "hf02";
    const INVITATION_PRIZE_HB_166 = "hb166";
    const INVITATION_PRIZE_HB_666 = "hb666";
    const INVITATION_PRIZE_XM_BJB = "xm_bjb";
    const INVITATION_PRIZE_PHONE11 = "phone11";

    const EXTENDS_JSON_KEY_RULE = "json_rule";

    public static function getTypeMap($key = "ALL", $default = false) {
        $ret = [
            self::TYPE_INVITATION => "新人邀请",
            self::TYPE_SALE_RANKING_LIST => "销售榜单"
        ];
        if ( $key === "ALL" ) {
            return $ret;
        }
        return Arr::get($ret, $key, $default);
    }

    public static function getInvitationMap($key = "ALL", $default = false)
    {
        $ret = [
            self::INVITATION_PRIZE_HF_45_47 => "45-47元话费券",
            self::INVITATION_PRIZE_HF_01 => "0.1元话费券",
            self::INVITATION_PRIZE_HF_02 => "0.2元话费券",
            self::INVITATION_PRIZE_HB_166 => "166元现金红包",
            self::INVITATION_PRIZE_HB_666 => "666元现金红包",
            self::INVITATION_PRIZE_XM_BJB => "小米笔记本一台",
            self::INVITATION_PRIZE_PHONE11 => "Phone11一台",
        ];
        if ( $key === "ALL" ) {
            return $ret;
        }
        return Arr::get($ret, $key, $default);
    }

    public static function getRow($type)
    {
        if ( !self::getTypeMap($type) ) {
            self::Error("无效的类型");
            return false;
        }
        $ret = self::whereType($type)->first();
        if ( $ret ) {
            return $ret;
        }
        $ret = new self();
        $ret->type = $type;
        $ret->st = 0;
        $ret->et = 0;
        $ret->status = GlobalConstant::IS_NO;
        $ret->initExtendsJson();
        $ret->save();
        return $ret;
    }

    public function initExtendsJson()
    {
        switch ( $this->type ) {
            case self::TYPE_INVITATION:
                $json = [];
                foreach ( self::getInvitationMap() as $key => $item ) {
                    $json[$key] = [
                        "count_after_must" => 0,
                        "probability" => 0,
                        "desc" => $item
                    ];
                }
                $this->setExtendJson(self::EXTENDS_JSON_KEY_RULE, $json);
                break;
            case self::TYPE_SALE_RANKING_LIST:
                break;
        }
    }

    public function getStAttribute($value)
    {
        return $value ? date("Y-m-d H:i:s", $value) : null;
    }
    public function setStAttribute($value)
    {
        $this->attributes["st"] = strtotime($value);
    }

    public function getEtAttribute($value)
    {
        return $value ? date("Y-m-d H:i:s", $value) : null;
    }
    public function setEtAttribute($value)
    {
        $this->attributes["et"] = strtotime($value);
    }
}

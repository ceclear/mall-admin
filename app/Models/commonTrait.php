<?php


namespace App\Models;


use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redis;

trait commonTrait
{
    public static $EXTEND_JSON_KEY = "extends_json";

    public static function lockOperate($key, \Closure $fun)
    {
        while ( Redis::setnx($key, 1) ) {
            $ret = $fun();
            Redis::del($key);
            return $ret;
        }
        return ;
    }

    public function setExtendJson($key, $value)
    {
        $extendJsonName = self::$EXTEND_JSON_KEY;
        $extendJson = $this->$extendJsonName;
        if ( !$extendJson ) {
            $extendJson = [];
        }
        if ( !is_array($extendJson) ) {
            $extendJson = json_decode($extendJson, true);
        }
        if ( $value === null ) {
            unset($extendJson[$key]);
        } else {
            if ( !is_array($value) ) {
                //验证一下是否是一个json字符串
                $v = json_decode($value, true);
                $extendJson[$key] = $v ? $v : $value;
            } else {
                $extendJson[$key] = $value;
            }
        }
        $this->$extendJsonName = json_encode($extendJson);
        return ;
    }
    /**
     * 获取扩展参数
     * @param $key
     * @param $default
     * @return mixed
     */
    public function getExtendJson($key, $default = false)
    {
        $extendJsonName = self::$EXTEND_JSON_KEY;
        if ( !is_array($this->$extendJsonName) ) {
            $extendsJson = json_decode($this->$extendJsonName, true);
        } else {
            $extendsJson = $this->$extendJsonName;
        }
        return Arr::get($extendsJson, $key, $default);
    }


    /**
     * 设置关联表数据
     * @param $mainName
     * @param $mainVal
     * @param $linkName
     * @param array $data
     * @return array|bool
     */
    public function setLinkRows($mainName, $mainVal, $linkName, array $data, $pk = "id")
    {
        $ret = self::where([
            $mainName => $mainVal
        ])->get()->keyBy($pk)->toArray();
        $insertIds = [];
        $updateIds = [];
        //如果关联表中没有关联数据则直接insert
        if ( !$ret ) {
            foreach ( $data as $item ) {
                if ( is_array($item) ) {
                    $this->fillable(array_keys($item));
                    $this->fill($item);
                } else {
                    $this->$linkName = $item;
                }
                $this->$mainName = $mainVal;
                if ( isset($this->$pk) ) {
                    unset($this->$pk);
                }
                $this->exists = false;
                $this->save();
                $insertIds[] = $this->$pk;
            }
            return [
                "deletes" => [],
                "insert" => $insertIds,
                "updates" => $updateIds
            ];
        }
        \DB::connection($this->getConnectionName())->beginTransaction();
        //这里来拿出需要被删除的数据和不需要处理的数据
        $first = reset($data);
        if ( is_array($first) ) {
            $tmpData = array_column($data, $linkName, $pk);
        } else {
            $tmpData = $data;
        }

        foreach ( $ret as $retItem ) {
            if ( !in_array($retItem[$linkName], $tmpData) ) {
                $deletes[] = $retItem[$pk];
            } else {
                $key = array_search($retItem[$linkName], $tmpData);
                if ( ($changeAttr = array_diff($data[$key], $retItem)) ) {
                    self::where($pk, $retItem[$pk])->update($changeAttr);
                    $updateIds[] = $retItem[$pk];
                }
                unset($data[$key]);
            }
        }
        if ( isset($deletes) && $deletes ) {
            $delRet = self::destroy($deletes);
            if ( !$delRet ) {
                \DB::connection($this->getConnectionName())->rollBack();
                return false;
            }
        }
        foreach ( $data as $item ) {
            if ( is_array($item) ) {
                $this->fillable(array_keys($item));
                $this->fill($item);
            } else {
                $this->$linkName = $item;
            }
            $this->$mainName = $mainVal;
            if ( isset($this->$pk) ) {
                unset($this->$pk);
            }
            $this->exists = false;
            $this->save();
            $insertIds[] = $this->$pk;
        }
        \DB::connection($this->getConnectionName())->commit();
        return [
            "deletes" => isset($deletes) ? $deletes : [],
            "insert" => $insertIds,
            "updates" => $updateIds
        ];
    }
}

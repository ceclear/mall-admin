<?php


namespace App;

use Encore\Admin\Grid\Filter\Between;
use Illuminate\Support\Arr;

class TimestampBetween extends Between
{
    public function condition($inputs)
    {
        // $inputs即为传进来的参数，格式化成timestamp再去构建条件

        if (!array_key_exists($this->column,$inputs)) {
            return;
        }

        $this->value =Arr::get($inputs,$this->column);

        $value = array_filter($this->value, function ($val) {
            return $val !== '';
        });

        if (empty($value)) {
            return;
        }

        if (!isset($value['start'])) {


            $value['end'] = strtotime($value['end']);//转成时间戳

            return $this->buildCondition($this->column, '<=', $value['end']);
        }

        if (!isset($value['end'])) {

            $value['start'] = strtotime($value['start']);//转成时间戳

            return $this->buildCondition($this->column, '>=', $value['start']);
        }

        $this->query = 'whereBetween';

        $value['end']   = strtotime($value['end']);//转成时间戳
        $value['start'] = strtotime($value['start']);//转成时间戳


        //return $this->buildCondition($this->column, $this->value);
        //这里需要注意$this->value的值会作用于页面reset按钮，不能直接修改这个值，否则会导致按reset回显时间戳

        return $this->buildCondition($this->column, $value);

    }
}


<?php

namespace App\Services;

use App\Models\Goods;
use App\Models\GoodsCat;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderService
{

    public function rankList($param)
    {
        $model = Order::select(DB::raw('goods_item_id, goods_title,order.source,goods_cat_two,goods_cat_one, count(*) AS counts,SUM(IF(order.status = 12, 1, 0)) AS had_paid'));

        if ($param['start_time']) {
            $model->where('order.created_at', '>=', $param['start_time']);
        }

        if ($param['end_time']) {
            $model->where('order.created_at', '<=', $param['end_time']);
        }
        if ($param['partition']) {
            $model->where('goods_partition', $param['partition']);
        }
        if ($param['cat_id_one']) {
            $model->where('goods_cat_one', $param['cat_id_one']);
        }
        if ($param['cat_id_two']) {
            $model->where('goods_cat_two', $param['cat_id_two']);
        }
        if ($param['goods_id']) {
            $model->where('goods_item_id', $param['goods_id']);
        }

        $model->groupBy(['goods_item_id', 'goods_title', 'order.source', 'goods_cat_two', 'goods_cat_one'])->orderBy('counts', 'desc');
        $list = $model->limit(10)->get();
        $cat  = GoodsCat::all(['id', 'name']);
        if ($list) {
            foreach ($list as &$item) {
                $item['source']       = Goods::getSourceMap($item['source']);
                $item['cat_one_name'] = '';
                $item['cat_two_name'] = '';
                foreach ($cat as $value) {
                    if ($value['id'] == $item['goods_cat_one']) {
                        $item['cat_one_name'] = $value['name'];
                    }
                    if ($value['id'] == $item['goods_cat_two']) {
                        $item['cat_two_name'] = $value['name'];
                    }
                }
            }
            unset($item);
        }
        return $list;
    }

    public function topStatics($param)
    {
        $model = Order::select(DB::raw(
            'count(IF(order.status != ' . Order::STATUS_CLOSED . ' and order.status !=' . Order::STATUS_INVALID . ', 1, 0)) AS total_recharge,'
            . 'IFNULL(SUM(IF(order.status != ' . Order::STATUS_CLOSED . ' and order.status !=' . Order::STATUS_INVALID . ', order.pay_price, 0)),0) AS total_money,'
            . 'count(*) AS total_order,'
            . 'IFNULL(SUM(IF(order.status =' . Order::STATUS_PAID . ' or order.status = ' . Order::STATUS_COMPLETE . ', order.pre_fee, 0)),0) AS total_pre_money,'
            . 'IFNULL(SUM(IF(order.status = ' . Order::STATUS_COMPLETE . ', order.total_commission_fee, 0)),0) AS total_settle_pre_money'
        ));
        if ($param['start_time']) {
            $model->where('created_at', '>=', $param['start_time']);
        }

        if ($param['end_time']) {
            $model->where('created_at', '<=', $param['end_time']);
        }
        if ($param['partition']) {
            $model->where('goods_partition', $param['partition']);
        }
        if ($param['cat_id_one']) {
            $model->where('goods_cat_one', $param['cat_id_one']);
        }
        if ($param['cat_id_two']) {
            $model->where('goods_cat_two', $param['cat_id_two']);
        }
        if ($param['goods_id']) {
            $model->where('goods_item_id', $param['goods_id']);
        }
        return $model->first();
    }

    public function dayStatics($param)
    {
        $model = Order::select(DB::raw(
            'pay_date as days,count(*) as counts,'
            . 'SUM(IF(order.status = ' . Order::STATUS_PAID . ' or order.status =' . Order::STATUS_COMPLETE . ', order.pre_fee, 0)) AS total_pre_money,'
            . 'SUM(IF(order.status = ' . Order::STATUS_COMPLETE . ', order.pre_fee, 0)) AS total_settle_pre_money'
        ));
        if ($param['start_time']) {
            $model->where('order.created_at', '>=', $param['start_time']);
        }

        if ($param['end_time']) {
            $model->where('order.created_at', '<=', $param['end_time']);
        }
        if ($param['partition']) {
            $model->where('goods_partition', $param['partition']);
        }
        if ($param['cat_id_one']) {
            $model->where('goods_cat_one', $param['cat_id_one']);
        }
        if ($param['cat_id_two']) {
            $model->where('goods_cat_two', $param['cat_id_two']);
        }
        if ($param['goods_id']) {
            $model->where('goods_item_id', $param['goods_id']);
        }
        $model->where('status', '!=', Order::STATUS_INVALID)->where('status', '!=', Order::STATUS_CLOSED);

        $model->groupBy('days')->orderBy('days', 'desc');

        return $model->limit(15)->get();

    }

}

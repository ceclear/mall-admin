<?php

namespace App\Admin\Extensions;

use App\Models\UsersInfo;
use App\Models\Withdrawal;
use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class RechargeExporter extends ExcelExporter implements WithMapping, WithCustomValueBinder
{
    protected $fileName = '话费充值列表.xlsx';

    public function bindValue(Cell $cell, $value)
    {
        $cell->setValueExplicit($value, DataType::TYPE_STRING);

        return true;
    }

    protected $columns = [
        'id' => 'ID',
        'uid' => '用户ID',
        'order_sn' => '订单编号',
        'phone' => '手机号',
        'amount' => '充值面额',
        'use_ticket' => '是否使用话费券',
        'ticket_amount' => '话费券金额',
        'pay_amount' => '实付金额',
        'payment' => '支付方式',
        'status' => '状态',
        'reason' => 'Reason',
        'created_at' => '创建时间',
        'updated_at' => '修改时间'
    ];

    public function map($recharge): array
    {
        return [
            $recharge->id,
            $recharge->uid,
            $recharge->order_sn,
            $recharge->phone,
            $recharge->amount,
            $recharge->use_ticket == 1 ? '使用' : '未使用',
            $recharge->ticket_amount,
            $recharge->pay_amount,
            $recharge->payment == 2 ? '微信' : '支付宝',
            \App\Models\RechargeOrder::$status[$recharge->status],
            $recharge->reason,
            $recharge->created_at,
            $recharge->updated_at
        ];
    }
}

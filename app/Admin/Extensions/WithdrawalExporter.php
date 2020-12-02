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

class WithdrawalExporter extends ExcelExporter implements WithMapping, WithCustomValueBinder
{
    protected $fileName = '提现列表.xlsx';

    public function bindValue(Cell $cell, $value)
    {
        $cell->setValueExplicit($value, DataType::TYPE_STRING);

        return true;
    }

    protected $columns = [
        'id' => 'ID',
        'trade_no' => '交易订单号',
        'out_trade_no' => '外部交易订单号',
        'uid' => '用户昵称(UID)',
        'real_name' => '实名姓名',
        'id_card' => '身份证号码',
        'userCards.name' => '链信实名姓名',
        'userCards.id_card' => '链信实名身份证',
        'mobile' => '联系电话',
        'amount' => '提现数量',
        'withdrawal_type' => '提现平台',
        'alipay' => '支付宝账号',
        'wx_openid' => '微信OPENID',
        'status' => '状态',
        'remark' => '备注',
        'manage.name' => '操作人',
        'created_at' => '创建时间',
        'updated_at' => '修改时间'
    ];

    public function map($withdrawal): array
    {
        return [
            $withdrawal->id,
            $withdrawal->trade_no,
            $withdrawal->out_trade_no,
            UsersInfo::where('id', $withdrawal->uid)->value('nickname') . '(' . $withdrawal->uid . ')',
            $withdrawal->real_name,
            $withdrawal->id_card,
            data_get($withdrawal, 'userCards.name'),
            data_get($withdrawal, 'userCards.id_card'),
            $withdrawal->mobile,
            $withdrawal->amount,
            $withdrawal->withdrawal_type == 1 ? '微信' : '支付宝',
            $withdrawal->alipay,
            $withdrawal->wx_openid,
            Withdrawal::$status[$withdrawal->status],
            $withdrawal->remark,
            data_get($withdrawal, 'manage.name'),
            $withdrawal->created_at,
            $withdrawal->updated_at
        ];
    }
}

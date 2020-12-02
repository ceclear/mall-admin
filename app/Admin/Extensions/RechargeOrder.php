<?php

namespace App\Admin\Extensions;

use App\Models\Withdrawal;
use Encore\Admin\Admin;

class RechargeOrder
{
    protected $id;
    protected $no;
    protected $status;

    public function __construct($id, $no, $status)
    {
        $this->id = $id;
        $this->no = $no;
        $this->status = $status;
    }

    protected function script()
    {
        $token = csrf_token();
        return <<<SCRIPT

$('.refuse-withdrawal').on('click', function () {
    var no = $(this).data('no');
    var id = $(this).data('id');

    layer.confirm('退回「'+no+'」的充值？', {
        btn: ['确认','取消']
    }, function(){
        $.ajax({
            type: "POST",
            url: "../recharge/order/refund",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': '$token'
            },
            data: {
                'id': id
            },
            success: function(result) {
                if (result.code == 0) {
                    layer.msg('操作成功，稍候将自动刷新', {
                        time: 1000,
                    }, function(){
                        window.location.reload();
                    });
                } else {
                    layer.msg(result.msg, {
                        time: 1000,
                    });
                }
            },
        });
    }, function(){
        layer.closeAll();
    });
});

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        if (in_array($this->status, [\App\Models\RechargeOrder::RECHARGE_FAIL, \App\Models\RechargeOrder::PAY_SUCCESS])) {
            return "<a class='btn btn-xs btn-danger refuse-withdrawal' data-id='{$this->id}' data-no='{$this->no}'>退回</a>";
        }

        return "";
    }

    public function __toString()
    {
        return $this->render();
    }
}

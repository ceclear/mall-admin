<?php

namespace App\Admin\Extensions;

use App\Models\SalesTop;
use App\Models\Withdrawal;
use Encore\Admin\Admin;

class RechargeDisableAlert
{
    protected $id;
    protected $status;

    public function __construct($id, $status)
    {
        $this->id = $id;
        $this->status = $status;
    }

    protected function script()
    {
        $token = csrf_token();
        return <<<SCRIPT

$('.refuse-withdrawal').on('click', function () {
    var id = $(this).data('id');

    layer.confirm('确定这是作弊用户么？', {
        btn: ['确认','取消']
    }, function(){
        $.ajax({
            type: "POST",
            url: "../activitySimConfigs/saleRank/refund",
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

        if ($this->status == 0) {
            return "<a class='btn btn-xs btn-danger refuse-withdrawal' data-id='{$this->id}'>拒绝</a>";
        }

        return "";
    }

    public function __toString()
    {
        return $this->render();
    }
}

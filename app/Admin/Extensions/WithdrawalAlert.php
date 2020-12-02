<?php

namespace App\Admin\Extensions;

use App\Models\Withdrawal;
use Encore\Admin\Admin;

class WithdrawalAlert
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

$('.allow-withdrawal').on('click', function () {
    var no = $(this).data('no');
    var id = $(this).data('id');
    layer.confirm('同意「'+no+'」的提现？', {
        btn: ['确定','取消']
    }, function(){
        $.ajax({
            type: "POST",
            url: "../withdrawal/log/change",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': '$token'
            },
            data: {
                'id': id,
                'status': 1
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
                        time: 5000,
                    });
                }
            },
        });
    }, function(){
        layer.closeAll();
    });
});

$('.refuse-withdrawal').on('click', function () {
    var no = $(this).data('no');
    var id = $(this).data('id');
    layer.prompt({title: '拒绝「'+no+'」的提现？', formType: 0, value: ''}, function(pass, index){
        if(!pass) {
            return layer.msg('请填写拒绝理由');
        }
        $.ajax({
            type: "POST",
            url: "../withdrawal/log/change",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': '$token'
            },
            data: {
                'id': id,
                'remark': pass,
                'status': 3
            },
            success: function(result) {
                if (result.code == 0) {
                    layer.msg('操作成功，稍候将自动刷新', {
                        time: 1000,
                    }, function(){
                        window.location.reload();
                    });
                } else {
                    layer.msg('操作失败，请稍候重试', {
                        time: 1000,
                    });
                }
            },
        });
    });
});

$('.fail-withdrawal').on('click', function () {
    var no = $(this).data('no');
    var id = $(this).data('id');
    layer.prompt({title: '「'+no+'」的提现失败？', formType: 0, value: ''}, function(pass, index){
        if(!pass) {
            return layer.msg('请填写失败理由');
        }

        $.ajax({
            type: "POST",
            url: "../withdrawal/log/change",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': '$token'
            },
            data: {
                'id': id,
                'remark': pass,
                'status': 2
            },
            success: function(result) {
                if (result.code == 0) {
                    layer.msg('操作成功，稍候将自动刷新', {
                        time: 1000,
                    }, function(){
                        window.location.reload();
                    });
                } else {
                    layer.msg('操作失败，请稍候重试', {
                        time: 1000,
                    });
                }
            },
        });
    });
});

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        if ($this->status == Withdrawal::STATUS_PENDING) {
            return "<a class='btn btn-xs btn-success allow-withdrawal' data-id='{$this->id}' data-no='{$this->no}'>通过</a>&nbsp;<a class='btn btn-xs btn-danger refuse-withdrawal' data-id='{$this->id}' data-no='{$this->no}'>拒绝</a>&nbsp;<a class='btn btn-xs btn-warning fail-withdrawal' data-id='{$this->id}' data-no='{$this->no}'>失败</a>";
        }
        if ($this->status == Withdrawal::STATUS_JOB) {
            return "<a class='btn btn-xs btn-danger refuse-withdrawal' data-id='{$this->id}' data-no='{$this->no}'>拒绝</a>";
        }

        return "";
    }

    public function __toString()
    {
        return $this->render();
    }
}

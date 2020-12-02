<?php

namespace App\Admin\Extensions;

use App\Models\Withdrawal;
use Encore\Admin\Admin;

class PushAlert
{
    protected $id;
    protected $title;

    public function __construct($id, $title)
    {
        $this->id = $id;
        $this->title = $title;
    }

    protected function script()
    {
        $token = csrf_token();
        return <<<SCRIPT
$('.allow-push').on('click', function () {
    var id = $(this).data('id');
    var title = $(this).data('title');
    layer.confirm('马上推送标题为「'+title+'」的消息？', {
        btn: ['确定','取消']
    }, function(){
        $.ajax({
            type: "POST",
            url: "../push/now",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': '$token'
            },
            data: {
                'id': id
            },
            success: function(result) {
                if (result.code == 0) {
                    layer.msg('推送成功，稍候将自动刷新', {
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
    }, function(){
        layer.closeAll();
    });
});
SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        return "<a class='btn btn-xs btn-success allow-push' data-id='{$this->id}' data-title='{$this->title}'>推送</a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}

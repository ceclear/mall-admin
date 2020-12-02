<?php

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class EnableRow
{
    protected $id;
    protected $action;
    protected $value;
    protected $show;
    protected $icon;

    public function __construct($id,$action,$value,$show,$icon)
    {
        $this->id = $id;
        $this->action = $action;
        $this->value = $value;
        $this->show = $show;
        $this->icon = $icon;
    }

    protected function script()
    {
        return <<<SCRIPT

$('.grid-row-disable').on('click', function () {

    // Your code.
    console.log($(this).data('id'));
    console.log($(this).data('id'));
    var id=$(this).data('id');
    var val=$(this).data('val');
    var actionUrl=$(this).data('action');
    swal({
        title: "确认进行此操作吗？",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "确认",
        showLoaderOnConfirm: true,
        cancelButtonText: "取消",
        preConfirm: function() {
            $.ajax({
                type: 'GET',
                url: actionUrl, 
                dataType : "json",
                data : {
                    'id':id,
                    'value':val
                },
                success: function (data) {
                console.log(data);
                $.pjax.reload('#pjax-container');
                if(data.code==1){
                    swal(data.msg, '', 'success');
                }else{
                    swal(data.msg, '', 'error');
                }
            }
            });
            }
    });

});

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());
//        href='{$this->action}/{$this->value}'
        $style='margin-left:10px;cursor: pointer;';
        if($this->value==2)
            $style.='color:#fb7a7a';
        return "<a style='{$style}'  class='grid-row-disable' data-action='{$this->action}' data-val='{$this->value}' data-id='{$this->id}'><i class='fa {$this->icon}
'></i>{$this->show}</a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}
<?php

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class DeleteRow
{
    protected $id;
    protected $action;
    protected $value;
    protected $show;
    protected $icon;
    protected $model;

    public function __construct($id,$model,$action,$value,$show,$icon)
    {
        $this->id = $id;
        $this->action = $action;
        $this->value = $value;
        $this->show = $show;
        $this->icon = $icon;
        $this->model = $model;
    }

    protected function script()
    {
        return <<<SCRIPT

$('.grid-row-delete-new').on('click', function () {

    // Your code.
    console.log($(this).data('id'));
    console.log($(this).data('id'));
    var id=$(this).data('id');
    var val=$(this).data('val');
    var actionUrl=$(this).data('model')+'/'+$(this).data('id');
    swal({
        title: "确认删除吗？",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "确认",
        showLoaderOnConfirm: true,
        cancelButtonText: "取消",
        preConfirm: function() {
            $.ajax({
                type: 'POST',
                url: actionUrl, 
                dataType : "json",
                data : {
                    _method:'delete',
                    _token:LA.token,
                },
                success: function (data) {
                console.log(data);
                $.pjax.reload('#pjax-container');
                if(data.status){
                    swal(data.message, '', 'success');
                }else{
                    swal(data.message, '', 'error');
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
        return "<a style='{$style}'  class='grid-row-delete-new' data-action='{$this->action}' data-model='{$this->model}' data-val='{$this->value}' data-id='{$this->id}'><i class='fa {$this->icon}
'></i>{$this->show}</a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}
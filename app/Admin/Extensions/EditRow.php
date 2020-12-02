<?php

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class EditRow
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



SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());
        $style='margin-left:10px;cursor: pointer;';
        if($this->value==2)
            $style.='color:#fb7a7a';
        return "<a style='{$style}'  href='{$this->model}/{$this->id}/edit' data-action='{$this->action}' data-model='{$this->model}' data-val='{$this->value}' data-id='{$this->id}'><i class='fa {$this->icon}
'></i>{$this->show}</a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}
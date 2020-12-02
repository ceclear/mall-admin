<?php


namespace App\Admin\Extensions;


use Encore\Admin\Actions\Action;

class ButtonJump extends Action
{
    protected $url;
    public $name;
    public function __construct($url, $name)
    {
        $this->url = $url;
        $this->name = $name;
        parent::__construct();
    }
    public function html()
    {
        return '<a href="'.$this->url.'" class="btn btn-sm btn-twitter" title="'.$this->name.'">
        <i class="fa fa-circle-thin"></i><span class="hidden-xs">&nbsp;&nbsp;'.$this->name.'</span>
    </a>';
    }
}
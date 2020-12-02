<?php

namespace App\Admin\Actions;



use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class LinkHref extends RowAction
{
    public $name = '查看日志';
    protected $selector = '.clear-caches';

    public function handle(Model $model, Request $request)
    {
        // $model ...
    }

    public function href()
    {
        return "/admin/users/show-logs?uid=".$this->row['id'];
    }


}
<?php

namespace App\Admin\Actions;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class CeclearDelete extends RowAction
{
    public $name = '删除';
    protected $selector = '.clear-caches';

    public function handle(Model $model)
    {
        // $model ...
        $model->delete();
        return $this->response()->success('删除成功')->refresh();
    }

    public function dialog()
    {
        $this->confirm('确定删除');
    }


}
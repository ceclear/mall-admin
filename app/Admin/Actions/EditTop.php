<?php

namespace App\Admin\Actions;

use Encore\Admin\Actions\RowAction;
use Encore\Admin\Widgets\Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class EditTop extends RowAction
{
    public $name = '顶置';
    protected $selector = '.clear-caches';

    public function handle(Model $model, Request $request)
    {
        // $model ...
        $model->top = $request->input('top');
        $model->save();
        return $this->response()->success('顶置成功')->refresh();
    }

    public function form()
    {
        $type    = [
            1 => '第一位',
            2 => '第二位',
        ];
        $default = $this->row('top') ?? 2;
        $this->radio('top', '顶置到首页')->options($type)->value($default);

    }


}
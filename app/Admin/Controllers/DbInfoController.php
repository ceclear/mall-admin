<?php


namespace App\Admin\Controllers;


use Encore\Admin\Controllers\AdminController;

class DbInfoController extends AdminController
{
    public function processlist()
    {
        $ret = \DB::select("show full processlist");

        echo json_encode($ret, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);die;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const Response_success_code = 1;
    const Response_failed_code = 0;

    public function __construct()
    {
        ini_set('post_max_size','100M');
        ini_set('upload_max_filesize','100m');
    }

    protected function responseJson($code, $msg = '', $data = [])
    {
        if ( !$msg ) {
            if ( $code == self::Response_success_code ) {
                $msg = "操作成功";
            } else {
                $msg = "操作失败";
            }
        } else {
            //支持msg直接data
            if ( !is_string($msg) ) {
                $data = $msg;
                if ( $code == self::Response_failed_code ) {
                    $msg = "操作成功";
                } else {
                    $msg = "操作失败";
                }
            }
        }
        $ret = [
            "code" => $code,
            "msg" => $msg,
            "data" => $data
        ];
        return response()->json($ret);
    }
}

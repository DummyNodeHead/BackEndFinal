<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BaseController extends Controller
{
    //
    protected function create($data, $msg='',$code=200)
    {
        //
        $result=[
            'code'=>$code,
            'msg'=>$msg,
            'data'=>$data
        ];

        return response($result,$code);
    }
    public function success($data): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'code' => '0',
            'data' => $data,
        ]);
    }

    //错误返回
    public function fail($code, $data): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'code' => $code,
            'data' => $data,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
}

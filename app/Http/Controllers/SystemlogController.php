<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SystemlogController extends BaseController
{
    public function store(Request $request)
    {
        date_default_timezone_set('PRC');
        $time=date('Y-m-d H:i:s', time());
        $input = $request;
        $file = 'D:\system.txt';
        if(file_exists($file))
        {
            $fp=fopen($file,'a');
            fwrite($fp,$time);
            fwrite($fp,'-');
            fwrite($fp,'ID:');
            fwrite($fp,'-');
            fwrite($fp,$input['ID']);
            fwrite($fp,'-');
            fwrite($fp,$input['message']);
            fwrite($fp,"\n");
            return $this->create([],'插入成功',200);
        }
    }
}

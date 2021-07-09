<?php

namespace App\Http\Controllers;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Storage;
use App\Teacher;
use Illuminate\Http\Request;
use App\Student;
use App\Account;

class UpPhotoController extends BaseController
{
    public function store(Request $request)
    {
        if($request->isMethod('POST')){
            $file = $request->file('file');
  
           // 文件是否上传成功
           if ($file->isValid()) {
                $filename = $file->getClientOriginalName();//原文件名
                $ext = $file->getClientOriginalExtension();//文件拓展名
                $type = $file->getClientMimeType();//mimetype
                $path = $file->getRealPath();//绝对路径
                $filenames = time().uniqid().".".$ext;//设置文件存储名称

                $res = Storage::disk('uploads')->put($filenames,file_get_contents($path));

                //判断是否创建成功
               if (!$res)
               {
                   return $this->responseError('添加图片失败', $this->status_blackvirus_insert_img_error);
               }
           }
       }
    }
}
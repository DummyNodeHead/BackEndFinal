<?php

namespace App\Http\Controllers;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Storage;
use App\Teacher;
use Illuminate\Http\Request;
use App\Student;
use App\Account;

class UpPhotoStuController extends BaseController
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
                $dataa=$request->all();
                $id = $dataa['id'];
                Student::where('ID', '=', $id)->update(['photoURL' => $filenames]);
                
           }
       }
    }
}
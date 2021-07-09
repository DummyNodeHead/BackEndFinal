<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\PersonalResources;

class SourceController extends Controller
{
   
    //TeacherCenter
    public function TeacherPersonalResource(Request $request)
    {
        $teacherid = $request['id'];
        $name = $request['name'];

        $where = [];

        $where['Teacher_id']=$teacherid;
        if ($name) {
            $where[] = ['Path', 'like', '%'.$name.'%'];
        }
        $personalresource = DB::table('personal_resources')->where($where)->orderBy('Submit_time', 'desc')->get();

        return response()->json([
             'status' => 200,
             'personalresource' => $personalresource,
         ]);
    }

    public function TeacherAddPersonalResource($path, $name,$originalName, $size, $uid)
    {
        $personalResources=new PersonalResources;
        $personalResources->Path=$path;
        $personalResources->Resource_name=$name;
        $personalResources->OriginalName=$originalName;
        $personalResources->Submit_time=NOW();
        $personalResources->Teacher_id=$uid;
        $personalResources->Resource_space=$size;

        $personalResources->save();
        return true;
    }

    public function TeacherDeletePersonalResource(Request $request)
    {
        $teacherid = $request['id'];
        $name = $request['name'];

        //删除资源

        $deletedRows = PersonalResources::where('Resource_name', $name)  ->where('Teacher_id', $teacherid)->delete();
        if ($deletedRows<=0) {
            return response()->json([
                'status' => -200,
                'msg'=>"删除失败"]);
        }

        return response()->json(['status' => 200]);
    }

    public function TeacherDownloadPersonalResource(Request $request)
    {
        $name=$request["name"];
        if (!$name) {
            return response()->json([
            'status' => -200,
            'msg'=>"请传入文件名"
        ]);
        }
        return response()->json([
            'status' => 200,
            'url'=> Storage::url($name),
            'msg'=>""
        ]);
    }

    public function TeacherUploadImage(Request $request)
    {
        $file = $request->file('file');
        $teacherid = $request['id'];

       
        if (!$file->isValid()) {
            return false;
        }

        $size=$this->getFilesize($file->getClientSize());
        $originalName = $file->getClientOriginalName();//原文件名
        $ext = $file->getClientOriginalExtension();//文件拓展名
        $type = $file->getClientMimeType();//mimetype
        $path = $file->getRealPath();//绝对路径
      
        $filenames = time().uniqid().".".$ext;//设置文件存储名称

        $newPath= "app/public/".$filenames;

        $res = Storage::disk("public")->put($filenames, file_get_contents($path));

        //判断是否创建成功
        if (!$res) {
            return $this->responseError('添加图片失败', $this->status_blackvirus_insert_img_error);
        }

        $addRes=  $this->TeacherAddPersonalResource($newPath, $filenames,$originalName, $size, $teacherid);
        if (!$addRes) {
            return response()->json([
            'status' => -200,
            'msg' => "上传失败",
        ]);
        }

        return response()->json([
                'status' => 200,
                'msg' => "上传成功",
            ]);
    }

    //StudentCenter
    public function StudentPersonalResource(Request $request)
    {
        $studentid = $request['id'];
        $name = $request['name'];

        $where = [];

        $where['Student_id']=$studentid;
        if ($name) {
            $where[] = ['Path', 'like', '%'.$name.'%'];
        }
        $personalresource = DB::table('personal_resources')->where($where)->orderBy('Submit_time', 'desc')->get();

        return response()->json([
             'status' => 200,
             'personalresource' => $personalresource,
         ]);
    }

    public function StudentAddPersonalResource($path, $name,$originalName, $size, $uid)
    {
        $personalResources=new PersonalResources;
        $personalResources->Path=$path;
        $personalResources->Resource_name=$name;
        $personalResources->OriginalName=$originalName;
        $personalResources->Submit_time=NOW();
        $personalResources->Student_id=$uid;
        $personalResources->Resource_space=$size;

        $personalResources->save();
        return true;
    }

    public function StudentDeletePersonalResource(Request $request)
    {
        $studentid = $request['id'];
        $name = $request['name'];

        //删除资源

        $deletedRows = PersonalResources::where('Resource_name', $name)  ->where('Student_id', $studentid)->delete();
        if ($deletedRows<=0) {
            return response()->json([
                'status' => -200,
                'msg'=>"删除失败"]);
        }

        return response()->json(['status' => 200]);
    }

    public function StudentDownloadPersonalResource(Request $request)
    {
        $name=$request["name"];
        if (!$name) {
            return response()->json([
            'status' => -200,
            'msg'=>"请传入文件名"
        ]);
        }
        return response()->json([
            'status' => 200,
            'url'=> Storage::url($name),
            'msg'=>""
        ]);
    }


    public function StudentUploadImage(Request $request)
    {
        $file = $request->file('file');
        $studentid = $request['id'];

       
        if (!$file->isValid()) {
            return false;
        }

        $size=$this->getFilesize($file->getClientSize());
        $originalName = $file->getClientOriginalName();//原文件名
        $ext = $file->getClientOriginalExtension();//文件拓展名
        $type = $file->getClientMimeType();//mimetype
        $path = $file->getRealPath();//绝对路径
      
        $filenames = time().uniqid().".".$ext;//设置文件存储名称

        $newPath= "app/public/".$filenames;

        $res = Storage::disk("public")->put($filenames, file_get_contents($path));

        //判断是否创建成功
        if (!$res) {
            return $this->responseError('添加图片失败', $this->status_blackvirus_insert_img_error);
        }

        $addRes=  $this->StudentAddPersonalResource($newPath, $filenames,$originalName, $size, $studentid);
        if (!$addRes) {
            return response()->json([
            'status' => -200,
            'msg' => "上传失败",
        ]);
        }

        return response()->json([
                'status' => 200,
                'msg' => "上传成功",
            ]);
    }


    public function getFilesize($num)
    {
        $p      = 0;
        $format = 'bytes';
        if ($num > 0 && $num < 1024) {
            $p = 0;
            return number_format($num) . ' ' . $format;
        }
        if ($num >= 1024 && $num < pow(1024, 2)) {
            $p      = 1;
            $format = 'KB';
        }
        if ($num >= pow(1024, 2) && $num < pow(1024, 3)) {
            $p      = 2;
            $format = 'MB';
        }
        if ($num >= pow(1024, 3) && $num < pow(1024, 4)) {
            $p      = 3;
            $format = 'GB';
        }
        if ($num >= pow(1024, 4) && $num < pow(1024, 5)) {
            $p      = 3;
            $format = 'TB';
        }
        $num /= pow(1024, $p);
        return number_format($num, 3) . ' ' . $format;
    }
}

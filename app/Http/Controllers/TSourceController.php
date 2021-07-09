<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TSource;

class TSourceController extends Controller
{
    //studentcenter
    public function showCourseResource(Request $request)
    {
        $classID = $request['classID'];
        $name = $request['name'];

        $where = [];

        $where['Class_id']=$classID;
        if ($name) {
            $where[] = ['Path', 'like', '%'.$name.'%'];
        }
        $courseresource = DB::table('course_resources')->where($where)->orderBy('Submit_time', 'desc')->get();

        return response()->json([
             'status' => 200,
             'courseresource' => $courseresource,
         ]);
    }

    //TeacherCenter
    public function showTeacherCourseResource(Request $request)
    {
        $classID = $request['classID'];
        $name = $request['name'];

        $where = [];

        $where['Class_id']=$classID;
        if ($name) {
            $where[] = ['Path', 'like', '%'.$name.'%'];
        }
        $courseresource = DB::table('course_resources')->where($where)->orderBy('Submit_time', 'desc')->get();

        return response()->json([
             'status' => 200,
             'courseresource' => $courseresource,
         ]);
    }

    public function TeacherAddCourseResource($path, $name,$originalName, $size, $classID)
    {
        $courseResources=new TSource;
        $courseResources->Path=$path;
        $courseResources->Resource_name=$name;
        $courseResources->OriginalName=$originalName;
        $courseResources->Submit_time=NOW();
        $courseResources->Class_id=$classID;
        $courseResources->Resource_space=$size;

       $affected= $courseResources->save();
       if( $affected>0){
           return true;
       }
       else{
           return false;
       }
    }

    public function TeacherDeleteCourseResource(Request $request)
    {
        $classID = $request['classID'];
        $name = $request['name'];

        //删除资源

        $deletedRows = TSource::where('Resource_name', $name)  ->where('Class_id', $classID)->delete();
        if ($deletedRows<=0) {
            return response()->json([
                'status' => -200,
                'msg'=>"删除失败"]);
        }

        return response()->json(['status' => 200]);
    }

    public function TeacherUpload(Request $request)
    {
        $file = $request->file('file');
        $classID = $request['classID'];

       
        if (!$file->isValid()) {
            return false;
        }

        $size=$this->getFilesize($file->getClientSize());
        $originalName = $file->getClientOriginalName();//原文件名
        $ext = $file->getClientOriginalExtension();//文件拓展名
        $type = $file->getClientMimeType();//mimetype
        $path = $file->getRealPath();//绝对路径
      
        $filenames = time().uniqid().".".$ext;//设置文件存储名称

        $newPath= "app/public".$filenames;

        $res = Storage::disk("public")->put($filenames, file_get_contents($path));

        //判断是否创建成功
        if (!$res) {
            return $this->responseError('添加图片失败', $this->status_blackvirus_insert_img_error);
        }

        $addRes=  $this->TeacherAddCourseResource($newPath, $filenames,$originalName, $size,$classID);
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

    public function DownloadResource(Request $request)
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

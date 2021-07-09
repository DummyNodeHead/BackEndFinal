<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class TSourceController extends Controller
{
    //studentcenter
    public function showCourseResource(){
        $classid = '0000000001';
        $courseresource = DB::table('course_resources')->where('Class_id',$classid)->orderBy('Submit_time','asc')->get();
        return response()->json([
            'status' => 200,
            'courseresource' => $courseresource,
        ]);
    }

    //TeacherCenter
    public function showTeacherCourseResource(){
        $classid = '0000000001';
        $courseresource = DB::table('course_resources')->where('Class_id',$classid)->orderBy('Submit_time','asc')->get();
        return response()->json([
            'status' => 200,
            'courseresource' => $courseresource,
        ]);
    }
}

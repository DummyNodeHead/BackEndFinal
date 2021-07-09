<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class SourceController extends Controller
{
    //StudentCenter
    public function showPersonalResource(){
        $studentid = '3190100123';
        $personalresource = DB::table('personal_resources')->where('Student_id',$studentid)->orderBy('Submit_time','asc')->get();
        return response()->json([
            'status' => 200,
            'personalresource' => $personalresource,
        ]);
    }
    //TeacherCenter
    public function showTeacherPersonalResource(){
        $teacherid = '2190100123';
        $personalresource = DB::table('personal_resources')->where('Teacher_id',$teacherid)->orderBy('Submit_time','asc')->get();
        return response()->json([
            'status' => 200,
            'personalresource' => $personalresource,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class CourseController extends Controller
{
    //
    public function showAllCourse(){
        $course = DB::table('course')->get();
        return response()->json([
            'status' => 200,
            'course' => $course,
        ]);
    }
}

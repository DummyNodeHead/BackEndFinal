<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Grade;

class GradeController extends Controller
{
    //student center
    //Quiz
    public function getInfo(Request $request)
    {
        $studentid = $request['id'];
        $grade = Grade::where('student_id',$studentid)->first();
        return response()->json([
            'status' => 200,
            'grade' => $grade,
        ]);
    }
}

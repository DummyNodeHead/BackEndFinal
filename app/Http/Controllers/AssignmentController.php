<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assignment;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    //student center
    public function showAssignment(){
        $classid = '0000000001';
        $assignment = DB::table('assignment')->groupBy('Assignment_id')->having('Class_id',$classid)->orderBy('Assignment_id','asc')->get();
        return response()->json([
            'status' => 200,
            'assignment' => $assignment,
        ]);
    }
    //student center hwanalysis
    public function showAssignmentScore(){
        $classid = '0000000001';
        $studentid = '3190100123';
        $assignment = DB::table('assignment')->groupBy('Assignment_id')->having('Class_id',$classid)->having('Student_id',$studentid)->orderBy('Assignment_id','asc')->get();
        return response()->json([
            'status' => 200,
            'assignment' => $assignment,
        ]);
    }
    //student center addassignment
    public function getAssignmentID(){
        $id = DB::table('assignment')->select('Assignment_id')->orderBy('Assignment_id','desc')->first();
        return response()->json([
            'status' => 200,
            'assignmentid' => $id,
        ]);
    }
    //student center
    public function store(Request $req){
        $assignment = new Assignment;
        $assignment->Assignment_title = $req->input('Assignment_title');
        $assignment->Assignment_content = $req->input('Assignment_content');
        $assignment->Score_percent = $req->input('Score_percent');
        //$assignment->Start_time = $req->input('Start_time');
        //$assignment->End_time = $req->input('End_time');
        $assignment->save();
        return response()->json([
            'status' => 200,
            'message' => 'Assignment Added Successfully',
        ]);
    }

    //teacher center
    //HWanalysis
    public function showStudentAssignmentScore(){
        $classid = '0000000001';
        $assignment = DB::table('assignment')->where('Class_id',$classid)->orderBy('Assignment_id','asc')->get();
        return response()->json([
            'status' => 200,
            'assignment' => $assignment,
        ]);
    }
    //HWmarking
    public function showStudentAssignment(){
        $classid = '0000000001';
        $assignment = DB::table('assignment')->where('Class_id',$classid)->orderBy('Assignment_id','asc')->get();
        return response()->json([
            'status' => 200,
            'assignment' => $assignment,
        ]);
    }
}

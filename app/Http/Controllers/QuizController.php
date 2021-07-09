<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class QuizController extends Controller
{
    //student center
    //Quiz
    public function showQuiz(){
        $studentid = '3190100123';
        $classid = '0000000001';
        $quiz = DB::table('quiz')->where('Student_id',$studentid)->where('Class_id',$classid)->get();
        return response()->json([
            'status' => 200,
            'quiz' => $quiz,
        ]);
    }
    //Grade
    public function showGrade(){
        $studentid = '3190100123';
        $quiz = DB::table('quiz')->where('Student_id',$studentid)->get();
        return response()->json([
            'status' => 200,
            'quiz' => $quiz,
        ]);
    }

    //teachercenter
    //student analysis
    public function showStudentAnalysisScore(){
        $studentid = '3190100123';
        $classid = '0000000001';
        $quiz = DB::table('quiz')->where('Student_id',$studentid)->where('Class_id',$classid)->get();
        return response()->json([
            'status' => 200,
            'quiz' => $quiz,
        ]);
    }

    //quiz analysis
    public function showQuizAnalysisScore(){
        $classid = '0000000001';
        $quiz = DB::table('quiz')->where('Class_id',$classid)->orderBy('Quiz_id','asc')->get();
        return response()->json([
            'status' => 200,
            'quiz' => $quiz,
        ]);
    }
    
    //Quiz
    public function showStudentQuiz(){ 
        $classid = '0000000001';
        $quiz = DB::table('quiz')->select('Quiz_score')->where('Class_id',$classid)->orderBy('Quiz_id','asc')->get();
        return response()->json([
            'status' => 200,
            'quiz' => $quiz,
        ]);
    }

    /*
      public function showAnalysisChart(){
        $classid = '20049589';
        //select sum(Quiz_score) group by Quiz_id where Class_id = 'xxx';
        $quiz = DB::table('quiz')->where('Class_id',$classid)->orderBy('Quiz_id','asc')->get();
        return response()->json([
            'status' => 200,
            'quiz' => $quiz,
        ]);
      }
     */
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Student;
use App\CourseSelect;
use App\Course;
use App\Quiz;
use App\Grade;

class QuizController extends Controller
{
    //student center
    //Quiz
    public function showQuiz(Request $request)
    {
        $studentid = $request['id'];
        $classID = $request['classID'];
        $quiz = DB::table('quiz')->where('Student_id', $studentid)->where('Class_id', $classID)->get();
        return response()->json([
            'status' => 200,
            'quiz' => $quiz,
        ]);
    }
    //Grade
    public function showGrade()
    {
        $studentid = '3190100123';
        $quiz = DB::table('quiz')->where('Student_id', $studentid)->get();
        return response()->json([
            'status' => 200,
            'quiz' => $quiz,
        ]);
    }

    //teachercenter
    //student analysis
    public function showStudentAnalysisScore()
    {
        $studentid = '3190100123';
        $classid = '0000000001';
        $quiz = DB::table('quiz')->where('Student_id', $studentid)->where('Class_id', $classid)->get();
        return response()->json([
            'status' => 200,
            'quiz' => $quiz,
        ]);
    }

    //quiz analysis
    public function showQuizAnalysisScore(Request $request)
    {
        $classID = $request['classID'];
        $quiz = DB::table('quiz')->where('Class_id', $classID)->get();
        return response()->json([
            'status' => 200,
            'quiz' => $quiz,
            'chart'=>$this->getChartData($classID)
        ]);
    }

    public function updateApplicationState(Request $request)
    {
        $Quiz_id = $request['id'];
        $type = $request['type'];

        $quizRes=Quiz::where("Quiz_id", $Quiz_id)->where("Application", 2)->first();

        if ($type==1) {
            $quizRes->Quiz_score=0;
        }
        $quizRes->Application=1;
        $affected= $quizRes->save();

        if ($affected<=0) {
            return response()->json([
                'status' => -200,
                'msg' => "申请处理失败",
            ]);
        }

        
        return response()->json([
            'status' => 200
        ]);
    }

    public function ManageGetList()
    {
        $quiz = DB::table('quiz')->where('Application', 2)->get();
        foreach ($quiz  as $k => &$v) {
            $courseInfo=Course::where("ID", $v->Class_id)->first();
            if ($courseInfo) {
                $v->className=$courseInfo->name;
            } else {
                $v->className="";
            }
        }

        return response()->json([
            'status' => 200,
            'quiz' => $quiz
        ]);
    }

    public function getChartData($classID)
    {
        $level1=DB::select('select count(*) as number from quiz where Class_id=? and Quiz_score<60', [$classID]);
        $level2=DB::select('select count(*) as number from quiz where Class_id=? and Quiz_score BETWEEN 60 and 70', [$classID]);
        $level3=DB::select('select count(*) as number from quiz where Class_id=? and Quiz_score BETWEEN 71 and 80', [$classID]);
        $level4=DB::select('select count(*) as number from quiz where Class_id=? and Quiz_score BETWEEN 81 and 90', [$classID]);
        $level5=DB::select('select count(*) as number from quiz where Class_id=? and Quiz_score BETWEEN 91 and 100', [$classID]);
        $rank=DB::select('select Quiz_score,student_id, row_number() over(order by Quiz_score desc) as rankNumber from quiz where Class_id=? limit 10', [$classID]);

        foreach ($rank  as $k => &$v) {
            $studentInfo=Student::where("ID", $v->student_id)->first();
            if ($studentInfo) {
                $v->studentName=$studentInfo->name;
            } else {
                $v->studentName="";
            }
        }
    
    
        return  ([
        "level1"=>$level1[0]->number,
        "level2"=>$level2[0]->number,
        "level3"=>$level3[0]->number,
        "level4"=>$level4[0]->number,
        "level5"=>$level5[0]->number,
        "rank"=>$rank
      ]);
    }

    //申请修改成绩
    public function applicationScore(Request $request)
    {
        $teacherID = $request["teacherID"];
        $Quiz_id = $request["qID"];
        
        //查询该学员是否参与考试
        $quizRes=Quiz::where("Quiz_id", $Quiz_id)->first();
        //参与考试更新成绩
        if (!$quizRes) {
            return response()->json([
                'status' => -200,
                'msg' => "该学员未参加考试",
            ]);
        }

        if ($quizRes->Application==2) {
            return response()->json([
                'status' => -200,
                'msg' => "已申请，请等待审核",
            ]);
        }
        $quizRes->Application=2;
        $affected= $quizRes->save();

        if ($affected<=0) {
            return response()->json([
                'status' => -200,
                'msg' => "申请失败",
            ]);
        }
        return response()->json([
            'status' => 200
        ]);
    }

    

    //新增或编辑成绩
    public function updateScore(Request $request)
    {
        $classID = $request["classID"];
        $teacherID = $request["teacherID"];
        $studentID = $request["studentID"];
        $score = $request["score"];
        $title = $request["title"];
        $content = $request["content"];
        $affected =0;
        //查询学员信息
        $studentInfo=Student::where("ID", $studentID)->first();
        $studentName=$studentInfo["name"];

        //查询该学员是否参与考试
        $quizRes=Quiz::where("Student_id", $studentID)->where("Class_id", $classID)->first();
        //参与考试更新成绩
        if ($quizRes) {
            $quizRes->Quiz_score=$score;
            $quizRes->Quiz_title=$title;
            $quizRes->Quiz_content=$content;
            $affected=   $quizRes->save();
        } else {
            //未参与考试新增考试成绩
            $quiz=new Quiz;
            $quiz->Quiz_score= $score;
            $quiz->Quiz_title=$title;
            $quiz->Quiz_content=$content;
            $quiz->Student_id= $studentID;
            $quiz->Teacher_id= $teacherID;
            $quiz->Class_id= $classID;
            $quiz->Application= 1;
            $quiz->Quiz_time= Now();
            $affected=  $quiz->save();
        }
   
        if ($affected<=0) {
            return response()->json([
                   'status' => -200,
                   'msg' => "更新成绩失败",
               ]);
        }

        
        //查询总分,平均分
        $scoreInfo=DB::select('select sum(Quiz_score) as totalScore ,avg(Quiz_score) as avgScore from quiz where Student_id =?', [$studentID]);

        $totalScore = $scoreInfo[0]->totalScore;
        $avgScore= $scoreInfo[0]->avgScore;
        $gpa=$this->returnGPA($avgScore);
       
        //删除成绩分析表 避免重复
        Grade::where('student_id', $studentID)->delete();
        //新增成绩分析表
        $grade=new Grade;
        $grade->student_name=$studentName;
        $grade->student_id=$studentID;
        $grade->total_credit=$totalScore;
        $grade->average_score=  $avgScore;
        $grade->gpa=$gpa;
        $affectedGrade= $grade->save();

           
        if ($affectedGrade<=0) {
            return response()->json([
                   'status' => -200,
                   'msg' => "更新成绩失败",
               ]);
        }

        return response()->json([
               'status' => 200
           ]);
    }
    

    //查询课程列表
    public function showStudentQuiz(Request $request)
    {
        $classID = $request["classID"];
        $teacherID = $request["teacherID"];
        //查询所有加入该课程的学员
        $courses=CourseSelect::join('student', 'course_select.Student_id', 'student.ID')
        ->where('course_select.Course_id', $classID)
        ->where('course_select.IsSelected', 1)
        ->select('student.name as studentName', 'student.ID as studentID')
        ->get();
        //查询该学员是否参加考试
        foreach ($courses  as $k => &$v) {
            $quiz= Quiz::where('Class_id',$classID)->where("Student_id", $v->studentID)->first();
            if ($quiz) {
                $v->join=true;
                $v->score= $quiz->Quiz_score;
                $v->Quiz_id= $quiz->Quiz_id;
                $v->Application= $quiz->Application;
                $v->Quiz_title= $quiz->Quiz_title;
                $v->Quiz_content= $quiz->Quiz_content;
            } else {
                $v->join=false;
                $v->score= 0;
                $v->Application= 1;
                $v->Quiz_title= "";
                $v->Quiz_content= "";
            }
        }
        //$quiz = DB::table('quiz')->where('Class_id', $classID)->where('Teacher_id', $teacherID)->orderBy('Quiz_id', 'asc')->get();
        return response()->json([
            'status' => 200,
            'quiz' => $courses,
        ]);
    }


    public function returnGPA($grade)
    {
        if ($grade>=95 && $grade<=100) {
            return 5.0;
        } elseif ($grade>=92 && $grade<=94) {
            return 4.8;
        } elseif ($grade>=89 && $grade<=91) {
            return 4.5;
        } elseif ($grade>=86 && $grade<=88) {
            return 4.2;
        } elseif ($grade>=83 && $grade<=85) {
            return 3.9;
        } elseif ($grade>=80 && $grade<=82) {
            return 3.7;
        } elseif ($grade>=77 && $grade<=79) {
            return 3.5;
        } elseif ($grade>=75 && $grade<=76) {
            return 3.3;
        } elseif ($grade>=72 && $grade<=74) {
            return 3.1;
        } elseif ($grade>=60 && $grade<=71) {
            return 3.0;
        } else {
            return 0;
        }
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

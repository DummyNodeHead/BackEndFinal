<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Student;
use App\Teacher;
use App\Course;
use App\Models\Assignment;

class AssignmentController extends Controller
{
    //student center
    public function showAssignment()
    {
        $classid = '0000000001';
        $assignment = DB::table('assignment')->groupBy('Assignment_id')->having('Class_id', $classid)->orderBy('Assignment_id', 'asc')->get();
        return response()->json([
            'status' => 200,
            'assignment' => $assignment,
        ]);
    }
    //student center hwanalysis
    public function showAssignmentScore()
    {
        $classid = '0000000001';
        $studentid = '3190100123';
        $assignment = DB::table('assignment')->groupBy('Assignment_id')->having('Class_id', $classid)->having('Student_id', $studentid)->orderBy('Assignment_id', 'asc')->get();
        return response()->json([
            'status' => 200,
            'assignment' => $assignment,
        ]);
    }
    //student center addassignment
    public function getAssignmentID()
    {
        $id = DB::table('assignment')->select('Assignment_id')->orderBy('Assignment_id', 'desc')->first();
        return response()->json([
            'status' => 200,
            'assignmentid' => $id,
        ]);
    }
    //student center
    public function store(Request $req)
    {
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
    public function showStudentAssignmentScore()
    {
        $classid = '0000000001';
        $assignment = DB::table('assignment')->where('Class_id', $classid)->orderBy('Assignment_id', 'asc')->get();
        return response()->json([
            'status' => 200,
            'assignment' => $assignment,
        ]);
    }
    //HWmarking
    public function showStudentAssignment()
    {
        $classid = '0000000001';
        $assignment = DB::table('assignment')->where('Class_id', $classid)->orderBy('Assignment_id', 'asc')->get();
        return response()->json([
            'status' => 200,
            'assignment' => $assignment,
        ]);
    }

    //????????????????????????

    public function TeacherGetList(Request $request)
    {
        $teacherID = $request['teacherID'];
        $classID = $request['classID'];
        $name = $request['name'];
   
        $where = [];
   
        $where['Teacher_id']=$teacherID;
        $where['Class_id']=$classID;
        $data = DB::table('assignment')->where($where)->orderBy('Start_time', 'desc')->get();
        foreach ($data as $k => &$v) {
            $class=Course::where("ID", $v->Class_id)->first();
            if ($class) {
                $v->className= $class->name;
            } else {
                $v->className="";
            }
        }
        return response()->json([
                'status' => 200,
                'data' => $data,
            ]);
    }

    public function DownloadResource(Request $request)
    {
        $name=$request["name"];
        if (!$name) {
            return response()->json([
               'status' => -200,
               'msg'=>"??????????????????"
           ]);
        }
        return response()->json([
               'status' => 200,
               'url'=> Storage::url($name),
               'msg'=>""
           ]);
    }

    //??????????????????

    public function GetList(Request $request)
    {
        $studentid = $request['id'];
        $classID = $request['classID'];
        $name = $request['name'];

        $where = [];

        $where['Student_id']=$studentid;
        $where['Class_id']=$classID;
        $personalresource = DB::table('assignment')->where($where)->orderBy('Start_time', 'desc')->get();
        foreach ($personalresource as $k => &$v) {
            $class=Course::where("ID", $v->Class_id)->first();
            if ($class) {
                $v->className= $class->name;
            } else {
                $v->className="";
            }
        }
        return response()->json([
             'status' => 200,
             'personalresource' => $personalresource,
         ]);
    }

    //????????????

    public function StudentUploadAssignment(Request $request)
    {
        $file = $request->file('file');
        $studentid = $request['id'];
        $assignment_id= $request['assignment_id'];
       
        if (!$file->isValid()) {
            return false;
        }

        $filename = $file->getClientOriginalName();//????????????
        $ext = $file->getClientOriginalExtension();//???????????????
        $type = $file->getClientMimeType();//mimetype
        $path = $file->getRealPath();//????????????
      
        $filenames = time().uniqid().".".$ext;//????????????????????????

        $newPath= "app/public/".$filenames;

        $res = Storage::disk("public")->put($filenames, file_get_contents($path));

        //????????????????????????
        if (!$res) {
            return $this->responseError('??????????????????', $this->status_blackvirus_insert_img_error);
        }

        //??????????????????
        $addRes=  $this->AssignmentUpdate($assignment_id, $studentid, $filenames);
        if (!$addRes) {
            return response()->json([
            'status' => -200,
            'msg' => "????????????",
        ]);
        }

        return response()->json([
                'status' => 200,
                'msg' => "????????????",
            ]);
    }

    public function AssignmentUpdate($assignment_id, $studentid, $path)
    {
        $affected = DB::update('update assignment set Submit_content = ?,Submit_state="?????????" where Assignment_id = ? and (Start_time<now() and End_time>now())', [$path,$assignment_id]);
        if ($affected>0) {
            return true;
        } else {
            return false;
        }
    }

    //????????????
    public function AssignmentAdd(Request $request)
    {
        $title=  $request["Assignment_title"];
        $classID=  $request["classID"];
        $content=  $request["Assignment_content"];
        $startDate=  $request["start_date"];
        $endDate=  $request["end_date"];
        $score=  $request["Score_percent"];
        $teacherID=  $request["teacherID"];


        //?????????????????????????????????
        $userRes=Student::join('course_select', 'student.ID', 'course_select.Student_id')
        ->where('course_select.Course_id', $classID)
        ->where('course_select.IsSelected', 1)
        ->select('student.*')
        ->get();

        //??????????????????
        if (count($userRes)<=0) {
            return response()->json([
        'status' => -200,
        'msg' =>"???????????????????????????????????????????????????" ]);
        }
        
        $data= [];

        foreach ($userRes as  $info) {
            $params = [
                'Class_id' => $classID,
                'Student_id' => $info->ID,
                'Teacher_id' => $teacherID,
                'Assignment_title' => $title,
                'Assignment_content' => $content,
                'Start_time' => date('Y-m-d H:i:s', strtotime($startDate)),
                'End_time' =>  date('Y-m-d H:i:s', strtotime($endDate)),
                'Score_percent' => $score,
                'Submit_state' => "?????????",
                'Submit_content' => "",
                'Assignment_score' => 0
            ];
           
            \array_push($data, $params);
        }
        //??????????????????
        $addRes  = DB::table('assignment')->insert($data);
        if ($addRes<=0) {
            return response()->json([
        'status' => -200,
        'msg' => "??????????????????"
    ]);
        }

        return response()->json([
    'status' => 200
]);
    }
}

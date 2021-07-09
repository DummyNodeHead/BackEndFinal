<?php


namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DatabaseController extends BaseController
{
    //------------------------------自动排课数据库相关函数---------------------------------//
    //取出课程ID
    public function classID(): array
    {
        $user=DB::table("course")->select('ID')->get();
        $j=0;
        $result=Array();
        foreach($user as $value){
            $result[$j]=$value->ID;
            $j++;
        }
        return $result;
    }


    //取出课程容量（ID=>容量）关联数组
    public function classIDandVolumn(): array
    {
        $user=DB::table("course")->select('*')->get();
        $j=0;
        $result1=Array();
        foreach($user as $value){
            $result1[$value->ID]=$value->stock;
            $j++;
        }
        return $result1 ;
    }

    //取出教室ID
    public function classroomID(): array
    {
        $user=DB::table('Classroom')->select('Classroom_id')->get();
        $j=0;
        $result=Array();
        foreach($user as $value){
            $result[$j]=$value->Classroom_id;
            $j++;
        }
        return $result;
    }

    //取出教室容量（ID=>容量）关联数组
    public function classroomIDandVolumn(): array
    {
        $user=DB::table("Classroom")->select('*')->get();
        $j=0;
        $result1=Array();
        foreach($user as $value){
            $result1[$value->Classroom_id]=$value->Classroom_capacity;

            $j++;
        }
        return $result1 ;
    }

    //取出教师ID
    public function teacherID(): array
    {
        $user=DB::table('Teacher')->select('ID')->get();
        $j=0;
        $result=Array();
        foreach($user as $value){
            $result[$j]=$value->ID;

            $j++;
        }
        return $result;
    }

    //取出课程-教师表及课程对应课时
    public function Class_Teacher()
    {
        $user=DB::table('tc')->join('course',function($join){
            $join->on('course_ID','=','ID');
        })->select('course_ID', 'tc.teacher_ID as steacher_ID', 'time')->get();
        $j=0;
        $result1=Array();
        $result2=Array();
        $result3=Array();
        foreach($user as $value){
            $result1[$j]=$value->course_ID;
            $result2[$j]=$value->steacher_ID;
            $result3[$j]=$value->time;
            $j++;
        }
        return array($result1,$result2,$result3);
    }

    //存储排课结果
    public function storeClass_Teacher($temp)
    {
        $length = count($temp);
        $courseID = array_column($temp, 'courseID');
        $teacherID = array_column($temp, 'teacherID');
        $time = array_column($temp, 'time');
        $roomID = array_column($temp, 'roomID');
        $weekday = array_column($temp, 'weekday');
        $slot = array_column($temp, 'slot');

        $courseID1 = $courseID;
        $teacherID1 = $teacherID;
        $time1 = $time;
        $roomID1 = $roomID;
        $weekday1 = $weekday;
        $slot1 = $slot;
        for ($i = 0; $i < $length; $i++) {
            $time1[$i] = $time1[$i] . $weekday1[$i] . $slot1[$i] ;
        }
        for ($m = 0; $m < $length; $m++) {
            DB::table('schedule')
                ->insert(['course_id' => $courseID1[$m], 'Teacher_id' => $teacherID1[$m], 'Classroom_id' => $roomID1[$m],  'Time' => $time1[$m]]);
        }
    }

    public function DeleteScheduleAll(){
        DB::table("schedule")->delete();
    }
    //------------------------------自动排课数据库相关函数---------------------------------//


    //------------------------------手动排课数据库相关函数---------------------------------//


    //一个字符串，判断是否在排课表的教室、教师属性中
    public function CourseOrTeacher($data): int
    {
        $user=DB::table("schedule")->select('*')->where('Teacher_id','=',$data)->get();
        $arr = array();
        foreach($user as $value){
            array_push($arr, $value);
        }
        if(empty($arr)){
            $user1=DB::table("schedule")->select('*')->where('Classroom_id','=',$data)->get();
            $arr = array();
            foreach($user1 as $value){
                array_push($arr, $value);
            }
            if(empty($arr)){
                return 0;
            }
            else{
                return 1;
            }
        }
        else{
            return 2;
        }
    }

    //根据教师姓名获得教师ID（默认姓名为UNIQUE）
    public function TeacherIDFromName($name): string
    {
        $data=DB::table("Teacher")->select('ID')->where('name','=',$name)->get();
        if($data->isEmpty()){
            return "";
        }
        else{
            foreach($data as $value ){
                return $value->ID;
            }
        }
    }

    //根据教室name和locate获得教室ID
    public function ClassroomIDFromName($name,$locate): string
    {
        $data=DB::table("Classroom")->select('Classroom_id')->where('Classroom_name','=',$name)
            ->where('Classroom_locate','=',$locate)->get();

        if($data->isEmpty()){
            return "";
        }
        else{
            foreach($data as $value ){
                return $value->Classroom_id;
            }
        }
    }

    //判断教室时间安排是否冲突
    public function IsScheduleConflict_Classroom($Classroom_id,$Time0): bool
    {
        $user=DB::table("schedule")->select('Time')->where('Classroom_id','=',$Classroom_id)->get();
        $Time = array();
        foreach($user as $value){
            array_push($Time, $value->Time);
        }
        foreach($Time as $time) {
            if ($Time0[1] == $time[1] && $Time0[2] == $time[2]) {
                return true;
            }
        }
        return false;
    }

    //判断老师时间安排是否冲突
    public function IsScheduleConflict_Teacher($Teacher_id,$Time0): bool
    {
        $user=DB::table("schedule")->select('Time')->where('Teacher_id','=',$Teacher_id)->get();
        $Time = array();
        foreach($user as $value){
            array_push($Time, $value->Time);
        }
        foreach($Time as $time) {
            if ($Time0[1] == $time[1] && $Time0[2] == $time[2]) {
                return true;
            }
        }
        return false;
    }

    //删掉一个排课记录
    public function DeleteSchedule($course_id,$Teacher_id,$Classroom_id,$Time){
        DB::table("schedule")->where('course_id','=',$course_id)
            ->where('Teacher_id','=',$Teacher_id)
            ->where('Classroom_id','=',$Classroom_id)
            ->where('Time','=',$Time)->delete();

    }

    //插入一条排课记录
    public function InsertSchedule($course_id,$Teacher_id,$Classroom_id,$Time){
        DB::table("schedule")->insert([
            'course_id'=>$course_id,
            'Teacher_id'=>$Teacher_id,
            'Classroom_id'=>$Classroom_id,
            'Time'=>$Time
        ]);
    }

    //得到前端显示的排课结果表
    public function result(): array
    {
        $user0=DB::table('schedule')->join('course','schedule.course_id','=','course.ID')
            ->join('Teacher','schedule.Teacher_id','=','Teacher.ID')->join('Classroom','schedule.Classroom_id','=','Classroom.Classroom_id')
            ->select('schedule.course_id','schedule.Teacher_id',
                'schedule.Classroom_id','course.name as name1', 'Teacher.name as name2',
                'course.credit','schedule.Time','Classroom.Classroom_locate','Classroom.Classroom_name')->get();

        $result = array();
        foreach($user0 as $value){
            $arr = array();
            array_push($arr,$value->course_id);
            array_push($arr,$value->Teacher_id);
            array_push($arr,$value->Classroom_id);
            array_push($arr,$value->name1);
            array_push($arr,$value->name2);
            array_push($arr,$value->credit);
            array_push($arr,$value->Time);
            array_push($arr,$value->Classroom_locate);
            array_push($arr,$value->Classroom_name);
            array_push($result, $arr);
        }
        return $result;
    }

    //检测一条排课记录是否冲突
    public function isConflict($Teacher_id,$Classroom_id,$Time): bool
    {
        $user0=DB::table("test2")->select('*')->where('teacher_id','=',$Teacher_id)
            ->where('time','=',$Time)->get();
        $user1=DB::table("test2")->select('*')->where('classroom_id','=',$Classroom_id)
            ->where('time','=',$Time)->get();
        $arr0 = array();
        foreach($user0 as $value){
            array_push($arr0, $value);
        }
        $arr1=array();
        foreach($user1 as $value){
            array_push($arr1, $value);
        }
        if(empty($arr0) && empty($arr1)){
            return false;
        }
        else{
            return true;
        }
    }

    //根据前端筛选信息返回排课结果元组
    public function ScheduleSearch($attibute, $value): Collection
    {
        if($attibute==1){
            $user0=DB::table('schedule')->join('course','schedule.course_id','=','course.ID')
                ->join('Teacher','schedule.Teacher_id','=','Teacher.ID')->join('Classroom','schedule.Classroom_id','=','Classroom.Classroom_id')
                ->select('schedule.course_id','schedule.Teacher_id',
                    'schedule.Classroom_id','course.name as name1', 'Teacher.name as name2',
                    'course.credit','schedule.Time','Classroom.Classroom_locate','Classroom.Classroom_name')->where('schedule.course_id','=',$value)->get();
        }
        else if($attibute==2){
            $user0=DB::table('schedule')->join('course','schedule.course_id','=','course.ID')
                ->join('Teacher','schedule.Teacher_id','=','Teacher.ID')->join('Classroom','schedule.Classroom_id','=','Classroom.Classroom_id')
                ->select('schedule.course_id','schedule.Teacher_id',
                    'schedule.Classroom_id','course.name as name1', 'Teacher.name as name2',
                    'course.credit','schedule.Time','Classroom.Classroom_locate','Classroom.Classroom_name')->where('course.name','=',$value)->get();
        }
        else if($attibute==3){
            $user0=DB::table('schedule')->join('course','schedule.course_id','=','course.ID')
                ->join('Teacher','schedule.Teacher_id','=','Teacher.ID')->join('Classroom','schedule.Classroom_id','=','Classroom.Classroom_id')
                ->select('schedule.course_id','schedule.Teacher_id',
                    'schedule.Classroom_id','course.name as name1', 'Teacher.name as name2',
                    'course.credit','schedule.Time','Classroom.Classroom_locate','Classroom.Classroom_name')->where('Teacher.name','=',$value)->get();
        }
        else if($attibute==4){
            $user0=DB::table('schedule')->join('course','schedule.course_id','=','course.ID')
                ->join('Teacher','schedule.Teacher_id','=','Teacher.ID')->join('Classroom','schedule.Classroom_id','=','Classroom.Classroom_id')
                ->select('schedule.course_id','schedule.Teacher_id',
                    'schedule.Classroom_id','course.name as name1', 'Teacher.name as name2',
                    'course.credit','schedule.Time','Classroom.Classroom_locate','Classroom.Classroom_name')->where('schedule.Time','=',$value)->get();
        }
        else if($attibute==5){
            $user0=DB::table('schedule')->join('course','schedule.course_id','=','course.ID')
                ->join('Teacher','schedule.Teacher_id','=','Teacher.ID')->join('Classroom','schedule.Classroom_id','=','Classroom.Classroom_id')
                ->select('schedule.course_id','schedule.Teacher_id',
                    'schedule.Classroom_id','course.name as name1', 'Teacher.name as name2',
                    'course.credit','schedule.Time','Classroom.Classroom_locate','Classroom.Classroom_name')->where('Classroom.Classroom_locate','=',$value)->get();
        }
        else if($attibute==6){
            $user0=DB::table('schedule')->join('course','schedule.course_id','=','course.ID')
                ->join('Teacher','schedule.Teacher_id','=','Teacher.ID')->join('Classroom','schedule.Classroom_id','=','Classroom.Classroom_id')
                ->select('schedule.course_id','schedule.Teacher_id',
                    'schedule.Classroom_id','course.name as name1', 'Teacher.name as name2',
                    'course.credit','schedule.Time','Classroom.Classroom_locate','Classroom.Classroom_name')->where('Classroom.Classroom_name','=',$value)->get();
        }

        return $user0;
    }

    //------------------------------手动排课数据库相关函数---------------------------------//

    //------------------------------教室信息数据库相关函数---------------------------------//
    //返回教室信息
    public function ReturnClassroom(): Collection
    {
        //return DB::table("Classroom")->select('*')->get();
        return DB::table("classroom")->select('*')->get();
    }

    //通过Classroom_name搜索获得对应教室ID
    public function getClassroomIDname($name): Collection
    {
        $user=DB::table("Classroom")->select('Classroom_id')->where('Classroom_name','=',$name)->get();
        return $user;
    }

    //通过Classroom_locate搜索获得对应教室ID
    public function getClassroomIDlocate($locate): Collection
    {
        $user=DB::table("Classroom")->select('Classroom_id')->where('Classroom_locate','=',$locate)->get();
        return $user;
    }

    //通过Classroom_capacity搜索获得对应教室ID
    public function getClassroomIDcapacity($capacity): Collection
    {
        $user=DB::table("Classroom")->select('Classroom_id')->where('Classroom_capacity','=',$capacity)->get();
        return $user;
    }

    //添加教室元组
    public function addClassroom($Classroom_id,$Classroom_name,$Classroom_locate,$Classroom_capacity){
        DB::table("Classroom")->insert([
            'Classroom_id'=>$Classroom_id,
            'Classroom_name'=>$Classroom_name,
            'Classroom_locate'=>$Classroom_locate,
            'Classroom_capacity'=>$Classroom_capacity
        ]);
    }

    //更新教室元组
    public function UpdateClassroom($id0, $name1, $locate1, $capacity1){
        DB::table("Classroom")->where('Classroom_id',$id0)->update([
            'Classroom_name'=>$name1,
            'Classroom_locate'=>$locate1,
            'Classroom_capacity'=>$capacity1
        ]);
    }

    //根据ID获得教室元组
    public function GetClassroom($ID): Collection
    {
        return DB::table("Classroom")->select('*')->where('Classroom_id','=',$ID)->get();
    }

    //判断传入值是否是教室id
    public function IsClassroomId($ID): bool
    {
        $result=DB::table("Classroom")->select('*')->where('Classroom_id','=',$ID)->get();
        if(empty($result)){
            return false;
        }
        else{
            return true;
        }
    }
    //------------------------------教室信息数据库相关函数---------------------------------//

    //------------------------------显示课表数据库相关函数---------------------------------//
    //根据教师id查询排课结果
    public function TeacheridSearch($id): array
    {
        $user0=DB::table('schedule')->join('course','schedule.course_id','=','course.ID')
            ->join('Teacher','schedule.Teacher_id','=','Teacher.ID')->join('Classroom','schedule.Classroom_id','=','Classroom.Classroom_id')
            ->select('schedule.course_id','schedule.Teacher_id',
                'schedule.Classroom_id','course.name as name1', 'Teacher.name as name2',
                'course.credit','schedule.Time','Classroom.Classroom_locate','Classroom.Classroom_name')->where('schedule.Teacher_id','=',$id)->get();

        $result = array();
        foreach($user0 as $value){
            $arr = array();
            array_push($arr, $value->course_id);
            array_push($arr,$value->name1);
            array_push($arr,$value->name2);
            array_push($arr,$value->credit);
            array_push($arr,$value->Time);
            array_push($arr,$value->Classroom_locate);
            array_push($arr,$value->Classroom_name);
            array_push($result, $arr);
        }

        return $result;
    }

    //根据教室id查询排课结果
    public function Classroom_idSearch($id): array
    {
        $user0=DB::table('schedule')->join('course','schedule.course_id','=','course.ID')
            ->join('Teacher','schedule.Teacher_id','=','Teacher.ID')->join('Classroom','schedule.Classroom_id','=','Classroom.Classroom_id')
            ->select('schedule.course_id','schedule.Teacher_id',
                'schedule.Classroom_id','course.name as name1', 'Teacher.name as name2',
                'course.credit','schedule.Time','Classroom.Classroom_locate','Classroom.Classroom_name')->where('Classroom.Classroom_id','=',$id)->get();

        $result = array();
        foreach($user0 as $value){
            $arr = array();
            array_push($arr, $value->course_id);
            array_push($arr,$value->name1);
            array_push($arr,$value->name2);
            array_push($arr,$value->credit);
            array_push($arr,$value->Time);
            array_push($arr,$value->Classroom_locate);
            array_push($arr,$value->Classroom_name);
            array_push($result, $arr);
        }
        return $result;
    }


    //------------------------------显示课表信息数据库相关函数---------------------------------//





    //------------------------------判断冲突相关函数---------------------------------//

    //判断是否含有冲突字符,有则返回true,否则返回false
    public function IsStringConflict($value): bool
    {
        $flag=0;
        $str = $value;
        $length = strlen(utf8_decode((trim($value))));
        for($i=0;$i<$length;$i++){
            $char = mb_substr($str, $i, $i+1, 'utf-8');
            if($char=='\\'||$char=='\''||$char=='"'||$char==':'||$char=='='||$char=='/'||$char=='*'){
                $flag=1;
                break;
            }
        }
        if($flag==1){
            return true;
        }
        else{
            return false;
        }
    }

    //判断新建教室的时候，新建教室的ID是否冲突
    public function IsClassroom_idConflict($value): bool
    {
        $user=DB::table("Classroom")->select('*')->where('Classroom_id','=',$value)->get();
        if($user->isEmpty()){
            return false;
        }
        else{
            return true;
        }
    }

    public function IsScheduleEmpty(): bool
    {
        $user=DB::table("schedule")->select('*')->get();
        if($user->isEmpty()){
            return true;
        }
        else{
            return false;
        }
    }
    //------------------------------判断冲突相关函数---------------------------------//
}

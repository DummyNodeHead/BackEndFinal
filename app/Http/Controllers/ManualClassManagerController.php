<?php

namespace App\Http\Controllers;

use App\Http\Controllers\DatabaseController;
use Illuminate\Http\Request;

class ManualClassManagerController extends BaseController
{
    //手动排课页面

    /* 返回所有排课信息 */
    public function DisplayManagement(): \Illuminate\Http\JsonResponse
    {
        $obj = new DatabaseController();
        if($obj->IsScheduleEmpty()){
            $this->fail("2001","");
        }
        $data = $obj->result();
        $arr = $this->table_to_management($data);
        //return response()->json($arr);
        //return $arr;
        return $this->success($arr);
    }

    /* 手动排课，修改排课信息 */
    public function ManualManage()
    {
        $Teacher_id1 = $Teacher_id0 = request()->input('beforeteacher_id');
        $Course_id = request()->input('beforeclass_id');
        $Classroom_id1 = $Classroom_id0 = request()->input('beforeclassroom_id');
        $Teacher_name0 = request()->input('beforeteacher_name');
        $Teacher_name1 = request()->input('afterteacher_name');
        $Classroom_name0 = request()->input('beforeclassroom_name');
        $Classroom_name1 = request()->input('afterclassroom_name');
        $Classroom_locate0 = request()->input('beforecampus');
        $Classroom_locate1 = request()->input('aftercampus');
        $beforetime = request()->input('beforetime');
        $aftertime = request()->input('aftertime');
        $time0 = explode("节", $beforetime);  // 数组保存原始的字符串（周一1、2节）
        $time1 = explode("节", $aftertime);
        unset($time0[count($time0) - 1]);
        unset($time1[count($time1) - 1]);
        $obj = new DatabaseController();
//        $Teacher_id1 = $Teacher_id0 = "t100";
//        $Course_id = "r100";
//        $Classroom_id1 = $Classroom_id0 = "r101";
//        $Teacher_name0 = "yyy";
//        $Teacher_name1 = "yyy";
//        $Classroom_name0 = $Classroom_name1 = "西101";
//        $Classroom_locate0 = $Classroom_locate1 = "紫金港";
//        $beforetime = "周一1、2节,周三3、4、5节";
//        $aftertime = "周一1、2节,周六3、4、5节";
//        $time0 = explode("节", $beforetime);  // 数组保存原始的字符串（周一1、2节）
//        $time1 = explode("节", $aftertime);
//        unset($time0[count($time0) - 1]);
//        unset($time1[count($time1) - 1]);
//        $obj = new DatabaseController();

        if($obj->IsStringConflict($Teacher_id1) || $obj->IsStringConflict($Course_id)
            ||$obj->IsStringConflict($Classroom_id1) || $obj->IsStringConflict($Teacher_name1)
            ||$obj->IsStringConflict($Classroom_name1)
            ||$obj->IsStringConflict($Classroom_locate1))
        {
            return $this->fail("3002","");
        }
        $Time0 = array();
        $Time1 = array();
        foreach($time0 as $time){
            $t = $this->str_to_time($time);
            array_push($Time0, $t);
        }
        foreach($time1 as $time){
            $t = $this->str_to_time($time);
            array_push($Time1, $t);
        }

        $flag = 0;
        //判断老师名称是否修改
        if($Teacher_name0 != $Teacher_name1){
            $flag = 1;
            $Teacher_id1 = $obj->TeacherIDFromName($Teacher_name1);
            if($Teacher_id1 == ""){
                return $this->fail("3003","");
            }
        }

        //判断教室是否修改
        if($Classroom_locate0 != $Classroom_locate1 || $Classroom_name0 != $Classroom_name1){
            $flag = 1;
            $Classroom_id1 = $obj->ClassroomIDFromName($Classroom_name1, $Classroom_locate1);
            if($Classroom_id1 == ""){
                return $this->fail("3004","");
            }
        }
//        $test = array();
        //判断时间是否修改
        if($time0 != $time1){
            $flag = 1;
            //还需要判断当前时间是否合法
        }

        //如果修改
        if($flag){
            $mark = 0;
            //在数据库中删除修改排课的元组
            foreach($Time0 as $value){
                $obj->DeleteSchedule($Course_id, $Teacher_id0, $Classroom_id0, $value);
            }
            //判断修改后教室时间是否冲突
            foreach($Time1 as $value){
                if($obj->IsScheduleConflict_Classroom($Classroom_id1, $value)){
                    $mark = 1;
                    break;
                }
                if($obj->IsScheduleConflict_Teacher($Teacher_id1, $value)){
                    $mark = 2;
                    break;
                }
            }
            if($mark == 1 || $mark == 2){
                foreach($Time0 as $value){
                    $obj->InsertSchedule($Course_id, $Teacher_id0, $Classroom_id0, $value);
                }
                if($mark == 1)
                    return $this->fail("2004","");
                else
                    return $this->fail("2005","");
            }
            else{
                foreach($Time1 as $value){
                    $obj->InsertSchedule($Course_id, $Teacher_id1, $Classroom_id1, $value);
                }
                return $this->success("");//修改成功
            }
        }
    }

    /* 根据前端输入筛选信息搜索排课信息 */
    public function searchManagement(): \Illuminate\Http\JsonResponse
    {
        /* 前端获取课程名 */
        $obj = new DatabaseController();
        $attribute = request()->input('num');
        $value = request()->input('value');
//        $attribute = 1;
//        $value = "0000000001";
        if($value == ""){
           return $this->DisplayManagement();
        }
        if($obj->IsStringConflict($value)){
            return $this->fail("3002","");
        }
        $attribute = $this->ChartoNum($attribute);

        if($attribute == 4){
            $value = $this->str_to_time($value);
        }
        $data = $obj->ScheduleSearch($attribute, $value);
        if($data->isEmpty()){
            if($attribute == 1 || $attribute == 2) {
                return $this->fail("2006","");
            }
            else if($attribute == 3){
                return $this->fail("3003","");
            }
            else if($attribute == 4){
                return $this->fail("2002","");
            }
            else {
                return $this->fail("3004","");
            }
        }
        $result = array();
        foreach($data as $value){
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
        $final = $this->table_to_management($result);
        return $this->success($final);
    }

    /* 字符串转时间 （用于获取输入时把前端时间转化为数据库接受的三位字符串格式）*/
    public function str_to_time($str){
        $str = trim($str);
        $str = str_replace('，', '', $str);
        $str = str_replace(',', '', $str);
        $str = str_replace(' ', '', $str);
        $str = str_replace('、', '', $str);
        $Time = array('0', '0', '0');
        $Week = strpos($str, "周");

        $Weekday = substr($str, $Week+strlen("周"), strlen("周"));
        if($Weekday == "一") $Time[1] = '1';
        else if($Weekday == "二") $Time[1] = '2';
        else if($Weekday == "三") $Time[1] = '3';
        else if($Weekday == "四") $Time[1] = '4';
        else if($Weekday == "五") $Time[1] = '5';
        else if($Weekday == "六") $Time[1] = '6';
        else if($Weekday == "日") $Time[1] = '7';
        else return array('0', '0', '0');
        $Slot = substr($str, $Week+strlen("周一"));
        $Classes = substr($Slot, 0, 1);
        if($Classes == "1"){
            $Classes = substr($Slot, 1, 1);
            if($Classes == "2"){
                $Time[0] = '2';
                $Time[2] = '1';
            }else if($Classes == "1"){
                $Classes = substr($Slot, 4, 1);
                $Time[2] = '5';
                if($Classes == "1"){
                    $Time[0] = '3';
                }else{
                    $Time[0] = '2';
                }
            }
        }else if($Classes == "6"){
            $Time[0] = '2';
            $Time[2] = '3';
        }else if($Classes == "3"){
            $Time[2] = '2';
            $Classes = substr($Slot, 2, 1);
            if($Classes == "5"){
                $Time[0] = '3';
            }else{
                $Time[0] = '2';
            }
        }else if($Classes == "8"){
            $Time[2] = '4';
            $Classes = substr($Slot, 2, 1);
            if($Classes == "1"){
                $Time[0] = '3';
            }else{
                $Time[0] = '2';
            }
        }
        return implode("", $Time);
    }

    /* 时间转字符串 （将数据库的时间格式转化为前端打印的时间字符串）*/
    public function time_to_str($time){
        $str = "周";
        $Classes = substr($time, 0, 1);
        $Weekday = substr($time, 1, 1);
        $Slot = substr($time, 2, 1);
        if($Weekday == "1"){
            $str = $str . "一";
        }else if($Weekday == "2"){
            $str = $str . "二";
        }else if($Weekday == "3"){
            $str = $str . "三";
        }else if($Weekday == "4"){
            $str = $str . "四";
        }else if($Weekday == "5"){
            $str = $str . "五";
        }else if($Weekday == "6"){
            $str = $str . "六";
        }else if($Weekday == "7"){
            $str = $str . "日";
        }else return "";
        if($Slot == "1"){
            if($Classes == "2"){
                $str = $str . "1、2节";
            }else return "";
        }else if($Slot == "2"){
            if($Classes == "2"){
                $str = $str . "3、4节";
            }else if($Classes == "3"){
                $str = $str . "3、4、5节";
            }else return "";
        }else if($Slot == "3"){
            if($Classes == "2"){
                $str = $str . "6、7节";
            }else return "";
        }else if($Slot == "4"){
            if($Classes == "2"){
                $str = $str . "8、9节";
            }else if($Classes == "3"){
                $str = $str . "8、9、10节";
            }else return "";
        }else if($Slot == "5"){
            if($Classes == "2"){
                $str = $str . "11、12节";
            }else if($Classes == "3"){
                $str = $str . "11、12、13节";
            }
        }else return "";
        return $str;
    }

    /* 表格转排课数组 课程ID，课程名称，教师ID, 教师名称，教室ID，课程学分，上课时间，上课地点*/
    public function table_to_management($data): array
    {
        $result = array();
        for($i = 0; $i < count($data); $i=$j) {
            $j = $i + 1;
            $str = "";
            if ($j == count($data)){
                $tuple = ["class_id"=>$data[$i][0], "class_name"=>$data[$i][3],
                    "teacher_name"=>$data[$i][4], "class_score"=>$data[$i][5],
                    "campus"=>$data[$i][7], "classroom_name"=>$data[$i][8],
                    "time"=>$this->time_to_str($data[$i][6]), "teacher_id"=>$data[$i][1],
                    "classroom_id"=>$data[$i][2]];
                array_push($result, $tuple);
                break;
            }
            if ($data[$i][0] == $data[$j][0] && $data[$i][1] == $data[$j][1]) {
                $str = $this->time_to_str($data[$i][6]) . "," . $this->time_to_str($data[$j][6]);
                $j++;
            }
            else{
                $str = $this->time_to_str($data[$i][6]);
            }
            $tuple = ["class_id" => $data[$i][0], "class_name" => $data[$i][3],
                "teacher_name" => $data[$i][4], "class_score" => $data[$i][5],
                "campus" => $data[$i][7], "classroom_name" => $data[$i][8],
                "time" => $str, "teacher_id" => $data[$i][1],
                "classroom_id" => $data[$i][2]];
            array_push($result, $tuple);
        }
        return $result;
    }

    //显示课表页面
    /* 根据输入获取课表（教室和教师来自同一个输入框） */
    public function SearchSchedule(){
        /* 前端获取输入 */
        $obj = new DatabaseController();
        if($obj->IsScheduleEmpty()){
            return $this->fail("3001","");
        }
        $input = request()->input('value');
        /* 调用数据库函数判断属性 */
        if($obj->IsStringConflict($input)){
            return $this->fail("1003", "");
        }
        $data = array();
        $obj = new DatabaseController();
        $flag = $obj->CourseOrTeacher($input);
        if($flag == 0){
            return $this->fail("1004", "");
        }else if($flag == 1){
            $data = $obj->Classroom_idSearch($input);
        }else if($flag == 2){
            $data = $obj->TeacheridSearch($input);
        }
        return $this->success($this->table_to_schedule($data));
    }

    //前端显示课表
    public function table_to_schedule($data): array
    {
        $result = array();
        $w = array();
        $w[1] = "Mon";
        $w[2] = "Tue";
        $w[3] = "Wed";
        $w[4] = "Thu";
        $w[5] = "Fri";
        $w[6] = "Sat";
        $w[7] = "Sun";
        for($i = 0; $i < 5; $i++){
            $result[$i] = ["Mon"=>"", "Tue"=>"", "Wed"=>"", "Thu"=>"", "Fri"=>"", "Sat"=>"","Sun"=>""];
        }
        for($i = 0; $i < count($data); $i++){
            $time = $data[$i][4];
            $slot = substr($time, 2, 1);
            $slot = $this->ChartoNum($slot) - 1;
            $weekday = substr($time, 1, 1);
            $weekday = $this->ChartoNum($weekday);
            $result[$slot][$w[$weekday]] = $this->array_to_schedule_string($data[$i]);
        }
        return $result;
    }
    //字符转数字，辅助函数
    function ChartoNum($char): int
    {
        $num = 0;
        if($char == '1'){
            $num = 1;
        }
        else if($char == '2'){
            $num = 2;
        }
        else if($char == '3'){
            $num = 3;
        }
        else if($char == '4'){
            $num = 4;
        }
        else if($char == '5'){
            $num = 5;
        }
        else if($char == '6'){
            $num = 6;
        }
        else if($char == '7'){
            $num = 7;
        }
        return $num;

    }
    //排课结果数组转字符串，辅助函数
    function array_to_schedule_string($arr): string
    {
        $Class_id = "课程代码:" . $arr[0] . ';';
        $Class_name = "课程名称:" . $arr[1] . ' ';
        $credit = $arr[3] . "学分" . ';';
        $Teacher_name="教师:" . $arr[2] . ';';
        $time = "时间:" . $this->time_to_str($arr[4]) . ';';
        $Classroom_name = "教室:" . $arr[5] . ' ' . $arr[6];
        return $Class_id . $Class_name . $credit . $Teacher_name . $time . $Classroom_name;
    }
}


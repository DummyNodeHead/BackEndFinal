<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClassroomController extends BaseController
{
    //新建教室
    public function CreateClassroom()
    {
        $Classroom_id=request()->input('id');
        $Classroom_name=request()->input('name');
        $Classroom_locate=request()->input('campus');
        $Classroom_capacity =request()->input('capacity');
        $obj = new DatabaseController();
        $Classroom_id1 = $obj->ClassroomIDFromName($Classroom_name, $Classroom_locate);

//        $Classroom_id = "r109";
//        $Classroom_name = "西一205";
//        $Classroom_capacity = 90;
//        $Classroom_locate = "玉泉";

        //判断输入是否有空值
        if($Classroom_id == "" || $Classroom_capacity == "" || $Classroom_locate == "" || $Classroom_name == "")
        {
            return $this->fail("1001","");
            // return '1001';
        }
        else if($Classroom_capacity <= 0){
            return $this->fail("1005", "");
        }
        else if($obj->IsClassroom_idConflict($Classroom_id1) && $Classroom_id1 != $Classroom_id){
            return $this->fail("1006", "");
        }
        else
        {
            if($obj->IsStringConflict($Classroom_id)||$obj->IsStringConflict($Classroom_name)||$obj->IsStringConflict($Classroom_locate)||$obj->IsStringConflict($Classroom_capacity)){
                return $this->fail("1003","");
                // return "1003";
            }
            if($obj->IsClassroom_idConflict($Classroom_id)){
                return $this->fail("1002","");
                //return "1002";
            }
            $obj -> addClassroom($Classroom_id,$Classroom_name,$Classroom_locate,$Classroom_capacity);
        }
        return $this->success("");
    }

    //更新教室信息
    public function UpdateClassroom(): \Illuminate\Http\JsonResponse
    {
        //前端获取新，旧共8个数据
        $Classroom_id0=request()->input('Classroom_id0');
        $Classroom_name1=request()->input('Classroom_name1');
        $Classroom_locate1=request()->input('Classroom_locate1');
        $Classroom_capacity1 =request()->input('Classroom_capacity1');
        if($Classroom_capacity1 <= 0){
            return $this->fail("1005", "");
        }
//        $Classroom_id0 = "r109";
//        $Classroom_name1 = "西一302";
//        $Classroom_locate1 = "玉泉";
//        $Classroom_capacity1 = 100;
        $obj = new DatabaseController();
        if($obj->IsStringConflict($Classroom_id0)||$obj->IsStringConflict($Classroom_name1)||$obj->IsStringConflict($Classroom_locate1)||$obj->IsStringConflict($Classroom_capacity1)){
            return $this->fail("1003","");
            //return "1003";
        }
        $Classroom_id1 = $obj->ClassroomIDFromName($Classroom_name1, $Classroom_locate1);
        if($obj->IsClassroom_idConflict($Classroom_id1) && $Classroom_id1 != $Classroom_id0){
            return $this->fail("1006", "");
        }
        $obj->UpdateClassroom($Classroom_id0, $Classroom_name1, $Classroom_locate1, $Classroom_capacity1);
        return $this->success($Classroom_locate1);
    }

    //根据条件查询教室元组
    public function SearchClassroom(): \Illuminate\Http\JsonResponse
    {
        //前端获取输入（既可以是教室名，也可以是教室地点或容量）
        $key = request()->input('value');
        if($key == ""){
            $obj = new DatabaseController();
            $data = $obj->ReturnClassroom();
            $arr = array();
            foreach($data as $value){
                $s=["campus"=>$value->Classroom_locate,"id"=>$value->Classroom_id,"name"=>$value->Classroom_name,"capacity"=>$value->Classroom_capacity];
                array_push($arr, $s);
            }
            return $this->success($arr);
        }
        $Classroom_id = array();
        //根据输入获取教室id
        $obj = new DatabaseController();
        //判断是否含有非法字符
        if($obj->IsStringConflict($key)){
            return $this->fail("1003","");
            //return "1003";
        }
        $data = $obj->getClassroomIDname($key);
        foreach($data as $value){
            array_push($Classroom_id, $value->Classroom_id);
        }
        $data = $obj->getClassroomIDlocate($key);
        foreach($data as $value){
            array_push($Classroom_id, $value->Classroom_id);
        }
        $data = $obj->getClassroomIDcapacity($key);
        foreach($data as $value){
            array_push($Classroom_id, $value->Classroom_id);
        }
        if($obj->IsClassroomId($key)){
            array_push($Classroom_id, $key);
        }

        //根据教室id搜索
        $result = array();
        foreach($Classroom_id as $value)
        {
            $tem = $obj->GetClassroom($value);
            $tuple = array();
            foreach($tem as $room){
                $tuple = ["campus"=>$room->Classroom_locate, "id"=>$room->Classroom_id, "name"=>$room->Classroom_name, "capacity"=>$room->Classroom_capacity];
            }
            if(!empty($tuple)){
                array_push($result, $tuple);
            }
        }
        if(empty($result)){
            return $this->fail("1004", "");
        }
        return $this->success($result);
        //return response()->json($result);
    }

    //返回所有教室信息
    public function DisplayClassroom()
    {
        $obj = new DatabaseController();
        $data = $obj->ReturnClassroom();
        $arr = array();
        foreach($data as $value){
            $s=["campus"=>$value->Classroom_locate,"id"=>$value->Classroom_id,"name"=>$value->Classroom_name,"capacity"=>$value->Classroom_capacity];
            array_push($arr, $s);
        }
        return $this->success($arr);
//        return response()->json($arr);
    }
}

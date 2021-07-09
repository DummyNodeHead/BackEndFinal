<?php

namespace App\classManagement;
use App\Http\Controllers\DatabaseController;

class DataLoader
{
    //
    var $course;
    var $courseCapacity;
    var $room;
    var $roomCapacity;
    var $teacher;
    var $schedule = array();

    function getData(){
        $database = new DatabaseController;
        //调用database_select返回所需数组
        $this->course = $database->classID();
        $this->room = $database->classroomID();
        $this->courseCapacity = $database->classIDandVolumn();
        $this->roomCapacity = $database->classroomIDandVolumn();
        $this->teacher = $database->teacherID();


        /*
         * 测试用
         * $this->course = array("c100", "c101", "c102", "c103", "c104", "c105", "c106", "c107");
           $this->room = array("r100", "r101", "r102");
           $this->roomCapacity = array("r100"=>100, "r101"=>150, "r102"=>70);
           $this->courseCapacity = array("c100"=>125, "c101"=>140, "c102"=>100, "c103"=>60, "c104"=>40, "c105"=>120, "c106"=>90, "c107"=>50);
           $this->teacher = array("t100", "t101", "t103", "t104", "t105", "t106", "t107");
         */

    }

    function generateSchedule(){
        $database = new DatabaseController;
        $result = $database->Class_Teacher();
        $courseID = $result[0];
        $teacherID = $result[1];
        $courseTime = $result[2];
        /*
         * 测试用
         * $courseID = array("c100", "c101", "c102", "c103", "c104", "c105", "c106", "c107");
           $teacherID = array("t100", "t101", "t100", "t103", "t104", "t105", "t106", "t107");
           $courseTime = array(5, 4 ,2 ,5, 4, 2, 5, 4);
         */


        for($i = 0; $i < count($courseID); $i++){
            if ($courseTime[$i] == 2){
                //$s = new Schedule($courseID[$i], $teacherID[$i], 2);
                $s = new Schedule($courseID[$i], $teacherID[$i], 2, $this->room[mt_rand(0, count($this->room) - 1)]);
                array_push($this->schedule, $s);
            }
            elseif($courseTime[$i] == 3){
                $s = new Schedule($courseID[$i], $teacherID[$i], 3, $this->room[mt_rand(0, count($this->room) - 1)]);
                array_push($this->schedule, $s);
            }
            elseif ($courseTime[$i] == 4){
                //$s = new Schedule($courseID[$i], $teacherID[$i], 2);
                $s = new Schedule($courseID[$i], $teacherID[$i], 2, $this->room[mt_rand(0, count($this->room) - 1)]);
                array_push($this->schedule, $s);
                //$s = new Schedule($courseID[$i], $teacherID[$i], 2);
                $s = new Schedule($courseID[$i], $teacherID[$i], 2, $this->room[mt_rand(0, count($this->room) - 1)]);
                array_push($this->schedule, $s);
            }
            elseif ($courseTime[$i] == 5){
                //$s = new Schedule($courseID[$i], $teacherID[$i], 2);
                $s = new Schedule($courseID[$i], $teacherID[$i], 2, $this->room[mt_rand(0, count($this->room) - 1)]);
                array_push($this->schedule, $s);
                //$s = new Schedule($courseID[$i], $teacherID[$i], 3);
                $s = new Schedule($courseID[$i], $teacherID[$i], 3, $this->room[mt_rand(0, count($this->room) - 1)]);
                array_push($this->schedule, $s);
            }
        }
    }

    //为schedule分配合适的教室
    function allocateRoom(): string
    {

        //每间教室剩余的时间段
        //剩余两节连上的时间段
        $roomSlotNum2 = array();
        //剩余三节连上的时间段
        $roomSlotNum3 = array();

        $roomSlotNum2 = array_pad($roomSlotNum2, count($this->room), 10);
        $roomSlotNum3 = array_pad($roomSlotNum3, count($this->room), 15);
        $curRoomIndex = 0;

        //标记是否需要再次分配
        $flag = 0;

        //找到教室容量的最值
        $max_capacity = 0;
        $min_capacity = 10000;
        foreach($this->roomCapacity as $value){
            if($value > $max_capacity){
                $max_capacity = $value;
            }
            if($value < $min_capacity){
                $min_capacity = $value;
            }
        }

        //若所有教室容量一样，则跳过第一轮分配,直接进行第二轮
        if($max_capacity == $min_capacity)
            $flag = 1;


        //第一轮分配，尝试为每个pair（课程，教师）（以后简称课程）分配一个容量接近的教室
        if($flag == 0)
            for($i = 0; $i < count($this->schedule); $i++){
                $courseID = $this->schedule[$i]->getCourseID();
                //计算查找过的教室个数（以便跳出循环）
                $cnt = 0;
                //成功分配标志位
                $mark = 0;

                while(1){
                    //已经遍历过所有教室没有容量适合的跳出，在下一轮为其安排空余教室
                    if($cnt >= count($this->room)){
                        $flag = 1;
                        break;
                    }

                    //课程容量大于当前教室容量
                    if($this->courseCapacity[$courseID] > $this->roomCapacity[$this->room[$curRoomIndex]]){
                        $curRoomIndex++;
                        if($curRoomIndex >= count($this->room)){
                            $curRoomIndex = 0;
                        }
                        $cnt++;
                        continue;
                    }
                    //课程容量小于等于当前教室容量（满足容量）
                    elseif ($this->courseCapacity[$courseID] <= $this->roomCapacity[$this->room[$curRoomIndex]]){
                        $RCapacity = $this->roomCapacity[$this->room[$curRoomIndex]];
                        $ratio = ($RCapacity - $this->courseCapacity[$courseID]) * 1.0 / ($max_capacity - $min_capacity);
                        //容量较为接近可以选择
                        if($ratio < 0.2){
                            //如果剩余教室课时仍有余量，直接分配
                            //课时为2
                            if($this->schedule[$i]->getTime() == 2){
                                if($roomSlotNum2[$curRoomIndex] > 0){
                                    $this->schedule[$i]->setRoomID($curRoomIndex);
                                    $roomSlotNum2[$curRoomIndex]--;
                                    $mark = 1;
                                }
                                elseif ($roomSlotNum3[$curRoomIndex] > 0){
                                    $this->schedule[$i]->setRoomID($curRoomIndex);
                                    $roomSlotNum3[$curRoomIndex]--;
                                    $mark = 1;
                                }
                            }
                            //课时为3
                            else{
                                if($roomSlotNum3[$curRoomIndex] > 0){
                                    $this->schedule[$i]->setRoomID($curRoomIndex);
                                    $roomSlotNum3[$curRoomIndex]--;
                                    $mark = 1;
                                }
                            }

                            //若与之相邻schedule和当前是否属于同个课程,且当前课程分配成功，如果可以同时为下个课程分配教室
                            if($mark && $i+1 < count($this->schedule) && $this->schedule[$i+1]->getCourseID() == $courseID && $this->schedule[$i+1]->getTeacherID() == $this->schedule[$i]->getTeacherID()){
                                $i++;
                                $mark1 = 0;
                                //课时为2
                                if($this->schedule[$i]->getTime() == 2){
                                    if($roomSlotNum2[$curRoomIndex] > 0){
                                        $this->schedule[$i]->setRoomID($curRoomIndex);
                                        $roomSlotNum2[$curRoomIndex]--;
                                        $mark1 = 1;
                                    }
                                    elseif ($roomSlotNum3[$curRoomIndex] > 0){
                                        $this->schedule[$i]->setRoomID($curRoomIndex);
                                        $roomSlotNum3[$curRoomIndex]--;
                                        $mark1 = 1;
                                    }
                                }
                                //课时为3
                                else{
                                    if($roomSlotNum3[$curRoomIndex] > 0){
                                        $this->schedule[$i]->setRoomID($curRoomIndex);
                                        $roomSlotNum3[$curRoomIndex]--;
                                        $mark1 = 1;
                                    }
                                }
                                //分配失败（教室时间段没有余量），单独处理下一个课程
                                if($mark1 == 0){
                                    $i--;
                                }
                            }
                            //当前课程分配成功,跳出处理下一个课程
                            if($mark == 1)
                                break;
                        }
                        else{
                            $cnt++;
                            $curRoomIndex++;
                            if($curRoomIndex >= count($this->room)){
                                $curRoomIndex = 0;
                            }
                        }
                    }
                }
                $curRoomIndex++;
                if($curRoomIndex >= count($this->room)){
                    $curRoomIndex = 0;
                }
            }

        //第二轮分配，为尚未分配到合适容量的课程分配符合容量规则的教室
        $curRoomIndex = 0;
        if($flag == 1)
            for($i = 0; $i < count($this->schedule); $i++){
                if(is_int($this->schedule[$i]->getRoomID()))
                    continue;
                $courseID = $this->schedule[$i]->getCourseID();
                //计算查找过的教室个数（以便跳出循环）
                $cnt = 0;
                //成功分配标志位
                $mark = 0;

                while(1){
                    //已经遍历过所有教室没有容量适合,则说明资源不足
                    if($cnt >= count($this->room)){
                        //返回前端错误信息，报告教室资源不足
                        return "教室资源不足";
                    }

                    //课程容量大于当前教室容量
                    if($this->courseCapacity[$courseID] > $this->roomCapacity[$this->room[$curRoomIndex]]){
                        $curRoomIndex++;
                        if($curRoomIndex >= count($this->room)){
                            $curRoomIndex = 0;
                        }
                        $cnt++;
                        continue;
                    }

                    //课程容量小于等于当前教室容量（满足容量）
                    elseif ($this->courseCapacity[$courseID] <= $this->roomCapacity[$this->room[$curRoomIndex]]) {
                        //如果剩余教室课时仍有余量，直接分配
                        //课时为2
                        if ($this->schedule[$i]->getTime() == 2) {
                            if ($roomSlotNum2[$curRoomIndex] > 0) {
                                $this->schedule[$i]->setRoomID($curRoomIndex);
                                $roomSlotNum2[$curRoomIndex]--;
                                $mark = 1;
                            } elseif ($roomSlotNum3[$curRoomIndex] > 0) {
                                $this->schedule[$i]->setRoomID($curRoomIndex);
                                $roomSlotNum3[$curRoomIndex]--;
                                $mark = 1;
                            }
                        } //课时为3
                        else {
                            if ($roomSlotNum3[$curRoomIndex] > 0) {
                                $this->schedule[$i]->setRoomID($curRoomIndex);
                                $roomSlotNum3[$curRoomIndex]--;
                                $mark = 1;
                            }
                        }

                        //若与之相邻schedule和当前是否属于同个课程,且当前课程分配成功，如果可以同时为下个课程分配教室
                        if ($mark && $i + 1 < count($this->schedule) && $this->schedule[$i + 1]->getCourseID() == $courseID && $this->schedule[$i + 1]->getTeacherID() == $this->schedule[$i]->getTeacherID()) {
                            $i++;
                            $mark1 = 0;
                            //课时为2
                            if ($this->schedule[$i]->getTime() == 2) {
                                if ($roomSlotNum2[$curRoomIndex] > 0) {
                                    $this->schedule[$i]->setRoomID($curRoomIndex);
                                    $roomSlotNum2[$curRoomIndex]--;
                                    $mark1 = 1;
                                } elseif ($roomSlotNum3[$curRoomIndex] > 0) {
                                    $this->schedule[$i]->setRoomID($curRoomIndex);
                                    $roomSlotNum3[$curRoomIndex]--;
                                    $mark1 = 1;
                                }
                            } //课时为3
                            else {
                                if ($roomSlotNum3[$curRoomIndex] > 0) {
                                    $this->schedule[$i]->setRoomID($curRoomIndex);
                                    $roomSlotNum3[$curRoomIndex]--;
                                    $mark1 = 1;
                                }
                            }
                            //分配失败（教室时间段没有余量），单独处理下一个课程
                            if ($mark1 == 0) {
                                $i--;
                            }
                        }
                        //当前课程分配成功,跳出处理下一个课程
                        if($mark == 1)
                            break;
                    }
                }
                $curRoomIndex++;
                if($curRoomIndex >= count($this->room)){
                    $curRoomIndex = 0;
                }
            }
        return "nb";
    }

    public function allocateTime(): array
    {
        //建立教室、老师时间表
        $roomTimeTable = array();
        $teacherTimeTable = array();

        for($i = 0; $i < count($this->room); $i++){
            $t = array();
            $t = array_pad($t, 25, 0);
            array_push($roomTimeTable, $t);
        }
        for($i = 0; $i < count($this->teacher); $i++){
            $t = array();
            $t = array_pad($t, 25, 0);
            array_push($teacherTimeTable, $t);
        }

        //为每个schedule分配时间（教室时间和老师时间）
        $currentTime = 0;
        foreach($this->schedule as $value){

            $roomIndex = $value->getRoomID();
            $teacherID = $value->getTeacherID();
            $teacherIndex = 0;
            //为teacherID 找到对应索引
            while($teacherID != $this->teacher[$teacherIndex]){
                $teacherIndex++;
            }

            while(1){
                //当前slot是否符合time
                if($value->getTime() == 2 || $value->getTime() == 3 && (($currentTime+1) % 5) != 1 && (($currentTime+1) % 5) != 3){
                    if($teacherTimeTable[$teacherIndex][$currentTime] == 0 && $roomTimeTable[$roomIndex][$currentTime] == 0){
                        $teacherTimeTable[$teacherIndex][$currentTime] = 1;
                        $roomTimeTable[$roomIndex][$currentTime] = 1;

                        //填充schedule时间
                        $weekday = floor(($currentTime + 1) / 5) + 1;
                        $value->setWeekday($weekday);
                        $j = (($currentTime + 1) % 5);
                        if($j == 0){
                            $value->setSlot(5);
                        }
                        else{
                            $value->setSlot($j);
                        }

                        //更新下一次时间为一天后（以免相同的课邻近时间上两次）
                        $currentTime = ($weekday + 1) * 5;
                        if($currentTime > 24){
                            $currentTime -= 25;
                        }

                        //将roomID改为真实值
                        $value->setRoomID($this->room[$value->getRoomID()]);
                        break;
                    }
                    else{
                        $currentTime++;
                        if($currentTime > 24){
                            $currentTime -= 25;
                        }
                    }
                }
                else{
                    $currentTime++;
                    if($currentTime > 24){
                        $currentTime -= 25;
                    }
                }
            }
        }
        return $this->schedule;
    }

    /*测试用
    public function cost(): int
    {
        $value = $this->schedule;
        $conflict = 0;
        for($i = 0; $i < count($value); $i++){
            for($j = $i + 1; $j < count($value); $j++) {
                //同一个教室的同一时间段安排了两门课
                if ($value[$i]->getRoomID() == $value[$j]->getRoomID() && $value[$i]->getWeekday() == $value[$j]->getWeekday() && $value[$i]->getSlot() == $value[$j]->getSlot()) {
                    $conflict++;
                }
                //同一个老师的同一时间段安排了两门课
                if ($value[$i]->getTeacherID() == $value[$j]->getTeacherID() && $value[$i]->getWeekday() == $value[$j]->getWeekday() && $value[$i]->getSlot() == $value[$j]->getSlot()) {
                    $conflict++;
                }
                //同一门课一天上两次
                if ($value[$i]->getCourseID() == $value[$j]->getCourseID() && $value[$i]->getTeacherID() == $value[$j]->getTeacherID() && $value[$i]->getWeekday() == $value[$j]->getWeekday()) {
                    $conflict++;
                }
                //同一门课教室不一样
                if ($value[$i]->getCourseID() == $value[$j]->getCourseID() && $value[$i]->getTeacherID() == $value[$j]->getTeacherID() && $value[$i]->getRoomID() != $value[$j]->getRoomID()) {
                    $conflict++;
                }
            }
            //一次上三节课被安排到上午下午的第一个时间段
            if($value[$i]->getTime() == 3 && ($value[$i]->getSlot() == 1 || $value[$i]->getSlot() == 3)){
                $conflict++;
            }
            //课程容量大于教室容量
            if($this->roomCapacity[$value[$i]->getRoomID()] < $this->courseCapacity[$value[$i]->getCourseID()]){
                $conflict++;
            }
        }
        return $conflict;
    }*/
}

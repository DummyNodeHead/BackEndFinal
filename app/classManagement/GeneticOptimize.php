<?php
namespace App\classManagement;

class GeneticOptimize
{
    var $popSize;
    var $mutProb;
    var $elite;
    var $maxIter;
    var $population = array();

    /**
     * GeneticOptimize constructor.
     * @param $popSize
     * @param $mutProb
     * @param $elite
     * @param $maxIter
     */
    public function __construct($popSize, $mutProb, $elite, $maxIter)
    {
        $this->popSize = $popSize;
        $this->mutProb = $mutProb;
        $this->elite = $elite;
        $this->maxIter = $maxIter;
    }

    function schedule_cost($Dataloader): array
    {
        $conflicts = array();
        foreach($this->population as $value){
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
                if($Dataloader->roomCapacity[$value[$i]->getRoomID()] < $Dataloader->courseCapacity[$value[$i]->getCourseID()]){
                    $conflict++;
                }
            }
            array_push($conflicts, $conflict);
            echo $conflict;
            echo " ";
        }

        $result = array();
        $index = array();

        for($i = 0; $i < $this->elite; $i++){
            $min = $i;
            for($j = $i + 1; $j < count($conflicts); $j++){
                if($conflicts[$min] > $conflicts[$j]){
                    $min = $j;
                }
            }
            echo $min;
            echo ' ';
            array_push($index, $min);
            $temp = $conflicts[$min];
            $conflicts[$min] = $conflicts[$i];
            $conflicts[$i] = $temp;
        }

        array_push($result, $index);
        array_push($result, $conflicts[0]);
        return $result;
    }

    /*public function init_population($schedules){
        for($i = 0; $i < $this->popSize; $i++){
            $entity = array();
            for($j = 0; $j < count($schedules); $j++){
                $s = new Schedule($schedules[$j]->getCourseID(), $schedules[$j]->getTeacherID(),$schedules[$j]->getTime());
                $s->setRoomID($schedules[$j]->getRoomID());
                array_push($entity, $s);
            }
            array_push($this->population, $entity);
        }
    }*/
    //改版后
    public function init_population($schedules, $roomRange, $room){
        for($i = 0; $i < $this->popSize; $i++){
            $entity = array();
            for($j = 0; $j < count($schedules); $j++){
                $s = new Schedule($schedules[$j]->getCourseID(), $schedules[$j]->getTeacherID(),$schedules[$j]->getTime(), $room[mt_rand(0, $roomRange-1)]);
                array_push($entity, $s);
            }
            array_push($this->population, $entity);
        }
    }

    public function mutate($elitePopulation, $roomRange, $room){
        $e = mt_rand(0, $this->elite - 1);
        $ep = array();
        for($i = 0; $i < count($elitePopulation[$e]); $i++){
            $s = new Schedule($elitePopulation[$e][$i]->getCourseID(), $elitePopulation[$e][$i]->getTeacherID(),$elitePopulation[$e][$i]->getTime(), $elitePopulation[$e][$i]->getRoomID());
            array_push($ep, $s);
        }

        for($i = 0; $i < count($ep); $i++){
            $operation = mt_rand(1, 10);
            $pos = mt_rand(0, 2);

            if($pos == 0)
                $ep[$i]->setWeekday($this->addSub($ep[$i]->getWeekday(),$operation, 5));
            else if($pos == 1)
                $ep[$i]->setSlot($this->addSub($ep[$i]->getSlot(),$operation, 5));
            else{
                $roomIndex = mt_rand(0, $roomRange-1);
                $ep[$i]->setRoomID($room[$roomIndex]);
            }
        }

        return $ep;
    }

    public function addSub($value, $op, $valueRange): int
    {
        if($op > 5){
            if($value < $valueRange)
                $value++;
            else
                $value--;
        }
        elseif($op < 3){
            if($value - 1 > 0)
                $value--;
            else
                $value++;
        }
        return $value;
    }

    function crossover($elitePopulation): array
    {
        $e1 = mt_rand(0, $this->elite - 1);
        $e2 = mt_rand(0, $this->elite - 1);

        while($e1 == $e2){
            $e2 = mt_rand(0, $this->elite - 1);
        }

        $ep1 = array();
        $ep2 = $elitePopulation[$e2];

        for($i = 0; $i < count($elitePopulation[$e1]); $i++){
            $s = new Schedule($elitePopulation[$e1][$i]->getCourseID(), $elitePopulation[$e1][$i]->getTeacherID(),$elitePopulation[$e1][$i]->getTime(), $elitePopulation[$e1][$i]->getRoomID());
            array_push($ep1, $s);
        }


        for($i = 0; $i < count($ep1); $i++){
            $pos = mt_rand(0,1);
            if($pos == 0){
                $ep1[$i]->setWeekday($ep2[$i]->getWeekday());
                $ep1[$i]->setSlot($ep2[$i]->getSlot());
            }
            else{
                $ep1[$i]->setRoomID($ep2[$i]->getRoomID());
            }
        }
        return $ep1;
    }

    /*public function evolution($schedules){

        $this->init_population($schedules);
        for($i = 0; $i < $this->maxIter; $i++){
            $temp = $this->schedule_cost($this->population);

            $eliteIndex = $temp[0];
            $bestScore = $temp[1];
            echo $bestScore;
            echo "best";
            if($bestScore == 0 || $i == $this->maxIter - 1){
                $bestSchedule = $this->population[$eliteIndex[0]];
                break;
            }

            $newPopulation = array();
            for($j = 0; $j < count($eliteIndex); $j++){
                array_push($newPopulation, $this->population[$eliteIndex[$j]]);
            }

            //变异和交叉

            while(count($newPopulation) < $this->popSize){
                $op = mt_rand(1, 10);
                if($op <= 2)
                    $newp = $this->mutate($newPopulation);
                else
                    $newp = $this->crossover($newPopulation);

                array_push($newPopulation, $newp);
            }
            $this->population = $newPopulation;
        }
        return $bestSchedule;
    }*/
    //改版后
    public function evolution($DataLoader){

        $schedules = $DataLoader->schedule;
        $roomRange = count($DataLoader->room);

        $this->init_population($schedules, $roomRange, $DataLoader->room);
        for($i = 0; $i < $this->maxIter; $i++){
            $temp = $this->schedule_cost($DataLoader);
            $eliteIndex = $temp[0];
            $bestScore = $temp[1];
            //echo $bestScore;
            //echo "best";
            if($bestScore == 0 || $i == $this->maxIter - 1){
                $bestSchedule = $this->population[$eliteIndex[0]];
                break;
            }

            $newPopulation = array();
            for($j = 0; $j < count($eliteIndex); $j++){
                array_push($newPopulation, $this->population[$eliteIndex[$j]]);
            }

            //变异和交叉

            while(count($newPopulation) < $this->popSize){
                $op = mt_rand(0, 100);
                if($op <= $this->mutProb)
                    $newp = $this->mutate($newPopulation, count($DataLoader->room), $DataLoader->room);
                else
                    $newp = $this->crossover($newPopulation);

                array_push($newPopulation, $newp);
            }
            $this->population = $newPopulation;
        }
        return $bestSchedule;
    }
}

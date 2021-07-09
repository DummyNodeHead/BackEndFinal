<?php


namespace App\classManagement;
use phpDocumentor\Reflection\Types\Null_;

class Schedule
{
    var $courseID;
    var $teacherID;
    // 2: 可以安排在任何时间段; 3: 只能安排在2，4，5三个时间段
    var $time;
    var $roomID;
    var $weekday;
    var $slot;

    /**
     * Schedule constructor.
     * @param $courseID
     * @param $teacherID
     * @param $time
     * @param $roomID
     */
    public function __construct($courseID, $teacherID, $time, $roomID)
    {
        $this->courseID = $courseID;
        $this->teacherID = $teacherID;
        $this->time = $time;
        $this->roomID = $roomID;

        $this->random_init();
    }

    /**
     * @return mixed
     */
    public function getCourseID()
    {
        return $this->courseID;
    }

    /**
     * @param mixed $roomID
     */
    public function setRoomID($roomID): void
    {
        $this->roomID = $roomID;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return mixed
     */
    public function getTeacherID()
    {
        return $this->teacherID;
    }

    /**
     * @return mixed
     */
    public function getRoomID()
    {
        return $this->roomID;
    }

    /**
     * @return mixed
     */
    public function getSlot()
    {
        return $this->slot;
    }


    /**
     * @param mixed $weekday
     */
    public function setWeekday($weekday): void
    {
        $this->weekday = $weekday;
    }

    /**
     * @param mixed $slot
     */
    public function setSlot($slot): void
    {
        $this->slot = $slot;
    }

    function random_init(){
        $this->weekday = mt_rand(1, 5);
        $this->slot = mt_rand(1, 5);
    }
}

<?php

namespace App\Http\Controllers;
use App\classManagement\DataLoader;
use App\classManagement\GeneticOptimize;
use Illuminate\Http\JsonResponse;


class AutoManagementController extends BaseController
{
    //自动排课函数
    public function classManagement()
    {
        //若排课结果非空，则需先清空在排课
        $obj = new DatabaseController();
        if(!$obj->IsScheduleEmpty()){
            $obj->DeleteScheduleAll();
        }
        $DataLoader = new DataLoader();
        $DataLoader->getData();
        $DataLoader->generateSchedule();
        $database = new DatabaseController();
        //如果schedule > 100使用遗传算法分配教室和时间片
        if(count($DataLoader->schedule) > 200){
            $gz = new GeneticOptimize(50, 20, 10, 500);
            $database->storeClass_Teacher($gz->evolution($DataLoader));
        }
        //如果排课数较少，使用顺序排课法（也能尽量充分利用资源）
        else{
            if($DataLoader->allocateRoom() == ""){
                return $this->fail("2003","");
                //return "2003";
            }
            $DataLoader->allocateTime();
            $database->storeClass_Teacher($DataLoader->schedule);
        }
        //向前端返回排课结果
        $control = new ManualClassManagerController();
        return $control->DisplayManagement();

        //测试用
        /*$DataLoader->allocateTime();
        echo $DataLoader->cost();
        return $DataLoader->schedule;*/
    }
}

<?php

namespace App\Http\Controllers;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;
use App\Teacher;
use Illuminate\Http\Request;
use App\Student;
use App\Account;



class UploadController extends BaseController
{
    public function store(Request $request)
    {
        $j=0;
        $content=$request['file'];
        $temp = array_chunk(explode(",", $content), 11);
        for ($i=0;$i<count($temp);$i++)
        {
            $arr = [
                'id' => $temp[$i][0],
                'PSW' => $temp[$i][1],
                'type' => $temp[$i][2],
                'name' => $temp[$i][3],
                'class' => $temp[$i][4],
                'department' => $temp[$i][5],
                'major' => $temp[$i][6],
                'sex' => $temp[$i][7],
                'IDCardNum' => $temp[$i][8],
                'state' => $temp[$i][9],
                'photoURL' => $temp[$i][10],
            ];
                if ($arr['type']== 1){
                    $stu=[
                        'id' => $arr['id'],
                        'name' => $arr['name'],
                        'class' => $arr['class'],
                        'school' => $arr['department'],
                        'major' => $arr['major'],
                        'sex' => $arr['sex'],
                        'IDCardNum' => $arr['IDCardNum'],
                        'photoURL' => $arr['photoURL'],
                    ];
                    $acc=[
                        'id' => $arr['id'],
                        'PSW' => $arr['PSW'],
                        'type' => $arr['type'],
                    ];
                    
                        Account::insert($acc);
                        Student::insert($stu);
                        $j=$j+1;
                    
                }
                //teacher
                if ($arr['type']== 2){
                    $tea=[
                        'id' => $arr['id'],
                        'name' => $arr['name'],
                        'department' => $arr['department'],
                        'sex' => $arr['sex'],
                        'IDCardNum' => $arr['IDCardNum'],
                        'photoURL' => $arr['photoURL'],
                    ];
                    $acc=[
                        'id' => $arr['id'],
                        'PSW' => $arr['PSW'],
                        'type' => $arr['type'],
                    ];


                        Account::insert($acc);
                        Teacher::insert($stu);
                        $j=$j+1;

                }
                //manager
                if ($arr['type']== 3){
                    $man=[
                        'id' => $arr['id'],
                        'name' => $arr['name'],
                        'department' => $arr['department'],
                        'sex' => $arr['sex'],
                        'IDCardNum' => $arr['IDCardNum'],
                        'photoURL' => $arr['photoURL'],
                    ];
                    $acc=[
                        'id' => $arr['id'],
                        'PSW' => $arr['PSW'],
                        'type' => $arr['type'],
                    ];

                        Account::insert($acc);
                        Manager::insert($stu);
                        $j=$j+1;
                }    
        }

        return $this->create( $j,'插入成功',200);

    }
}
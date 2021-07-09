<?php

namespace App\Http\Controllers;
use App\api\Account;
use App\api\Student;
use App\api\Teacher;
use App\api\Manager;
use Illuminate\Http\Request;

class PassforgotController extends BaseController
{
    //
    public function store(Request $request)
    {
        $input = $request;
        if($input['type']==1)
        {
            $user = Student::where('IDCardNum',$input['IDcard'])->first();

            if(!$user)
            {
                return $this->create([],'不存在您的身份证号',400);
            }
            else
            {
                $user1 = Account::where('ID',$user->ID)->first();
                if(!$user1)
                return $this->create([],'不存在您的密码',400);
                else
                return $this->create($user1->PSW,'您的密码',200);
            }
        }
        if($input['type']==2)
        {
            $user = Teacher::where('IDCardNum',$input['IDcard'])->first();

            if(!$user)
            {
                return $this->create([],'不存在您的身份证号',400);
            }
            else
            {
                $user1 = Account::where('ID',$user->ID)->first();
                if(!$user1)
                return $this->create([],'不存在您的密码',400);
                else
                return $this->create([$user1->PSW],'您的密码',200);
            }
        }
        if($input['type']==3)
        {
            $user = Manager::where('IDCardNum',$input['IDcard'])->first();

            if(!$user)
            {
                return $this->create([],'不存在您的身份证号',400);
            }
            else
            {
                $user1 = Account::where('ID',$user->ID)->first();
                if(!$user1)
                return $this->create([],'不存在您的密码',400);
                else
                return $this->create([$user1->PSW],'您的密码',200);
            }
        }
    }

}

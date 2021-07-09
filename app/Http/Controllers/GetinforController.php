<?php

namespace App\Http\Controllers;

use App\api\Student;
use App\api\Teacher;
use App\api\Manager;
use Illuminate\Http\Request;

class GetinforController extends BaseController
{
    public function store(Request $request)
    {
        $input = $request;

        if($input['type']==1)
        {
            $user = Student::where('ID',$input['ID'])->first();

            return $this->create([$user->name,$user->class,$user->school,$user->major,$user->sex,$user->IDCardNum,$user->commu,$user->photoURL],'您的信息',200);

        }
        else if($input['type']==2)
        {
            $user = Teacher::where('ID',$input['ID'])->first();

            return $this->create([$user->name,$user->department,$user->sex,$user->IDCardNum,$user->photoURL,$user->commu],'您的信息',200);
        }
        else if($input['type']==3)
        {
            $user = Manager::where('ID',$input['ID'])->first();

            return $this->create([$user->name,$user->department,$user->sex,$user->IDCardNum,$user->photoURL,$user->commu],'您的信息',200);
        }
  
    }
}

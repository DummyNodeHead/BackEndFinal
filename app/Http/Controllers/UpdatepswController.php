<?php

namespace App\Http\Controllers;
use App\api\Account;

use Illuminate\Http\Request;

class UpdatepswController extends BaseController
{
    public function store(Request $request)
    {
        $input = $request;

        //$validator = Validator::make($request->all(),[]);

        $user = Account::where('ID',$input['ID'])->first();

        if($user->PSW!=$input['oldpsw'])
        {
            return $this->create([],'原密码错误',400);
        }
        else if($input['oldpsw']==$input['newpsw'])
        {
            return $this->create([],'新密码和原密码相同',400);
        }
        else if($input['newpsw']!=$input['confirmpsw'])
        {
            return $this->create([],'确认密码有误',400);
        }
        else
        {
            $acc = Account::where('ID',$input['ID'])->first();
            $acc->PSW = $input['newpsw'];
            $acc->save();
            return $this->create([],'密码修改成功',200);
        }
  
    }
}

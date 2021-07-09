<?php

namespace App\Http\Controllers;
use App\api\Account;
use Illuminate\Http\Request;

class LoginController extends BaseController
{
    /*public function index()
    {
        //
        //return $this->create([1,2,3],'数据获取成功',200);
        return $this->create(User::select('name')->paginate(5),'数据获取成功',200);
    }*/
    public function store(Request $request)
    {
        $input = $request;

        //$validator = Validator::make($request->all(),[]);

        $user = Account::where('ID',$input['ID'])->first();

        if(!$user)
        {
            return $this->create([],'id错误',400);
        }
        if($input['PSW']!=$user->PSW)
        {
            return $this->create([],'密码错误',400);
        }
        else
        {
            return $this->create([],'登录成功',200);
        }
  
    }

}

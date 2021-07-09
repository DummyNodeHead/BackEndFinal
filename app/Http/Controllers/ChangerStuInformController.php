<?php


namespace App\Http\Controllers;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Student;

class ChangerStuInformController extends BaseController
{
    public function store(Request $request)
    {
        $input=$request;
        $ID=$input['id'];
        $commu=$input['commu'];

        Student::where('id', '=', $ID)->update(['commu' => $commu]);
    }
}


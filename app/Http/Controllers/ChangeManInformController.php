<?php


namespace App\Http\Controllers;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Manager;

class ChangeManInformController extends BaseController
{
    public function store(Request $request)
    {
        $input=$request;
        $id=$input['id'];
        $name=$input['name'];
        $sex=$input['sex'];
        $idcardnum=$input['idcardnum'];
        $commu=$input['commu'];
        $v = Validator::make($input, [
            'name' => 'required|max:20',
            'department' => 'required|max:30',
            'id_card' => 'required|min:18|max:18',
            'commu' => 'required|max:30'
        ]);
        if ($v->fails())
        {
            return $this->create([$input['id']],'hfhg',200);
        }
        else 
        {
            Manager::where('id', '=', $id)->update(['name' => $name]);
            Manager::where('id', '=', $id)->update(['sex' => $sex]);
            Manager::where('id', '=', $id)->update(['idcardnum' => $idcardnum]);
            Manager::where('id', '=', $id)->update(['commu' => $commu]);
        }

    }
}


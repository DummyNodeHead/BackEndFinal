<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use App\Student;
use App\Teacher;
use App\Manager;
use App\Account;

class ChangeUserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        if (!$u = Account::find($data['id']))
        {
            return $this->create($data['id'], `δ�ҵ��û� ${data['id']} ��`, 204);
        }
        
        switch ($u['type']) // delete old user info
        {
            case '2':
                Teacher::where('id', $data['id'])->delete();
                break;
            case '3':
                Manager::where('id', $data['id'])->delete();
                break;
            case '1':
                Student::where('id', $data['id'])->delete();
                break;
        }
        
        Account::where('id', $data['id'])->update(['type' => $data['type']]); // update account info
        
        switch ($data['type']) // insert new user info
        {
            case '2':
                $v = Validator::make($data, [
                    'name' => 'required|max:20',
                    'department' => 'required|max:30',
                    'id_card' => 'required|min:18|max:18',
                    'contact' => 'required|max:30'
                ]);
                if ($v->fails())
                {
                    return $this->create([], '�޸�ʧ�ܣ����������ʽ��', 400);
                }
                else
                {
                    $addData = [
                        'ID' => $data['id'],
                        'name' => $data['name'],
                        'department' => $data['department'],
                        'sex' => $data['gender'],
                        'IDCardNum' => $data['id_card'],
                        'commu' => $data['contact'],
                        'photoURL' => ''
                    ];
                    if (Teacher::insert($addData))
                    {
                        return $this->create($data, `�û� ${data['id']} �޸ĳɹ�`, 200);
                    }
                    else
                    {
                        return $this->create([], `�޸�ʧ�ܣ��������룡`, 204);
                    }
                }
                break;
            case '1':
                $v = Validator::make($data, [
                        'name' => 'required|max:20',
                        'college' => 'required|max:30',
                        'major' => 'required|max:20',
                        'class' => 'required|max:20',
                        'id_card' => 'required|min:18|max:18',
                        'contact' => 'required|max:30'
                ]);
                if ($v->fails())
                {
                    return $this->create([], $v->errors(), 400);
                }
                else
                {
                    $addData = [
                        'ID' => $data['id'],
                        'name' => $data['name'],
                        'class' => $data['class'],
                        'major' => $data['major'],
                        'school' => $data['college'],
                        'sex' => $data['gender'],
                        'IDCardNum' => $data['id_card'],
                        'commu' => $data['contact'],
                        'photoURL' => ''
                    ];
                    if (Student::insert($addData))
                    {
                        return $this->create($data, `�û� ${data['id']} �޸ĳɹ�`, 200);
                    }
                    else
                    {
                        return $this->create([], `�޸�ʧ�ܣ��������룡`, 204);
                    }
                }
                break;
            case '3':
                $v = Validator::make($data, [
                        'name' => 'required|max:20',
                        'department' => 'required|max:30',
                        'id_card' => 'required|min:18|max:18',
                        'contact' => 'required|max:30'
                ]);
                
                if ($v->fails())
                {
                    return $this->create([], $v->errors(), 400);
                }
                else
                {
                    $addData = [
                        'ID' => $data['id'],
                        'name' => $data['name'],
                        'department' => $data['department'],
                        'sex' => $data['gender'],
                        'IDCardNum' => $data['id_card'],
                        'commu' => $data['contact'],
                        'photoURL' => ''
                    ];              
                    if (Manager::insert($addData))
                    {
                        return $this->create($data, `�û� ${data['id']} �޸ĳɹ�`, 200);
                    }
                    else
                    {
                        return $this->create([], `�޸�ʧ�ܣ��������룡`, 204);
                    }
                }
                break;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

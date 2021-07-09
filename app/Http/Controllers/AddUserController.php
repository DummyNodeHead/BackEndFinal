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

class AddUserController extends BaseController
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
    public function store(Request $request): Response
    {
        $data = $request->all();
        
        $addData1 = [
            'ID' => $data['id'],
            'PSW' => '123456',
            'type' => $data['type']
        ];
        if (!Account::insert($addData1))
        {
            return $this->create([], `����ʧ�ܣ����� id �Ƿ��ظ���`, 204);
        }
        
        switch ($data['type'])
        {
            case '2':
                $v = Validator::make($data, [
                    'id' => 'required|min:8|max:10',
                    'name' => 'required|max:20',
                    'department' => 'required|max:30',
                    'id_card' => 'required|min:18|max:18',
                    'contact' => 'required|max:30'
                ]);
                if ($v->fails())
                {
                    Account::where('id', $data['id'])->delete();
                    return $this->create([], $v->errors(), 400);
                }
                else
                {
                    $addData2 = [
                        'ID' => $data['id'],
                        'name' => $data['name'],
                        'department' => $data['department'],
                        'sex' => $data['gender'],
                        'IDCardNum' => $data['id_card'],
                        'commu' => $data['contact'],
                        'photoURL' => ''
                    ];
                    if (Teacher::insert($addData2))
                    {
                        return $this->create($data, `�û� ${data['id']} ���ӳɹ�`, 200);
                    }
                    else
                    {
                        Account::where('id', $data['id'])->delete();
                        return $this->create([], `����ʧ�ܣ����������ʽ�� id �Ƿ��ظ���`, 204);
                    }
                }
                break;
            case '1':
                $v = Validator::make($data, [
                    'id' => 'required|min:8|max:10',
                    'name' => 'required|max:20',
                    'college' => 'required|max:30',
                    'major' => 'required|max:20',
                    'class' => 'required|max:20',
                    'id_card' => 'required|min:18|max:18',
                    'contact' => 'required|max:30'
                ]);
                if ($v->fails())
                {
                    Account::where('id', $data['id'])->delete();
                    return $this->create([], $v->errors(), 400);
                }
                else
                {
                    $addData2 = [
                        'ID' => $data['id'],
                        'name' => $data['name'],
                        'class' => $data['class'],
                        'school' => $data['college'],
                        'major' => $data['major'],
                        'sex' => $data['gender'],
                        'IDCardNum' => $data['id_card'],
                        'commu' => $data['contact'],
                        'photoURL' => ''
                    ];
                    if (Student::insert($addData2))
                    {
                        return $this->create($data, `�û� ${data['id']} ���ӳɹ�`, 200);
                    }
                    else
                    {
                        Account::where('id', $data['id'])->delete();
                        return $this->create([], `����ʧ�ܣ����������ʽ�� id �Ƿ��ظ���`, 204);
                    }
                }
                break;
            case '3':
                $v = Validator::make($data, [
                    'id' => 'required|min:8|max:10',
                    'name' => 'required|max:20',
                    'department' => 'required|max:30',
                    'id_card' => 'required|min:18|max:18',
                    'contact' => 'required|max:30'
                ]);
                
                if ($v->fails())
                {
                    Account::where('id', $data['id'])->delete();
                    return $this->create([], $v->errors(), 400);
                }
                else
                {
                    $addData2 = [
                        'ID' => $data['id'],
                        'name' => $data['name'],
                        'department' => $data['department'],
                        'sex' => $data['gender'],
                        'IDCardNum' => $data['id_card'],
                        'commu' => $data['contact'],
                        'photoURL' => ''
                    ];
                    if (Manager::insert($addData2))
                    {
                        return $this->create($data, `�û� ${data['id']} ���ӳɹ�`, 200);
                    }
                    else
                    {
                        Account::where('id', $data['id'])->delete();
                        return $this->create([], `����ʧ�ܣ����������ʽ�� id �Ƿ��ظ���`, 204);
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

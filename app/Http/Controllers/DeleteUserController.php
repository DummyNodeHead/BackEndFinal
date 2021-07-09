<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;
use App\Student;
use App\Teacher;
use App\Manager;
use App\Account;

class DeleteUserController extends BaseController
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
        if ($u = Account::find($data['id']))
        {
            switch ($u['type'])
            {
                case '3':
                    Manager::where('id', $data['id'])->delete();
                    break;
                case '2':
                    Teacher::where('id', $data['id'])->delete();
                    break;
                case '1':
                    Student::where('id', $data['id'])->delete();
            }
            Account::where('id', $data['id'])->delete();
            return $this->create($data['id'], `�û� ${data['id']} �ѳɹ�ɾ��`, 200);
        }
        else
        {
            return $this->create($data['id'], `δ�ҵ��û� ${data['id']} ��`, 204);
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

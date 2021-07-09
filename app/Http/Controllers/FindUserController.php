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

class FindUserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       return Teacher::find('1691081315');
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
        switch ($data['type'])
        {
            case '2':
                if ($t = Teacher::find($data['id']))
                {
                    //return json_encode($t);
                    return $t;
                }
                else
                {
                    return null;
                }
                break;
            case '1':
                if ($t = Student::find($data['id']))
                {
                    return $t;
                }
                else
                {
                    return null;
                }
                break;
            case '3':
                if ($t = Manager::find($data['id']))
                {
                    return $t;
                }
                else
                {
                    return null;
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

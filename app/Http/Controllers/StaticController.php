<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Student;
use App\Courses;

class StaticController extends Controller
{
    public function index()
    {
       # $a=127;
       # $time=time();
       # return view('new',compact('time','a'));

       #Student::insert(
       #    ['id'=>123211,'name'=>'qian xingyi']
       #);

       #$stu = new Student;
       #$stu -> name='xingyi qian';
       #$stu -> id = 666666;
       #$stu -> save();

       #Student::where('id','>','0')->delete();

       #Student::where('id','>',0)->delete();

       #Student::destroy(118450);

       #Student::where('id','>','984032')->update(['name'=>'qianxingyi']);

       #$stu = Student::find(984032);
       #$stu->name='xingyi qian';
       #$stu->save();

       #$cor=new Courses(['course_name'=>'aaa']);

       #$stu=Student::create([
       #   'name'=>'bbbb'
       #]);

       #$stu->courses()->save($cor);
    }

    public function delete($ID)
    {
       Courses::where('course_id','=','$ID')->delete();
    }

    public function query($ID)
    {
        return Courses::where('course_id','$ID')->first();
    }

    public function insert($NAME,$DESCRIPTION,$CREDITS,$MAX)
    {
        $cor = new Courses;
        //$cor -> course_id
        $cor -> name= $NAME;
        $cor -> description= $DESCRIPTION;
        $cor -> credits = $CREDITS;
        $cor -> maxN= $MAX;
        $cor -> save();
    }

    public function update($ID,$choose,$updated)
    {
        Courses::where('course_id','=',$ID)->update([$choose=>$updated]);
    }


    public function test()
    {
        return view('test');
    }


}

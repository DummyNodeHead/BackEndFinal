<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Http\Requests\Api\CourseRequest; 
use App\Course;
use App\tc;


class CourseController extends BaseController
{
    public function index()
    {
        //return $this->create([1,2,3],'数据获取成功',200);
        //return $this->create(Courses::select('id','course_name')->get(),'数据获取成功',200);    //不分页
        return $this->create(Course::select('ID','name','credit','description','type','teacher_ID')->simplePaginate(5),'数据获取成功',200);     //分页
    }

    public function show(Request $request)
    {
        $dataa=$request->all();
        $id=$dataa['key'];
        $type=$dataa['type'];
        $data=Course::where($type,'=',$id)->get();
        
        if (empty($data))
        {
            return $this->create([],'无数据',204);
        }
        /*else if(!is_numeric($id))
        {
            return $this->create([],'参数错误',400);
        }*/
        else
        {
            return $this->create($data,'数据请求成功',200);
    
        }
    }
    public function update(Request $request)
    {
        $dataa=$request->all();
        $id = $dataa['ID'];
        if(!is_numeric($id))
        {
            return $this->create([],'参数错误',400);
        }
        $data=Course::where('ID','=',$id);
        if(empty($data))
        {
            return $this->create([],'不存在',300);
        }
        

        $validator=Validator::make($dataa,[
            'name' => 'required|min:2|max:30'
        ]);

        if($validator->fails())
        {
            return $this->create([],$validator->errors(),400);
        }
        else
        {
            $data->delete();
            $addData=Course::create($dataa);
            if($addData)
            {
                return $this->create($dataa,'数据更新成功',200);
            } 
            else
            {
                return $this->create([],'数据跟新失败',400);
            }
        }


    }
    public function destory(Request $request)
    {
        $dataa=$request->all();
        $id=$dataa['key'];
        //判断ID是否合法
        if(!is_numeric($id))
        {
            return $this->create([],'参数错误',400);
        }
        
        $data=Course::where('ID','=',$id);
        //Course::where('ID','=',$id)->delete();
        //$data=Course::find($id);
        $datatc=tc::where('course_ID','=',$id);
        if(empty($data)||empty($datatc))
        {
            return $this->create([],'不存在',300);
        }
        if($data->delete()&&$datatc->delete())
        {
            return $this->create($data,'删除成功',200);
        }
        return $this->create($data,'删除失败',202);


    }

    public function store(Request $request)
    {
        //return $request->all();
        
       $data = $request->all();
       $validator=Validator::make($data,[
           'name' => 'required|min:2|max:30'
       ]);

       if($validator->fails())
       {
           return $this->create([],$validator->errors(),400);
       }
       else
       {
            $dataa=$request->all();
            $tc=new tc;
            $tc->course_ID=$dataa['ID'];
            $tc->teacher_ID=$dataa['teacher_ID'];
            //$tc->save();
            $addData=Course::create($data);
           if($addData&&$tc->save())
           {
               return $this->create($data,'数据添加成功',200);
           } 
           else
           {
               return $this->create([],'添加失败',400);
           }
       }
    }

    /*public function delete(CourseRequest $request)
    {
       Courses::where('course_id','=',$request->ID)->delete();
    }

    public function query(Courses $course)
    {
        //return Courses::where('course_id','$ID')->first();
        return $course;
    }

    public function insert(CoureseRequest $request)
    {
        /*$cor = new Courses;
        //$cor -> course_id
        $cor -> name= $NAME;
        $cor -> description= $DESCRIPTION;
        $cor -> credits = $CREDITS;
        $cor -> maxN= $MAX;
        $cor -> save();

        User::create($request->all());
        return '添加成功';
    }

    public function update($ID,$choose,$updated)
    {
        Courses::where('course_id','=',$ID)->update([$choose=>$updated]);
    }
    //用户注册
    public function store(UserRequest $request)
    { 
        User::create($request->all()); return '用户注册成功。。。'; 
    } 
    //用户登录
     public function login(Request $request)
     { 
        $res=Auth::guard('web')->attempt(['name'=>$request->name,'password'=>$request->password]); 
        if($res){ return '用户登录成功...'; } 
        return '用户登录失败'; 
    }*/
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
#use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    #use SoftDeletes;
    #protected $dates = ['deleted_at'];
    //
    public $table = 'student';
    public $timestamps = false;
    protected $fillable = ['name'];
    public function courses(){
        return $this->hasOne('App\Courses');
    }
}

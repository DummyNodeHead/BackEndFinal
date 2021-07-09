<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Course extends Model
{
    //
    public $table='course';
    protected $fillable=['ID','name','credit','description','type','teacher_ID','stock','time'];
}

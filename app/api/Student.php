<?php

namespace App\api;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    public $table = 'student';

    public $primaryKey = 'ID';

    protected $fillable = ['ID','name','class','school','major','sex','IDCardNum','state','photoURL'];
}

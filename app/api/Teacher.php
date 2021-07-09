<?php

namespace App\api;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    public $table = 'teacher';

    public $primaryKey = 'ID';

    protected $fillable = ['ID','name','department','sex','IDCardNum','photoURL'];
}

<?php

namespace App\api;

use Illuminate\Database\Eloquent\Model;

class Manager extends Model
{
    public $table = 'manager';

    public $primaryKey = 'ID';

    protected $fillable = ['ID','name','department','sex','IDCardNum','photoURL'];
}

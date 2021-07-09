<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tc extends Model
{
    //
    public $table='tc';
    protected $fillable=['class_ID','teacher_ID'];
}

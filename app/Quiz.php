<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    public $table = 'quiz';
    public $timestamps = false;
    protected $primaryKey = 'Quiz_id';
}

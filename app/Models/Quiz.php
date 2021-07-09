<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;
    protected $table ='quiz';
    protected $fillable = [
        'Quiz_id',
        'Quiz_score',
        'Quiz_title',
        'Quiz_content',
        'Student_id',
        'Teacher_id',
        'Class_id',
        'Quiz_time'
    ];
}

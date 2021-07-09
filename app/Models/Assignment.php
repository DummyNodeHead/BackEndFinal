<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;
    protected $table ='assignment';
    protected $fillable = [
        'Assignment_id',
        'Class_id',
        'Student_id',
        'Teacher_id',
        'Assignment_title',
        'Assignment_content',
        'Start_time',
        'End_time',
        'Score_percent',
        'Submit_state',
        'Submit_content',
        'Assignment_score',
    ];
    public $timestamps = false;
}

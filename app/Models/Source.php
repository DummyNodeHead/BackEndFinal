<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    use HasFactory;
    protected $table ='personal_resources';
    protected $fillable = [
        'Path',
        'Resource_name',
        'Submit_time',
        'Student_id',
        'Teacher_id',
        'Resource_space',
    ];
}

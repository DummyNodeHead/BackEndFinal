<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TSource extends Model
{
    use HasFactory;
    protected $table ='course_resources';
    protected $fillable = [
        'Path',
        'Resource_name',
        'Class_id',
        'Submit_time',
        'Resource_space',
    ];
}

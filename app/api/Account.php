<?php

namespace App\api;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    public $table = 'account';

    public $primaryKey = 'ID';

    protected $fillable = ['ID','PSW','type'];

    public $timestamps = false;

}

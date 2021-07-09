<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}


/*<?php
 
namespace App\Models; //这里从App改成了App\Models 
use Illuminate\Notifications\Notifiable; 
use Illuminate\Foundation\Auth\User as Authenticatable; 
class User extends Authenticatable 
{ 
    use Notifiable;
    protected $table = 'users'; //去掉我创建的数据表没有的字段 
    protected $fillable = [ 'name', 'password' ]; //去掉我创建的数据表没有的字段 
    protected $hidden = [ 'password' ]; //将密码进行加密 
    public function setPasswordAttribute($value) 
    { 
        $this->attributes['password'] = bcrypt($value); 
    } 
}*/
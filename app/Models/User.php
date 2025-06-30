<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $primaryKey = 'user_id';
    
    protected $fillable = [
        'name',
        'phone',
        'email',
        'username',
        'password',
        'role',
        'extra'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'extra' => 'array',
        'password' => 'hashed',
    ];

    protected $attributes = [
        'extra' => '{}',
    ];

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower(trim($value));
    }

    public function setUsernameAttribute($value)
    {
        $this->attributes['username'] = strtolower(trim($value));
    }
}
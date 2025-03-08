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

    public function projectUsers()
    {
        return $this->hasMany(ProjectUser::class, 'user_id', 'user_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to', 'user_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'uploaded_by', 'user_id');
    }
}

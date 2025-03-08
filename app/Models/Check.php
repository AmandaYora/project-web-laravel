<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Check extends Model
{
    protected $primaryKey = 'check_id';

    protected $fillable = ['name','description','extra'];
    protected $casts = [
        'extra' => 'array'
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'user_id', 'user_id');
    }
}

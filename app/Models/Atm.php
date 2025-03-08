<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Atm extends Model
{
    protected $primaryKey = 'atm_id';

    protected $fillable = ['code','name','alamat','description','extra'];
    protected $casts = [
        'extra' => 'array'
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'user_id', 'user_id');
    }
}

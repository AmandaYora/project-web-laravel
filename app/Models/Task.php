<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $primaryKey = 'task_id';

    protected $fillable = ['user_id','check_id','atm_id','status','description','extra'];
    protected $casts = [
        'extra' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function check()
    {
        return $this->belongsTo(Check::class, 'check_id', 'check_id');
    }

    public function atm()
    {
        return $this->belongsTo(Atm::class, 'atm_id', 'atm_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'task_id', 'task_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $primaryKey = 'activity_id';

    protected $fillable = ['task_id','status','date','time','evidence','description','extra'];

    protected $casts = [
        'extra' => 'array'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id', 'task_id');
    }
}

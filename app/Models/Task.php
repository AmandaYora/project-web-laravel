<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $primaryKey = 'task_id';

    protected $fillable = [
        'project_id',
        'task_name',
        'description',
        'assigned_to',
        'status',
        'progress',
        'priority',
        'weight',
        'start_date',
        'end_date',
        'estimated_hours',
        'actual_hours',
    ];

    const STATUS_PENDING = 'Pending';
    const STATUS_IN_PROGRESS = 'In Progress';
    const STATUS_COMPLETED = 'Completed';
    const STATUS_ON_HOLD = 'On Hold';

    const PRIORITY_LOW = 1;
    const PRIORITY_MEDIUM = 2;
    const PRIORITY_HIGH = 3;

    protected static function boot()
    {
        parent::boot();
        
        static::saved(function ($task) {
            // Update project progress when task is saved
            $task->updateProjectProgress();
        });
    }

    public function updateProjectProgress()
    {
        if ($this->project) {
            $tasks = $this->project->tasks;
            $totalWeight = $tasks->sum('weight');
            $weightedProgress = 0;

            foreach ($tasks as $task) {
                // Calculate weighted progress for each task
                $weightedProgress += ($task->progress * $task->weight);
            }

            // Calculate overall project progress
            $projectProgress = $totalWeight > 0 ? ($weightedProgress / $totalWeight) : 0;
            
            // Update project progress and status
            $this->project->progress = $projectProgress;
            
            // Update project status based on tasks
            if ($tasks->count() > 0) {
                if ($projectProgress == 100) {
                    $this->project->status = 'Completed';
                } elseif ($tasks->where('status', self::STATUS_IN_PROGRESS)->count() > 0) {
                    $this->project->status = 'In Progress';
                } elseif ($tasks->where('status', self::STATUS_ON_HOLD)->count() == $tasks->count()) {
                    $this->project->status = 'On Hold';
                }
            }
            
            $this->project->save();
        }
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to', 'user_id');
    }
}

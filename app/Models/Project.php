<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $primaryKey = 'project_id';

    protected $fillable = [
        'project_name',
        'description',
        'start_date',
        'end_date',
        'progress',
        'status',
    ];

    const STATUS_PENDING = 'Pending';
    const STATUS_IN_PROGRESS = 'In Progress';
    const STATUS_COMPLETED = 'Completed';
    const STATUS_ON_HOLD = 'On Hold';

    public function calculateProgress()
    {
        $tasks = $this->tasks;
        
        if ($tasks->count() === 0) {
            return 0;
        }

        $totalWeight = $tasks->sum('weight');
        $weightedProgress = 0;

        foreach ($tasks as $task) {
            $weightedProgress += ($task->progress * $task->weight);
        }

        return $totalWeight > 0 ? round($weightedProgress / $totalWeight, 2) : 0;
    }

    public function updateStatus()
    {
        $tasks = $this->tasks;
        
        if ($tasks->count() === 0) {
            $this->status = self::STATUS_PENDING;
        } elseif ($this->progress == 100) {
            $this->status = self::STATUS_COMPLETED;
        } elseif ($tasks->where('status', Task::STATUS_IN_PROGRESS)->count() > 0) {
            $this->status = self::STATUS_IN_PROGRESS;
        } elseif ($tasks->where('status', Task::STATUS_ON_HOLD)->count() == $tasks->count()) {
            $this->status = self::STATUS_ON_HOLD;
        } else {
            $this->status = self::STATUS_PENDING;
        }

        return $this->status;
    }

    public function getEstimatedHours()
    {
        return $this->tasks->sum('estimated_hours');
    }

    public function getActualHours()
    {
        return $this->tasks->sum('actual_hours');
    }

    public function getCompletionRate()
    {
        $estimatedHours = $this->getEstimatedHours();
        $actualHours = $this->getActualHours();
        
        if ($estimatedHours > 0 && $actualHours > 0) {
            return round(($estimatedHours / $actualHours) * 100, 2);
        }
        
        return 0;
    }

    public function users()
    {
        return $this->hasMany(ProjectUser::class, 'project_id', 'project_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id', 'project_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'project_id', 'project_id');
    }

    public function cpmActivities()
    {
        return $this->hasMany(CpmActivity::class, 'project_id', 'project_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'project_id', 'project_id');
    }
}

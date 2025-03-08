<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Document;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get project statistics
        $projectStats = [
            'total' => Project::count(),
            'in_progress' => Project::where('status', Project::STATUS_IN_PROGRESS)->count(),
            'completed' => Project::where('status', Project::STATUS_COMPLETED)->count(),
            'on_hold' => Project::where('status', Project::STATUS_ON_HOLD)->count(),
        ];

        // Get task statistics
        $taskStats = [
            'total' => Task::count(),
            'pending' => Task::where('status', Task::STATUS_PENDING)->count(),
            'in_progress' => Task::where('status', Task::STATUS_IN_PROGRESS)->count(),
            'completed' => Task::where('status', Task::STATUS_COMPLETED)->count(),
        ];

        // Get recent projects with their progress
        $recentProjects = Project::with(['tasks'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($project) {
                return [
                    'id' => $project->project_id,
                    'name' => $project->project_name,
                    'progress' => $project->progress,
                    'status' => $project->status,
                    'tasks_count' => $project->tasks->count(),
                    'completion_rate' => $project->getCompletionRate(),
                ];
            });

        // Get urgent tasks (high priority and in progress)
        $urgentTasks = Task::with(['project', 'user'])
            ->where('priority', Task::PRIORITY_HIGH)
            ->where('status', Task::STATUS_IN_PROGRESS)
            ->orderBy('end_date')
            ->limit(5)
            ->get();

        // Get recent documents
        $recentDocuments = Document::with('project')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Calculate overall project health
        $projectHealth = [
            'on_track' => Project::whereIn('status', [Project::STATUS_IN_PROGRESS, Project::STATUS_COMPLETED])
                ->where('progress', '>=', DB::raw('DATEDIFF(CURDATE(), start_date) * 100 / DATEDIFF(end_date, start_date)'))
                ->count(),
            'at_risk' => Project::where('status', Project::STATUS_IN_PROGRESS)
                ->where('progress', '<', DB::raw('DATEDIFF(CURDATE(), start_date) * 100 / DATEDIFF(end_date, start_date)'))
                ->count(),
            'delayed' => Project::where('end_date', '<', DB::raw('CURDATE()'))
                ->where('status', '!=', Project::STATUS_COMPLETED)
                ->count(),
        ];

        return view('content.dashboard.index', compact(
            'projectStats',
            'taskStats',
            'recentProjects',
            'urgentTasks',
            'recentDocuments',
            'projectHealth'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $projectId = $request->projectId;
        $users = User::all();
        $projects = Project::all();
        
        if ($projectId) {
            $project = Project::findOrFail($projectId);
            $tasks = Task::where('project_id', $projectId)->with(['project', 'user'])->get();
            return view('content.tasks.index', compact('tasks', 'users', 'projects', 'project'));
        }

        $tasks = Task::with(['project', 'user'])->get();
        return view('content.tasks.index', compact('tasks', 'users', 'projects'));
    }

    public function saveTask(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,project_id',
            'task_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,user_id',
            'status' => 'required|in:Pending,In Progress,Completed,On Hold',
            'progress' => 'required|numeric|min:0|max:100',
            'priority' => 'required|in:1,2,3',
            'weight' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'estimated_hours' => 'nullable|numeric|min:0',
            'actual_hours' => 'nullable|numeric|min:0',
        ]);

        $taskId = $request->input('task_id');
        
        if ($taskId) {
            $task = Task::findOrFail($taskId);
            $message = 'Task updated successfully!';
        } else {
            $task = new Task();
            $message = 'Task created successfully!';
        }

        $task->fill($request->all());
        $task->save();

        return redirect()->back()->with('success', $message);
    }

    public function deleteTask(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $projectId = $task->project_id;
        $task->delete();

        return redirect()->back()->with('success', 'Task deleted successfully!');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,In Progress,Completed,On Hold',
            'progress' => 'required|numeric|min:0|max:100'
        ]);

        $task = Task::findOrFail($id);
        $task->status = $request->status;
        $task->progress = $request->progress;
        if ($request->status === Task::STATUS_COMPLETED) {
            $task->progress = 100;
        }
        if ($request->status === Task::STATUS_PENDING) {
            $task->progress = 0;
        }
        $task->save();

        $projectProgress = $task->project->progress;

        return redirect()->back()->with('success', 'Task status updated successfully');
    }

    public function updateProgress(Request $request, $id)
    {
        $request->validate([
            'progress' => 'required|numeric|min:0|max:100'
        ]);

        $task = Task::findOrFail($id);
        $task->progress = $request->progress;
        if ($task->progress == 100) {
            $task->status = Task::STATUS_COMPLETED;
        } elseif ($task->progress > 0) {
            $task->status = Task::STATUS_IN_PROGRESS;
        } elseif ($task->progress == 0) {
            $task->status = Task::STATUS_PENDING;
        }
        $task->save();

        return redirect()->back()->with('success', 'Task progress updated successfully');
    }
}

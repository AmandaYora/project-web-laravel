<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with(['tasks', 'users'])->get();
        return view('content.projects.index', compact('projects'));
    }

    public function show($id)
    {
        $project = Project::with(['tasks', 'users', 'documents', 'cpmActivities', 'reports'])->findOrFail($id);
        return view('content.projects.show', compact('project'));
    }

    public function saveProject(Request $request)
    {
        $request->validate([
            'project_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|string|in:Pending,In Progress,Completed,On Hold'
        ]);

        $projectId = $request->input('project_id');
        
        if ($projectId) {
            $project = Project::findOrFail($projectId);
            $message = 'Project updated successfully!';
        } else {
            $project = new Project();
            $message = 'Project created successfully!';
        }

        $project->fill($request->all());
        $project->save();

        return redirect()->route('projects.index')->with('success', $message);
    }

    public function deleteProject($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully!');
    }
}

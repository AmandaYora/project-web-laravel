<?php

namespace App\Http\Controllers;

use App\Models\CpmActivity;
use App\Models\Project;
use Illuminate\Http\Request;

class CpmController extends Controller
{
    public function index($projectId)
    {
        $project = Project::findOrFail($projectId);
        $activities = CpmActivity::where('project_id', $projectId)->get();
        return view('content.cpm.index', compact('project', 'activities'));
    }

    public function saveActivity(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,project_id',
            'activity_name' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'predecessors' => 'nullable|string',
        ]);

        if ($request->activity_id) {
            $activity = CpmActivity::findOrFail($request->activity_id);
            $activity->update($request->all());
        } else {
            CpmActivity::create($request->all());
        }

        $this->calculateCPM($request->project_id);

        return redirect()->route('cpm.index', $request->project_id)
            ->with('success', 'Activity saved successfully');
    }

    public function deleteActivity($id)
    {
        $activity = CpmActivity::findOrFail($id);
        $projectId = $activity->project_id;
        $activity->delete();

        $this->calculateCPM($projectId);

        return redirect()->route('cpm.index', $projectId)
            ->with('success', 'Activity deleted successfully');
    }

    private function calculateCPM($projectId)
    {
        $activities = CpmActivity::where('project_id', $projectId)->get();
        
        // Reset all values
        foreach ($activities as $activity) {
            $activity->early_start = 0;
            $activity->early_finish = 0;
            $activity->late_start = 0;
            $activity->late_finish = 0;
            $activity->save();
        }

        // Forward Pass
        $processed = [];
        $queue = $activities->filter(function($activity) {
            return empty($activity->predecessors);
        });

        while ($queue->count() > 0) {
            $current = $queue->shift();
            
            // Calculate early start and early finish
            if (!empty($current->predecessors)) {
                $predecessors = explode(',', $current->predecessors);
                $maxEarlyFinish = 0;
                foreach ($predecessors as $predecessorId) {
                    $predecessor = $activities->firstWhere('activity_id', trim($predecessorId));
                    if ($predecessor) {
                        $maxEarlyFinish = max($maxEarlyFinish, $predecessor->early_finish);
                    }
                }
                $current->early_start = $maxEarlyFinish;
            }
            
            $current->early_finish = $current->early_start + $current->duration;
            $current->save();
            
            $processed[] = $current->activity_id;
            
            // Add successors to queue
            $successors = $activities->filter(function($activity) use ($current, $processed) {
                if (empty($activity->predecessors)) return false;
                $predecessors = explode(',', $activity->predecessors);
                return in_array($current->activity_id, $predecessors) &&
                       !in_array($activity->activity_id, $processed);
            });
            
            $queue = $queue->concat($successors);
        }

        // Backward Pass
        $maxEarlyFinish = $activities->max('early_finish');
        $processed = [];
        $queue = $activities->filter(function($activity) use ($activities) {
            return !$activities->contains(function($a) use ($activity) {
                return !empty($a->predecessors) && 
                       in_array($activity->activity_id, explode(',', $a->predecessors));
            });
        });

        foreach ($activities as $activity) {
            $activity->late_finish = $maxEarlyFinish;
            $activity->late_start = $maxEarlyFinish;
            $activity->save();
        }

        while ($queue->count() > 0) {
            $current = $queue->shift();
            
            if (empty($processed)) {
                $current->late_finish = $maxEarlyFinish;
                $current->late_start = $current->late_finish - $current->duration;
            }
            
            $current->save();
            $processed[] = $current->activity_id;
            
            if (!empty($current->predecessors)) {
                $predecessors = explode(',', $current->predecessors);
                foreach ($predecessors as $predecessorId) {
                    $predecessor = $activities->firstWhere('activity_id', trim($predecessorId));
                    if ($predecessor && !in_array($predecessor->activity_id, $processed)) {
                        $predecessor->late_finish = min(
                            $predecessor->late_finish,
                            $current->late_start
                        );
                        $predecessor->late_start = $predecessor->late_finish - $predecessor->duration;
                        $predecessor->save();
                        $queue->push($predecessor);
                    }
                }
            }
        }
    }

    public function getCriticalPath($projectId)
    {
        $activities = CpmActivity::where('project_id', $projectId)->get();
        $criticalPath = $activities->filter(function($activity) {
            return $activity->early_start === $activity->late_start &&
                   $activity->early_finish === $activity->late_finish;
        })->values();

        return response()->json([
            'activities' => $activities,
            'criticalPath' => $criticalPath
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Task;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ActivityController extends Controller
{
    public function index()
    {
        $currentId = session('user.user_id');
        $currentRole = session('user.role');
        if ($currentRole === 'user') {
            $tasks = Task::where('user_id', $currentId)
                ->whereDoesntHave('activities', function($query) {
                    $query->whereDate('date', Carbon::today());
                })->get();
            $activities = Activity::whereIn('task_id', Task::where('user_id', $currentId)->pluck('task_id'))->get();
        } else {
            $tasks = Task::all();
            $activities = Activity::all();
        }
        return view('content.activities.index', compact('activities', 'tasks'));
    }

    public function saveActivity(Request $request)
    {
        $rules = [
            'task_id' => 'required|exists:tasks,task_id',
            'status' => 'required',
            'date' => 'required|date',
            'time' => 'required',
            'evidence' => ($request->activity_id ? 'nullable' : 'required') . '|image'
        ];
        $request->validate($rules);
        $data = $request->all();
        if ($request->hasFile('evidence')) {
            $uploadPath = public_path('uploads');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $file = $request->file('evidence');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);
            $data['evidence'] = 'uploads/' . $filename;
        } else {
            if ($request->activity_id) {
                unset($data['evidence']);
            }
        }
        $extraData = [];
        foreach ($data as $key => $value) {
            if (!in_array($key, ['task_id', 'status', 'date', 'time', 'evidence', 'description'])) {
                $extraData[$key] = $value;
                unset($data[$key]);
            }
        }
        if ($request->activity_id) {
            $item = Activity::find($request->activity_id);
            $item->update($data);
            $item->extra = array_merge($item->extra ?? [], $extraData);
            $item->save();
            $message = 'Activity updated successfully';
        } else {
            $data['extra'] = $extraData;
            Activity::create($data);
            $message = 'Activity created successfully';
        }
        return redirect()->route('activities.index')->with('success', $message);
    }

    public function deleteActivity($id)
    {
        $item = Activity::find($id);
        if ($item) {
            $item->delete();
            return redirect()->route('activities.index')->with('success', 'Activity deleted successfully');
        }
        return redirect()->route('activities.index')->with('error', 'Activity not found');
    }

}

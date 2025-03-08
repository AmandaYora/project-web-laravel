<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use App\Models\Atm;
use App\Models\Check;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        $users = User::all();
        $atms = Atm::all();
        $checks = Check::all();
        return view('content.tasks.index', compact('tasks', 'users', 'atms', 'checks'));
    }

    public function saveTask(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'check_id' => 'required',
            'atm_id' => 'required',
            'status' => 'required',
        ]);

        $data = $request->all();
        $extraData = [];

        foreach ($data as $key => $value) {
            if (!in_array($key, ['user_id','check_id','atm_id','status','description'])) {
                $extraData[$key] = $value;
                unset($data[$key]);
            }
        }

        if ($request->task_id) {
            $item = Task::find($request->task_id);
            $item->update($data);
            $item->extra = array_merge($item->extra ?? [], $extraData);
            $item->save();
            $message = 'Task updated successfully';
        } else {
            $data['extra'] = $extraData;
            Task::create($data);
            $message = 'Task created successfully';
        }

        return redirect()->route('tasks.index')->with('success', $message);
    }

    public function deleteTask($id)
    {
        $item = Task::find($id);
        if ($item) {
            $item->delete();
            return redirect()->route('tasks.index')->with('success', 'Task deleted successfully');
        }
        return redirect()->route('tasks.index')->with('error', 'Task not found');
    }
}

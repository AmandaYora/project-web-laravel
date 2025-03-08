<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Check;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CheckController extends Controller
{
    public function index()
    {
        $checks = Check::all();
        return view('content.checks.index', compact('checks'));
    }

    public function saveCheck(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $data = $request->all();
        $extraData = [];

        foreach ($data as $key => $value) {
            if (!in_array($key, ['name','description'])) {
                $extraData[$key] = $value;
                unset($data[$key]);
            }
        }

        if ($request->user_id) {
            $item = Check::find($request->check_id);
            $item->update($data);
            $item->extra = array_merge($item->extra ?? [], $extraData);
            $item->save();
            $message = 'Check updated successfully';
        } else {
            $data['extra'] = $extraData;
            Check::create($data);
            $message = 'Check created successfully';
        }

        return redirect()->route('checks.index')->with('success', $message);
    }

    public function deleteCheck($id)
    {
        $item = Check::find($id);
        if ($item) {
            $item->delete();
            return redirect()->route('checks.index')->with('success', 'Check deleted successfully');
        }
        return redirect()->route('checks.index')->with('error', 'Check not found');
    }
}

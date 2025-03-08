<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Atm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AtmController extends Controller
{
    public function index()
    {
        $atms = Atm::all();
        return view('content.atms.index', compact('atms'));
    }

    public function saveAtm(Request $request)
    {
        $request->validate([
            
        ]);

        $data = $request->all();
        $extraData = [];

        foreach ($data as $key => $value) {
            if (!in_array($key, ['code','name','alamat','description'])) {
                $extraData[$key] = $value;
                unset($data[$key]);
            }
        }

        if ($request->user_id) {
            $item = Atm::find($request->atm_id);
            $item->update($data);
            $item->extra = array_merge($item->extra ?? [], $extraData);
            $item->save();
            $message = 'Atm updated successfully';
        } else {
            $data['extra'] = $extraData;
            Atm::create($data);
            $message = 'Atm created successfully';
        }

        return redirect()->route('atms.index')->with('success', $message);
    }

    public function deleteAtm($id)
    {
        $item = Atm::find($id);
        if ($item) {
            $item->delete();
            return redirect()->route('atms.index')->with('success', 'Atm deleted successfully');
        }
        return redirect()->route('atms.index')->with('error', 'Atm not found');
    }
}

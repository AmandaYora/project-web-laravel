<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JurusanController extends Controller
{
    public function index()
    {
        $jurusans = Jurusan::all();
        return view('content.jurusan.index', compact('jurusans'));
    }

    public function saveJurusan(Request $request)
    {
        $request->validate([
            'jurusan' => 'required',
            'description' => 'nullable'
        ]);

        try {
            DB::beginTransaction();

            if ($request->filled('jurusan_id')) {
                $jurusan = Jurusan::find($request->jurusan_id);
                if (!$jurusan) {
                    return redirect()->back()->with('error', 'Jurusan not found');
                }
                
                $jurusan->update($request->all());
                DB::commit();
                return redirect()->back()->with('success', 'Jurusan updated successfully');
            }

            Jurusan::create($request->all());
            
            DB::commit();
            return redirect()->back()->with('success', 'Jurusan created successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error processing request: ' . $e->getMessage());
        }
    }

    public function deleteJurusan($id)
    {
        try {
            DB::beginTransaction();
            
            $jurusan = Jurusan::find($id);
            if (!$jurusan) {
                return redirect()->back()->with('error', 'Jurusan not found');
            }

            if ($jurusan->siswas()->exists()) {
                return redirect()->back()->with('error', 'Cannot delete jurusan. It has students assigned to it');
            }

            $jurusan->delete();
            
            DB::commit();
            return redirect()->back()->with('success', 'Jurusan deleted successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error deleting jurusan: ' . $e->getMessage());
        }
    }
}

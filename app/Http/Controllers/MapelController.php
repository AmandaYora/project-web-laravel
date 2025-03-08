<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use App\Models\Subject;
use App\Models\Jurusan;
use App\Models\ClassSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapelController extends Controller
{
    public function index()
    {
        $mapels = Mapel::with(['subject', 'jurusan', 'class'])->get();
        $subjects = Subject::all();
        $jurusans = Jurusan::all();
        $classes = ClassSiswa::all();

        return view('content.mapel.index', compact('mapels', 'subjects', 'jurusans', 'classes'));
    }

    public function saveMapel(Request $request)
    {
        $request->validate([
            'day' => 'required',
            'subject_id' => 'required|exists:subjects,subject_id',
            'jurusan_id' => 'required|exists:jurusan,jurusan_id',
            'class_id' => 'required|exists:classes,class_id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'date' => 'required|date'
        ]);

        try {
            DB::beginTransaction();

            if ($request->filled('mapel_id')) {
                $mapel = Mapel::find($request->mapel_id);
                if (!$mapel) {
                    return redirect()->back()->with('error', 'Schedule not found');
                }
                
                $mapel->update($request->all());
                DB::commit();
                return redirect()->back()->with('success', 'Schedule updated successfully');
            }

            Mapel::create($request->all());
            
            DB::commit();
            return redirect()->back()->with('success', 'Schedule created successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error processing request: ' . $e->getMessage());
        }
    }

    public function deleteMapel($id)
    {
        try {
            DB::beginTransaction();
            
            $mapel = Mapel::find($id);
            if (!$mapel) {
                return redirect()->back()->with('error', 'Schedule not found');
            }

            $mapel->delete();
            
            DB::commit();
            return redirect()->back()->with('success', 'Schedule deleted successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Jadwal sedang digunakan oleh Sesi kelas');
            //return redirect()->back()->with('error', 'Error deleting schedule: ' . $e->getMessage());
        }
    }
}

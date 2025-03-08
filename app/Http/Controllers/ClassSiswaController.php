<?php

namespace App\Http\Controllers;

use App\Models\ClassSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassSiswaController extends Controller
{
    public function index()
    {
        $classes = ClassSiswa::all();
        return view('content.classes.index', compact('classes'));
    }

    public function saveClass(Request $request)
    {
        $request->validate([
            'class' => 'required',
            'description' => 'nullable'
        ]);

        try {
            DB::beginTransaction();

            if ($request->filled('class_id')) {
                $class = ClassSiswa::find($request->class_id);
                if (!$class) {
                    return redirect()->back()->with('error', 'Class not found');
                }
                
                $class->update($request->all());
                DB::commit();
                return redirect()->back()->with('success', 'Class updated successfully');
            }

            ClassSiswa::create($request->all());
            
            DB::commit();
            return redirect()->back()->with('success', 'Class created successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error processing request: ' . $e->getMessage());
        }
    }

    public function deleteClass($id)
    {
        try {
            DB::beginTransaction();
            
            $class = ClassSiswa::find($id);
            if (!$class) {
                return redirect()->back()->with('error', 'Class not found');
            }

            if ($class->siswas()->exists()) {
                return redirect()->back()->with('error', 'Cannot delete class. It has students assigned to it');
            }

            $class->delete();
            
            DB::commit();
            return redirect()->back()->with('success', 'Class deleted successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error deleting class: ' . $e->getMessage());
        }
    }
}

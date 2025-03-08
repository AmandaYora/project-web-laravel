<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::all();
        return view('content.subjects.index', compact('subjects'));
    }

    public function saveSubject(Request $request)
    {
        $request->validate([
            'subject' => 'required',
            'description' => 'nullable'
        ]);

        try {
            DB::beginTransaction();

            if ($request->filled('subject_id')) {
                $subject = Subject::find($request->subject_id);
                if (!$subject) {
                    return redirect()->back()->with('error', 'Subject not found');
                }
                
                $subject->update($request->all());
                DB::commit();
                return redirect()->back()->with('success', 'Subject updated successfully');
            }

            Subject::create($request->all());
            
            DB::commit();
            return redirect()->back()->with('success', 'Subject created successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error processing request: ' . $e->getMessage());
        }
    }

    public function deleteSubject($id)
    {
        try {
            DB::beginTransaction();
            
            $subject = Subject::find($id);
            if (!$subject) {
                return redirect()->back()->with('error', 'Subject not found');
            }

            if ($subject->gurus()->exists()) {
                return redirect()->back()->with('error', 'Cannot delete subject. It is being used by teachers');
            }

            $subject->delete();
            
            DB::commit();
            return redirect()->back()->with('success', 'Subject deleted successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error deleting subject: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ClassSession;
use App\Models\Guru;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClassSessionController extends Controller
{
    public function index()
    {
        $user = session('user');
        $query = ClassSession::with(['mapel.subject', 'mapel.class', 'mapel.jurusan', 'guru.user']);
        if ($user['role'] === 'guru') {
            $query->where('guru_id', $user['guru']['guru_id']);
        }
        $sessions = $query->get();
        $gurusQuery = Guru::with(['user', 'subject']);
        if ($user['role'] === 'guru') {
            $gurusQuery->where('guru_id', $user['guru']['guru_id']);
        }
        $gurus = $gurusQuery->get();
        $mapels = Mapel::with(['subject', 'class', 'jurusan'])->get();
        return view('content.sessions.index', compact('sessions', 'gurus', 'mapels'));
    }

    public function saveSession(Request $request)
    {
        if (session('user')['role'] === 'guru') {
            $request->merge([
                'guru_id' => session('user')['guru']['guru_id']
            ]);
        }

        $request->validate([
            'mapel_id' => 'required|exists:mapel,mapel_id',
            'guru_id' => 'required|exists:guru,guru_id',
            'status' => 'required|in:pending,ongoing,completed'
        ]);

        try {
            DB::beginTransaction();

            if ($request->filled('class_session_id')) {
                $session = ClassSession::find($request->class_session_id);
                if (!$session) {
                    return redirect()->back()->with('error', 'Session not found');
                }
                
                $session->update($request->all());
                DB::commit();
                return redirect()->back()->with('success', 'Session updated successfully');
            }

            // Generate unique barcode
            $barcode = Str::random(10);
            while (ClassSession::where('barcode', $barcode)->exists()) {
                $barcode = Str::random(10);
            }

            ClassSession::create(array_merge(
                $request->all(),
                ['barcode' => $barcode]
            ));
            
            DB::commit();
            return redirect()->back()->with('success', 'Session created successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error processing request: ' . $e->getMessage());
        }
    }

    public function deleteSession($id)
    {
        try {
            DB::beginTransaction();
            
            $session = ClassSession::find($id);
            if (!$session) {
                return redirect()->back()->with('error', 'Session not found');
            }

            $session->delete();
            
            DB::commit();
            return redirect()->back()->with('success', 'Session deleted successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error deleting session: ' . $e->getMessage());
        }
    }
}

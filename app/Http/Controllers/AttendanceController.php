<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = session('user');

        if ($user['role'] === 'guru') {
            $attendance = Attendance::with(['classSession.mapel.subject', 'classSession.mapel.class', 'classSession.mapel.jurusan', 'user'])
                ->whereHas('classSession', function ($q) use ($user) {
                    $q->where('guru_id', $user['guru']['guru_id']);
                })
                ->get();

            $sessions = ClassSession::with(['mapel.subject', 'mapel.class', 'mapel.jurusan', 'guru.user'])
                ->where('status', 'ongoing')
                ->where('guru_id', $user['guru']['guru_id'])
                ->get();
        } elseif ($user['role'] === 'siswa') {
            $attendance = Attendance::with(['classSession.mapel.subject', 'classSession.mapel.class', 'classSession.mapel.jurusan', 'user'])
                ->where('user_id', $user['user_id'])
                ->get();

            $sessions = ClassSession::with(['mapel.subject', 'mapel.class', 'mapel.jurusan', 'guru.user'])
                ->where('status', 'ongoing')
                ->whereHas('mapel.class', function ($q) use ($user) {
                    $q->where('class_id', $user['siswa']['class_id']);
                })
                ->get();
        } else {
            $attendance = Attendance::with(['classSession.mapel.subject', 'classSession.mapel.class', 'classSession.mapel.jurusan', 'user'])
                ->get();

            $sessions = ClassSession::with(['mapel.subject', 'mapel.class', 'mapel.jurusan', 'guru.user'])
                ->where('status', 'ongoing')
                ->get();
        }

        $users = User::where('role', 'siswa')->get();
        return view('content.attendance.index', compact('attendance', 'sessions', 'users'));
    }


    public function scan()
    {
        return view('content.attendance.scan');
    }

    public function processAttendance(Request $request)
    {
        $request->validate([
            'barcode' => 'required'
        ]);

        try {
            DB::beginTransaction();
            $session = ClassSession::where('barcode', $request->barcode)
                ->where('status', 'ongoing')
                ->first();

            if (!$session) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid or expired session code'
                ]);
            }

            $exists = Attendance::where('class_session_id', $session->class_session_id)
                ->where('user_id', session('user.user_id'))
                ->exists();

            if ($exists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You have already recorded attendance for this session'
                ]);
            }

            $scheduleTime = strtotime($session->mapel->start_time);
            $currentTime = time();
            $status = $currentTime <= $scheduleTime + (15 * 60) ? 'present' : 'late';

            Attendance::create([
                'class_session_id' => $session->class_session_id,
                'user_id' => session('user.user_id'),
                'clock_in' => Carbon::now()->format('H:i'),
                'date' => date('Y-m-d'),
                'status' => $status
            ]);
            
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Attendance recorded successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Error processing attendance: ' . $e->getMessage()
            ]);
        }
    }

    public function saveAttendance(Request $request)
    {
        $request->validate([
            'class_session_id' => 'required|exists:class_sessions,class_session_id',
            'user_id' => 'required|exists:users,user_id',
            'clock_in' => 'required',
            'date' => 'required|date',
            'status' => 'required|in:present,late,absent'
        ]);

        try {
            DB::beginTransaction();

            if ($request->filled('attendance_id')) {
                $attendance = Attendance::find($request->attendance_id);
                if (!$attendance) {
                    return redirect()->back()->with('error', 'Attendance not found');
                }
                $attendance->update($request->all());
                DB::commit();
                return redirect()->back()->with('success', 'Attendance updated successfully');
            }

            $exists = Attendance::where('class_session_id', $request->class_session_id)
                ->where('user_id', $request->user_id)
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Attendance already exists for this session');
            }

            Attendance::create($request->all());
            DB::commit();
            return redirect()->back()->with('success', 'Attendance created successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error processing request: ' . $e->getMessage());
        }
    }

    public function deleteAttendance($id)
    {
        try {
            DB::beginTransaction();
            $attendance = Attendance::find($id);
            if (!$attendance) {
                return redirect()->back()->with('error', 'Attendance not found');
            }
            $attendance->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Attendance deleted successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Sesi kelas sudah digunakan oleh siswa');
            //return redirect()->back()->with('error', 'Error deleting attendance: ' . $e->getMessage());
        }
    }

    public function printByMonth($monthYear)
    {
        $user = session('user');
        list($month, $year) = explode('-', $monthYear);
        $bulanIndo = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];
        $namaBulan = $bulanIndo[$month];
        $attendanceQuery = Attendance::with([
                'classSession.mapel.subject',
                'classSession.mapel.class',
                'classSession.mapel.jurusan',
                'user'
            ])
            ->whereMonth('date', $month)
            ->whereYear('date', $year);
        $sessionsQuery = ClassSession::with([
                'mapel.subject',
                'mapel.class',
                'mapel.jurusan',
                'guru.user'
            ])
            ->where('status', 'ongoing');

        if ($user['role'] === 'guru') {
            $attendanceQuery->whereHas('classSession', function ($q) use ($user) {
                $q->where('guru_id', $user['guru']['guru_id']);
            });
            $sessionsQuery->where('guru_id', $user['guru']['guru_id']);
        }

        $attendance = $attendanceQuery->get();
        $sessions = $sessionsQuery->get();
        $users = User::where('role', 'siswa')->get();

        return view('content.attendance.print', compact('attendance', 'sessions', 'users', 'namaBulan', 'year'));
    }
}

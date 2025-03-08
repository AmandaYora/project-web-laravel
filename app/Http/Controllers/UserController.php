<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Subject;
use App\Models\ClassSiswa;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    protected $userFields = ['name', 'phone', 'email', 'username', 'password', 'role'];

    public function index()
    {
        $users = User::with(['guru.subject', 'siswa.kelas', 'siswa.jurusan'])->get();
        $subjects = Subject::all();
        $classes = ClassSiswa::all();
        $jurusans = Jurusan::all();
        return view('content.users.index', compact('users', 'subjects', 'classes', 'jurusans'));
    }

    public function saveUser(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'username' => 'required',
            'role' => 'required'
        ]);

        try {
            DB::beginTransaction();
            $data = $request->only($this->userFields);
            $data['extra'] = $request->except(array_merge(
                $this->userFields, 
                ['nip','subject_id','education','hire_date','gender_guru','nis','tahun_masuk','class_id','jurusan_id','gender_siswa'],
                ['_token','user_id']
            ));

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            if ($request->filled('user_id')) {
                $user = User::find($request->user_id);
                if (!$user) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'User not found');
                }
                if (!$request->filled('password')) {
                    unset($data['password']);
                }
                $oldRole = $user->role;
                $user->update($data);

                if ($oldRole !== $request->role) {
                    if ($oldRole === 'guru') {
                        $user->guru()->delete();
                    } elseif ($oldRole === 'siswa') {
                        $user->siswa()->delete();
                    }
                }

                if ($request->role === 'guru') {
                    $request->validate([
                        'nip' => 'required',
                        'subject_id' => 'required|exists:subjects,subject_id',
                        'education' => 'required',
                        'hire_date' => 'required|date',
                        'gender_guru' => 'required|in:L,P'
                    ]);
                    $guruData = $request->only(['nip','subject_id','education','hire_date']);
                    $guruData['gender'] = $request->gender_guru;
                    if ($user->guru) {
                        $user->guru()->update($guruData);
                    } else {
                        $guruData['user_id'] = $user->user_id;
                        Guru::create($guruData);
                    }
                } elseif ($request->role === 'siswa') {
                    $request->validate([
                        'nis' => 'required',
                        'tahun_masuk' => 'required',
                        'class_id' => 'required|exists:classes,class_id',
                        'jurusan_id' => 'required|exists:jurusan,jurusan_id',
                        'gender_siswa' => 'required|in:L,P'
                    ]);
                    $siswaData = $request->only(['nis','tahun_masuk','class_id','jurusan_id']);
                    $siswaData['gender'] = $request->gender_siswa;
                    if ($user->siswa) {
                        $user->siswa()->update($siswaData);
                    } else {
                        $siswaData['user_id'] = $user->user_id;
                        Siswa::create($siswaData);
                    }
                }

                DB::commit();
                return redirect()->back()->with('success', 'User updated successfully');
            }

            $user = User::create($data);

            if ($request->role === 'guru') {
                $request->validate([
                    'nip' => 'required',
                    'subject_id' => 'required|exists:subjects,subject_id',
                    'education' => 'required',
                    'hire_date' => 'required|date',
                    'gender_guru' => 'required|in:L,P'
                ]);
                $guruData = $request->only(['nip','subject_id','education','hire_date']);
                $guruData['gender'] = $request->gender_guru;
                $guruData['user_id'] = $user->user_id;
                Guru::create($guruData);
            } elseif ($request->role === 'siswa') {
                $request->validate([
                    'nis' => 'required',
                    'tahun_masuk' => 'required',
                    'class_id' => 'required|exists:classes,class_id',
                    'jurusan_id' => 'required|exists:jurusan,jurusan_id',
                    'gender_siswa' => 'required|in:L,P'
                ]);
                $siswaData = $request->only(['nis','tahun_masuk','class_id','jurusan_id']);
                $siswaData['gender'] = $request->gender_siswa;
                $siswaData['user_id'] = $user->user_id;
                Siswa::create($siswaData);
            }

            DB::commit();
            return redirect()->back()->with('success', 'User created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error processing request: '.$e->getMessage());
        }
    }

    public function deleteUser($id)
    {
        try {
            DB::beginTransaction();
            $user = User::find($id);
            if (!$user) {
                DB::rollBack();
                return redirect()->back()->with('error', 'User not found');
            }
            if ($user->role === 'guru') {
                $user->guru()->delete();
            } elseif ($user->role === 'siswa') {
                $user->siswa()->delete();
            }
            $user->delete();
            DB::commit();
            return redirect()->back()->with('success', 'User deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error deleting user: '.$e->getMessage());
        }
    }
}

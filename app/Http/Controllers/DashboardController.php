<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Subject;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'siswa_count' => Siswa::count(),
            'guru_count' => Guru::count(),
            'subject_count' => Subject::count()
        ];

        return view('dashboard', compact('stats'));
    }
}

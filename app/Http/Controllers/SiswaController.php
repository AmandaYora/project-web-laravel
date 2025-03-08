<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\User;

class SiswaController extends Controller
{
    public function index()
    {
        $siswas = Siswa::with(['user', 'kelas', 'jurusan'])->get();
        return view('content.siswa.index', compact('siswas'));
    }
}

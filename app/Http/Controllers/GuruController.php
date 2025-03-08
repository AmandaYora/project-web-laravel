<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\User;

class GuruController extends Controller
{
    public function index()
    {
        $gurus = Guru::with(['user', 'subject'])->get();
        return view('content.guru.index', compact('gurus'));
    }
}

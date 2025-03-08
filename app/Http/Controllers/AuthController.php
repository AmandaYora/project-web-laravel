<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return redirect()->back()->with('error', 'Invalid credentials');
        }

        $userData = $user->toArray();
        
        if ($user->role === 'guru') {
            $userData['guru'] = $user->guru;
        } elseif ($user->role === 'siswa') {
            $userData['siswa'] = $user->siswa;
        }

        session(['user' => $userData]);
        
        return redirect()->route('dashboard')->with('success', 'Login successful');
    }

    public function logout()
    {
        session()->forget('user');
        return redirect()->route('auth.login')->with('success', 'Logged out successfully');
    }
}

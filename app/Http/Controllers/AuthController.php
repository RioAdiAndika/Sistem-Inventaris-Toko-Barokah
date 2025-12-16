<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 🟢 Tampilkan halaman login
    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();


            if ($user->hasRole('Admin')) {
                return redirect()->route('dashboard.admin');
            } elseif ($user->hasRole('Gudang')) {
                return redirect()->route('dashboard.gudang');
            }
        }

        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->hasRole('Admin')) {
                return redirect()->route('dashboard.admin');
            } elseif ($user->hasRole('Gudang')) {
                return redirect()->route('dashboard.gudang');
            }

            Auth::logout();
            return redirect()->route('login')->with('error', 'Role tidak dikenal!');
        }

        return back()->with('error', 'Email atau password salah!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Berhasil logout!');
    }
}

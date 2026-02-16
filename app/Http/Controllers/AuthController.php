<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse; // Tambahkan ini
use Illuminate\View\View; // Tambahkan ini

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function login(): View
    {
        return view('auth.login');
    }

    // Proses login
    public function loginProcess(Request $req): RedirectResponse
    {
        $credentials = $req->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $req->session()->regenerate();

            // Gunakan intended() agar user kembali ke halaman yang ingin diakses sebelumnya
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau password tidak sesuai dengan data kami.',
        ])->onlyInput('email'); // Mengembalikan input email agar user tidak perlu ketik ulang
    }

    // Proses logout
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
 
        $request->session()->invalidate();
        $request->session()->regenerateToken();
 
        return redirect('/login');
    }
}
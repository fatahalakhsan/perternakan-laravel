<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        // Batasi percobaan login: 5 kali per menit untuk kombinasi email + alamat IP
        $key = Str::lower($data['email']).'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $detik = RateLimiter::availableIn($key);

            return back()->withErrors(['email' => "Terlalu banyak percobaan. Coba lagi dalam {$detik} detik."])->onlyInput('email');
        }

        if (Auth::attempt($data, $request->boolean('remember'))) {
            RateLimiter::clear($key);
            $request->session()->regenerate(); // cegah session fixation

            return redirect()->intended('/dashboard');
        }

        RateLimiter::hit($key, 60);

        return back()->withErrors(['email' => 'Email atau kata sandi salah.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nama'     => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'nama.required'     => 'Nama pengguna wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        // 1. Cari user aktif berdasarkan nama
        $pengguna = Pengguna::where('nama', $request->nama)
            ->where('aktif', true)
            ->first();

        // 2. Cocokkan password memakai method dari model Pengguna
        if ($pengguna && $pengguna->cocokKataSandi($request->password)) {
            Auth::login($pengguna, $request->boolean('remember'));
            $request->session()->regenerate();

            if ($pengguna->peran === 'admin') {
                return redirect()->route('admin.dasbor');
            }

            return redirect()->route('petugas.scan');
        }

        return back()->withErrors([
            'nama' => 'Nama pengguna salah, kata sandi tidak cocok, atau akun nonaktif.',
        ])->onlyInput('nama');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
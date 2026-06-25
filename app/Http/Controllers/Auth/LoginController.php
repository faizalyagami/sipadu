<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string', // Bisa email atau NPM
            'password' => 'required|string',
        ]);

        $login = $request->login;
        $password = $request->password;

        // Cek apakah login menggunakan NPM atau Email
        $user = null;

        // Coba cari user berdasarkan email
        $user = User::where('email', $login)->first();

        // Jika tidak ditemukan, coba cari berdasarkan NPM
        if (!$user) {
            $mahasiswa = Mahasiswa::where('npm', $login)->first();
            if ($mahasiswa) {
                $user = $mahasiswa->user;
            }
        }

        // Jika user ditemukan dan password cocok
        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $request->remember);
            $request->session()->regenerate();

            // Redirect berdasarkan role
            if ($user->role == 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role == 'petugas') {
                return redirect()->route('petugas.dashboard');
            } elseif ($user->role == 'mahasiswa') {
                return redirect()->route('mahasiswa.dashboard');
            }

            return redirect('/');
        }

        return back()->withErrors([
            'login' => 'NPM/Email atau password salah.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {
        $prodis = Prodi::with('fakultas')->get();
        return view('auth.register', compact('prodis'));
    }

    /**
     * Handle registration request for mahasiswa
     */
    public function register(Request $request)
    {
        $request->validate([
            'npm' => 'required|string|unique:mahasiswas',
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'prodi_id' => 'required|exists:prodis,id',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:15',
            'tanggal_masuk' => 'required|date',
        ]);

        // Create user account
        $user = User::create([
            'name' => $request->nama_lengkap,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'mahasiswa',
            'is_active' => true,
        ]);

        // Create mahasiswa data
        Mahasiswa::create([
            'user_id' => $user->id,
            'prodi_id' => $request->prodi_id,
            'npm' => $request->npm,
            'nama_lengkap' => $request->nama_lengkap,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'no_hp' => $request->no_hp,
            'tanggal_masuk' => $request->tanggal_masuk,
            'status_mahasiswa' => 'Aktif',
        ]);

        // Send welcome email
        Mail::send('emails.welcome', [
            'name' => $user->name,
            'email' => $user->email,
        ], function($message) use ($user) {
            $message->to($user->email)
                    ->subject('Selamat Datang di Sistem Surat Unisba');
        });

        // Auto login after registration
        Auth::login($user);

        return redirect()->route('mahasiswa.dashboard')
            ->with('success', 'Registrasi berhasil! Selamat datang di Sistem Surat Unisba.');
    }
}
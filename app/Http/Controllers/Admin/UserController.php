<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'petugas')->get();
        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'nip' => 'required|unique:users',
            'position' => 'required|in:admin,petugas'
        ]);

        $password = Str::random(8);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'nip' => $request->nip,
            'position' => $request->position,
            'role' => 'petugas'
        ]);

        // Kirim email
        Mail::send('emails.user-credentials', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $password
        ], function($message) use ($user) {
            $message->to($user->email)
                    ->subject('Akun Sistem Surat Unisba');
        });

        return redirect()->route('admin.users.index')->with('success', 'Petugas berhasil ditambahkan');
    }

    public function edit(User $user)
    {
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'nip' => 'required|unique:users,nip,' . $user->id,
            'position' => 'required|in:admin,petugas'
        ]);

        $user->update($request->only('name', 'email', 'nip', 'position'));

        return redirect()->route('admin.users.index')->with('success', 'Petugas berhasil diupdate');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Petugas berhasil dihapus');
    }
}
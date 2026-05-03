<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $user = auth()->user();
        
        if ($user->role == 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role == 'petugas') {
            return redirect()->route('petugas.dashboard');
        } elseif ($user->role == 'mahasiswa') {
            return redirect()->route('mahasiswa.dashboard');
        }
        
        return redirect('/login');
    }
}
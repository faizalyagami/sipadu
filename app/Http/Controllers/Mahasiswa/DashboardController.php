<?php
// app/Http/Controllers/Mahasiswa/DashboardController.php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Cek apakah user memiliki relasi mahasiswa
        if (!$user->mahasiswa) {
            return view('mahasiswa.dashboard', [
                'totalSurat' => 0,
                'pendingSurat' => 0,
                'approvedSurat' => 0,
                'rejectedSurat' => 0,
                'latestSurats' => collect([]),
                'error' => 'Data mahasiswa tidak ditemukan. Silakan hubungi administrator.'
            ]);
        }
        
        $mahasiswa = $user->mahasiswa;
        
        $data = [
            'totalSurat' => Surat::where('mahasiswa_id', $mahasiswa->id)->count(),
            'pendingSurat' => Surat::where('mahasiswa_id', $mahasiswa->id)->where('status', 'pending')->count(),
            'approvedSurat' => Surat::where('mahasiswa_id', $mahasiswa->id)->where('status', 'approved')->count(),
            'rejectedSurat' => Surat::where('mahasiswa_id', $mahasiswa->id)->where('status', 'rejected')->count(),
            'latestSurats' => Surat::where('mahasiswa_id', $mahasiswa->id)->with('jenisSurat')->latest()->limit(5)->get()
        ];
        
        return view('mahasiswa.dashboard', $data);
    }
}
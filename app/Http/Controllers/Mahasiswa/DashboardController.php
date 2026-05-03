<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $mahasiswa = auth()->user()->mahasiswa;
        
        $data = [
            'totalSurat' => Surat::where('mahasiswa_id', $mahasiswa->id)->count(),
            'pendingSurat' => Surat::where('mahasiswa_id', $mahasiswa->id)->where('status', 'pending')->count(),
            'approvedSurat' => Surat::where('mahasiswa_id', $mahasiswa->id)->where('status', 'approved')->count(),
            'rejectedSurat' => Surat::where('mahasiswa_id', $mahasiswa->id)->where('status', 'rejected')->count(),
            'recentSurats' => Surat::where('mahasiswa_id', $mahasiswa->id)->with('jenisSurat')->latest()->limit(5)->get()
        ];
        
        return view('mahasiswa.dashboard', $data);
    }
}
<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'totalPending' => Surat::where('status', 'pending')->count(),
            'totalApproved' => Surat::where('status', 'approved')->count(),
            'totalRejected' => Surat::where('status', 'rejected')->count(),
            'recentSurats' => Surat::with(['mahasiswa', 'jenisSurat'])->latest()->limit(10)->get()
        ];
        
        return view('petugas.dashboard', $data);
    }
}
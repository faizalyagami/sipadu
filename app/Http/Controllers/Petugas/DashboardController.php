<?php
// app/Http/Controllers/Petugas/DashboardController.php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\JenisSurat;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik surat
        $totalPending = Surat::where('status', 'pending')->count();
        $totalApproved = Surat::where('status', 'approved')->count();
        $totalRejected = Surat::where('status', 'rejected')->count();
        $totalSurat = Surat::count();
        
        // Surat per jenis
        $suratPerJenis = JenisSurat::withCount('surats')->get();
        
        // Data chart (6 bulan terakhir)
        $months = [];
        $suratCounts = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $months[] = $month->translatedFormat('M Y');
            $count = Surat::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $suratCounts[] = $count;
        }
        
        // Surat terbaru pending
        $pendingSurats = Surat::where('status', 'pending')
            ->with(['mahasiswa', 'jenisSurat'])
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get();
        
        // Surat terbaru disetujui
        $approvedSurats = Surat::where('status', 'approved')
            ->with(['mahasiswa', 'jenisSurat'])
            ->orderBy('approved_at', 'desc')
            ->limit(5)
            ->get();
        
        $data = [
            'totalPending' => $totalPending,
            'totalApproved' => $totalApproved,
            'totalRejected' => $totalRejected,
            'totalSurat' => $totalSurat,
            'suratPerJenis' => $suratPerJenis,
            'months' => $months,
            'suratCounts' => $suratCounts,
            'pendingSurats' => $pendingSurats,
            'approvedSurats' => $approvedSurats
        ];
        
        return view('petugas.dashboard', $data);
    }
}
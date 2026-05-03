<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Fakultas;
use App\Models\Mahasiswa;
use App\Models\Surat;
use App\Models\JenisSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function getDashboardData(Request $request)
    {
        $year = $request->get('year', date('Y'));
        
        // Statistik dasar
        $totalMahasiswa = Mahasiswa::count();
        $totalPetugas = User::where('role', 'petugas')->count();
        $totalFakultas = Fakultas::count();
        $totalPending = Surat::where('status', 'pending')->count();
        $aktifCount = Mahasiswa::where('status_mahasiswa', 'Aktif')->count();

        // Data chart surat per bulan
        $months = [];
        $suratCounts = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $monthName = date('M', mktime(0, 0, 0, $i, 1));
            $months[] = $monthName;
            
            $count = Surat::whereYear('created_at', $year)
                ->whereMonth('created_at', $i)
                ->count();
            $suratCounts[] = $count;
        }

        // Data kategori surat
        $kategoriData = JenisSurat::select('kategori', DB::raw('count(*) as total'))
            ->groupBy('kategori')
            ->get();
        
        $kategoriLabels = $kategoriData->pluck('kategori')->toArray();
        $kategoriCounts = $kategoriData->pluck('total')->toArray();

        // Jika tidak ada data kategori, beri data default
        if (empty($kategoriLabels)) {
            $kategoriLabels = ['Surat Izin', 'Surat Keterangan', 'Surat Pengajuan'];
            $kategoriCounts = [0, 0, 0];
        }

        // Data surat terbaru
        $latestSurats = Surat::with(['mahasiswa', 'jenisSurat'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function($surat) {
                return [
                    'id' => $surat->id,
                    'status' => $surat->status,
                    'created_at' => $surat->created_at,
                    'mahasiswa' => $surat->mahasiswa ? [
                        'nama_lengkap' => $surat->mahasiswa->nama_lengkap,
                        'npm' => $surat->mahasiswa->npm
                    ] : null,
                    'jenis_surat' => $surat->jenisSurat ? [
                        'nama_surat' => $surat->jenisSurat->nama_surat
                    ] : null
                ];
            });

        return response()->json([
            'totalMahasiswa' => $totalMahasiswa,
            'totalPetugas' => $totalPetugas,
            'totalFakultas' => $totalFakultas,
            'totalPending' => $totalPending,
            'aktifCount' => $aktifCount,
            'months' => $months,
            'suratCounts' => $suratCounts,
            'kategoriLabels' => $kategoriLabels,
            'kategoriData' => $kategoriCounts,
            'latestSurats' => $latestSurats
        ]);
    }
}
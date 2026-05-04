<?php
// app/Http/Controllers/Petugas/ApprovalController.php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\JenisSurat;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ApprovalController extends Controller
{
    public function index()
    {
        $surats = Surat::where('status', 'pending')
            ->with(['mahasiswa', 'jenisSurat'])
            ->orderBy('created_at', 'asc')
            ->paginate(10);
        
        return view('petugas.approval.index', compact('surats'));
    }

    public function approve($id)
    {
        $surat = Surat::findOrFail($id);
        $surat->status = 'approved';
        $surat->approved_by = auth()->id();
        $surat->approved_at = now();
        
        // Generate TTD Elektronik
        $surat->ttd_nama = 'Dr. Oki Mardiawan, M.Psi., Psikolog.';
        $surat->ttd_nip = 'D.07.0.464';
        $surat->ttd_jabatan = 'Wakil Dekan Bidang Pembelajaran dan Kemahasiswaan';
        $surat->nomor_surat = $this->generateNomorSurat();
        
        // Generate base64 signature image
        $surat->ttd_elektronik = $this->generateSignatureBase64($surat);
        
        $surat->save();

        return redirect()->route('petugas.approval.index')
            ->with('success', 'Surat berhasil disetujui dan telah ditandatangani secara elektronik');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string'
        ]);

        $surat = Surat::findOrFail($id);
        $surat->status = 'rejected';
        $surat->approved_by = auth()->id();
        $surat->approved_at = now();
        $surat->alasan_reject = $request->alasan;
        $surat->save();

        return redirect()->route('petugas.approval.index')
            ->with('success', 'Surat ditolak');
    }

    public function history()
    {
        $surats = Surat::where('status', '!=', 'pending')
            ->with(['mahasiswa', 'jenisSurat', 'approvedBy'])
            ->orderBy('approved_at', 'desc')
            ->paginate(10);
        
        return view('petugas.approval.history', compact('surats'));
    }

    private function generateNomorSurat()
    {
        $tahun = date('Y');
        $bulan = date('m');
        $bulanRomawi = $this->getRomanMonth($bulan);
        $lastSurat = Surat::whereYear('created_at', $tahun)->count() + 1;
        $nomor = str_pad($lastSurat, 3, '0', STR_PAD_LEFT);
        return "{$nomor}/UNISBA/FK/{$bulanRomawi}/{$tahun}";
    }

    private function getRomanMonth($month)
    {
        $romawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $romawi[(int)$month];
    }

    private function generateSignatureBase64($surat)
    {
        // Create signature image
        $width = 300;
        $height = 80;
        $image = imagecreate($width, $height);
        
        // Colors
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        $purple = imagecolorallocate($image, 111, 66, 193);
        
        // Background
        imagefill($image, 0, 0, $white);
        
        // Draw signature line
        imageline($image, 10, 50, $width - 10, 50, $black);
        
        // Add text
        $text = "TTD Elektronik";
        $name = $surat->ttd_nama;
        
        imagettftext($image, 10, 0, 50, 30, $purple, '', $text);
        imagettftext($image, 9, 0, 50, 55, $black, '', $name);
        imagettftext($image, 8, 0, 50, 70, $black, '', "NIP. " . $surat->ttd_nip);
        
        // Convert to base64
        ob_start();
        imagepng($image);
        $imageData = ob_get_contents();
        ob_end_clean();
        imagedestroy($image);
        
        return 'data:image/png;base64,' . base64_encode($imageData);
    }
}
<?php

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
            ->with(['mahasiswa.prodi.fakultas', 'jenisSurat'])
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        return view('petugas.approval.index', compact('surats'));
    }

    /**
     * Approve surat dengan TTD + Cap dari gambar
     */
    public function approve($id)
    {
        $surat = Surat::findOrFail($id);

        // Gunakan method dari model untuk approve dengan TTD
        $surat->approveWithTTD(auth()->id());

        return redirect()->route('petugas.approval.index')
            ->with('success', 'Surat berhasil disetujui dan telah ditandatangani secara elektronik');
    }

    /**
     * Tolak surat dengan alasan
     */
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

    /**
     * Riwayat approve surat
     */
    public function history()
    {
        $surats = Surat::where('status', '!=', 'pending')
            ->with(['mahasiswa', 'jenisSurat', 'approvedBy'])
            ->orderBy('approved_at', 'desc')
            ->paginate(10);

        return view('petugas.approval.history', compact('surats'));
    }

    /**
     * Generate nomor surat otomatis
     */
    private function generateNomorSurat()
    {
        $tahun = date('Y');
        $bulan = date('m');
        $bulanRomawi = $this->getRomanMonth($bulan);
        $lastSurat = Surat::whereYear('created_at', $tahun)->count() + 1;
        $nomor = str_pad($lastSurat, 3, '0', STR_PAD_LEFT);
        return "{$nomor}/UNISBA/FK/{$bulanRomawi}/{$tahun}";
    }

    /**
     * Konversi bulan ke angka romawi
     */
    private function getRomanMonth($month)
    {
        $romawi = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];
        return $romawi[(int)$month];
    }

    public function getSuratData($id)
    {
        try {
            $surat = Surat::with(['mahasiswa.prodi.fakultas', 'jenisSurat'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $surat->id,
                    'mahasiswa_nama' => $surat->mahasiswa->nama_lengkap,
                    'mahasiswa_npm' => $surat->mahasiswa->npm,
                    'fakultas' => $surat->mahasiswa->prodi->fakultas->nama_fakultas ?? '-',
                    'jenis_surat' => $surat->jenisSurat->nama_surat,
                    'keperluan' => $surat->keperluan,
                    'content' => $surat->content,
                    'tanggal_pengajuan' => $surat->created_at->format('d/m/Y H:i'),
                    'nama_ortu' => $surat->nama_ortu,
                    'nik_ortu' => $surat->nik_ortu,
                    'pangkat_ortu' => $surat->pangkat_ortu,
                    'instansi_ortu' => $surat->instansi_ortu,
                    'alamat_kantor_ortu' => $surat->alamat_kantor_ortu,
                    'file_ktm' => $surat->file_ktm,
                    'bukti_pembayaran' => $surat->bukti_pembayaran,
                    'file_pendukung' => $surat->file_pendukung,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

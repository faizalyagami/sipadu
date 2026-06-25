<?php
// app/Http/Controllers/Mahasiswa/PengajuanSuratController.php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\JenisSurat;
use App\Models\Surat;
use Illuminate\Http\Request;

class PengajuanSuratController extends Controller
{
    public function index()
    {
        $jenisSurats = JenisSurat::where('is_active', true)->get();
        return view('mahasiswa.pengajuan.index', compact('jenisSurats'));
    }

    public function getFormFields($jenisSuratId)
    {
        $jenisSurat = JenisSurat::with('kategoriSurat')->findOrFail($jenisSuratId);
        $kategori = $jenisSurat->kategoriSurat->nama_kategori ?? '';
        $fields = [];

        if ($kategori == 'Surat Permohonan') {
            $fields = [
                'nama_ortu' => ['type' => 'text', 'label' => 'Nama Orang Tua', 'required' => true],
                'nik_ortu' => ['type' => 'text', 'label' => 'NIK Orang Tua', 'required' => true],
                'pangkat_ortu' => ['type' => 'text', 'label' => 'Pangkat Orang Tua', 'required' => true],
                'instansi_ortu' => ['type' => 'text', 'label' => 'Instansi Orang Tua', 'required' => true],
                'alamat_kantor_ortu' => ['type' => 'text', 'label' => 'Alamat Kantor Orang Tua', 'required' => true],
                'bukti_pembayaran' => ['type' => 'file', 'label' => 'Bukti Pembayaran', 'required' => true, 'accept' => '.pdf,.jpg,.jpeg,.png']
            ];
        } elseif ($kategori == 'Surat Izin') {
            $fields = [
                'tipe_pengajuan' => ['type' => 'select', 'label' => 'Tipe Pengajuan', 'required' => true, 'options' => ['individu' => 'Individu', 'kelompok' => 'Kelompok']],
                'nama_kelompok' => ['type' => 'text', 'label' => 'Nama Kelompok', 'required' => false],
                'file_ktm' => ['type' => 'file', 'label' => 'Upload KTM', 'required' => true, 'accept' => '.pdf,.jpg,.jpeg,.png']
            ];
        }

        return response()->json([
            'success' => true,
            'fields' => $fields,
            'kategori' => $kategori,
            'jenis_surat' => $jenisSurat->nama_surat
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_surat_id' => 'required|exists:jenis_surats,id',
            'keperluan' => 'required|string'
        ]);

        $user = auth()->user();

        if (!$user->mahasiswa) {
            return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        $mahasiswa = $user->mahasiswa;
        $jenisSurat = JenisSurat::findOrFail($request->jenis_surat_id);

        // Generate content dari template menggunakan method di model
        $content = $jenisSurat->generateSuratContent($request->all(), $mahasiswa);

        Surat::create([
            'mahasiswa_id' => $mahasiswa->id,
            'jenis_surat_id' => $request->jenis_surat_id,
            'keperluan' => $request->keperluan,
            'content' => $content,
            'status' => 'pending'
        ]);

        return redirect()->route('mahasiswa.pengajuan.index')
            ->with('success', 'Pengajuan surat berhasil dikirim');
    }

    public function history()
    {
        $user = auth()->user();

        if (!$user->mahasiswa) {
            return view('mahasiswa.pengajuan.history', [
                'surats' => collect([]),
                'error' => 'Data mahasiswa tidak ditemukan'
            ]);
        }

        $surats = Surat::where('mahasiswa_id', $user->mahasiswa->id)
            ->with('jenisSurat')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('mahasiswa.pengajuan.history', compact('surats'));
    }

    public function download($id)
    {
        try {
            set_time_limit(300);

            $user = auth()->user();

            if (!$user->mahasiswa) {
                return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan');
            }

            $surat = Surat::with(['mahasiswa', 'jenisSurat', 'approvedBy'])->findOrFail($id);

            if ($surat->mahasiswa_id != $user->mahasiswa->id) {
                abort(403);
            }

            if ($surat->status != 'approved') {
                return redirect()->back()->with('error', 'Surat belum disetujui');
            }

            \Log::info('Generating PDF for surat ID: ' . $id);

            // Generate HTML surat
            $html = $surat->generateSuratHtml();

            // Generate PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'defaultFont' => 'Times New Roman',
                'logErrors' => true
            ]);

            $filename = 'Surat_' . ($surat->nomor_surat ?? $surat->id) . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('Download error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            // Coba tampilkan HTML sebagai fallback
            if (isset($surat) && $surat) {
                try {
                    return response()->stream(
                        function () use ($surat) {
                            echo $surat->generateSuratHtml();
                        },
                        200,
                        [
                            'Content-Type' => 'text/html',
                            'Content-Disposition' => 'inline; filename="surat_' . ($surat->nomor_surat ?? $surat->id) . '.html"'
                        ]
                    );
                } catch (\Exception $fallbackError) {
                    \Log::error('Fallback HTML error: ' . $fallbackError->getMessage());
                }
            }

            return redirect()->back()->with('error', 'Gagal mendownload surat: ' . $e->getMessage());
        }
    }
}

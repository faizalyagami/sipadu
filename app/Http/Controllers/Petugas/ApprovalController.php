<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\JenisSurat;
use App\Services\PDFGenerator;
use App\Traits\PDFGenerationTrait;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
        try {
            $surat = Surat::findOrFail($id);

            // Approve surat (generate nomor surat, dll)
            $surat->approveWithTTD(auth()->id());

            // Generate PDF dan simpan
            $pdfGenerator = new PDFGenerator();
            $pdfPath = $pdfGenerator->generateAndSave($surat);

            Log::info('Surat approved and PDF generated: ' . $surat->id);

            return response()->json([
                'success' => true,
                'message' => 'Surat berhasil disetujui dan PDF telah dibuat',
                'pdf_path' => $pdfPath
            ]);
        } catch (\Exception $e) {
            Log::error('Approve error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui surat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject surat
     */
    public function reject(Request $request, $id)
    {
        try {
            $request->validate([
                'alasan' => 'required|string|min:5'
            ]);

            $surat = Surat::findOrFail($id);
            $surat->status = 'rejected';
            $surat->approved_by = auth()->id();
            $surat->approved_at = now();
            $surat->alasan_reject = $request->alasan;
            $surat->save();

            return response()->json([
                'success' => true,
                'message' => 'Surat berhasil ditolak'
            ]);
        } catch (\Exception $e) {
            Log::error('Reject error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak surat: ' . $e->getMessage()
            ], 500);
        }
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
            Log::info('=== getSuratData called for ID: ' . $id . ' ===');

            // Cari surat dengan eager loading
            $surat = Surat::with([
                'mahasiswa.prodi.fakultas',
                'jenisSurat.kategoriSurat',
                'approvedBy'
            ])->find($id);

            // Jika surat tidak ditemukan
            if (!$surat) {
                Log::error('Surat not found for ID: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Surat tidak ditemukan'
                ], 404);
            }

            Log::info('Surat found:', [
                'id' => $surat->id,
                'mahasiswa_id' => $surat->mahasiswa_id,
                'jenis_surat_id' => $surat->jenis_surat_id,
                'status' => $surat->status
            ]);

            $mahasiswa = $surat->mahasiswa;

            if (!$mahasiswa) {
                Log::error('Mahasiswa not found for surat ID: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Data mahasiswa tidak ditemukan'
                ], 404);
            }

            // ============================================
            // AMBIL DATA ORANG TUA
            // ============================================
            $dataOrangTua = [
                'nama_ortu' => $surat->nama_ortu ?? '-',
                'nik_ortu' => $surat->nik_ortu ?? '-',
                'pangkat_ortu' => $surat->pangkat_ortu ?? '-',
                'instansi_ortu' => $surat->instansi_ortu ?? '-',
                'alamat_kantor_ortu' => $surat->alamat_kantor_ortu ?? '-',
            ];

            Log::info('Data Orang Tua:', $dataOrangTua);

            // ============================================
            // GENERATE CONTENT SURAT
            // ============================================
            $replacements = [
                '{nama_mahasiswa}' => $mahasiswa->nama_lengkap ?? '-',
                '{npm}' => $mahasiswa->npm ?? '-',
                '{alamat}' => $mahasiswa->alamat ?? '-',
                '{fakultas}' => $mahasiswa->prodi->fakultas->nama_fakultas ?? '-',
                '{prodi}' => $mahasiswa->prodi->nama_prodi ?? '-',
                '{semester}' => $mahasiswa->semester ?? '-',
                '{nomor_surat}' => $surat->nomor_surat ?? '-',
                '{tanggal_surat}' => $surat->approved_at ? $surat->approved_at->format('d F Y') : now()->format('d F Y'),
                '{perihal}' => $surat->keperluan ?? '-',
                '{dekan}' => $surat->ttd_nama ?? 'Dr. Oki Mardiawan, M.Psi., Psikolog.',
                '{nip_dekan}' => $surat->ttd_nip ?? 'D.07.0.464',
                '{nama_orangtua}' => $dataOrangTua['nama_ortu'],
                '{nrp_nik_nip}' => $dataOrangTua['nik_ortu'],
                '{pangkat_orangtua}' => $dataOrangTua['pangkat_ortu'],
                '{instansi_orangtua}' => $dataOrangTua['instansi_ortu'],
                '{alamat_kantor}' => $dataOrangTua['alamat_kantor_ortu'],
            ];

            $templateContent = $surat->jenisSurat->template_content ?? '';
            $content = str_replace(array_keys($replacements), array_values($replacements), $templateContent);

            if (empty($content) && $surat->content) {
                $content = $surat->content;
            }

            // ============================================
            // RESPONSE DATA
            // ============================================
            $responseData = [
                'id' => $surat->id,
                'mahasiswa_nama' => $mahasiswa->nama_lengkap ?? '-',
                'mahasiswa_npm' => $mahasiswa->npm ?? '-',
                'fakultas' => $mahasiswa->prodi->fakultas->nama_fakultas ?? '-',
                'jenis_surat' => $surat->jenisSurat->nama_surat ?? '-',
                'keperluan' => $surat->keperluan ?? '-',
                'content' => $content,
                'tanggal_pengajuan' => $surat->created_at->format('d/m/Y H:i'),
                'tanggal_diproses' => $surat->approved_at ? $surat->approved_at->format('d/m/Y H:i') : '-',
                'status' => $surat->status,
                'alasan_reject' => $surat->alasan_reject,
                'nomor_surat' => $surat->nomor_surat ?? '-',
                'ttd_nama' => $surat->ttd_nama ?? '-',
                'ttd_nip' => $surat->ttd_nip ?? '-',
                'ttd_jabatan' => $surat->ttd_jabatan ?? '-',
                'approved_by' => $surat->approvedBy->name ?? '-',
                // Data Orang Tua
                'nama_ortu' => $dataOrangTua['nama_ortu'],
                'nik_ortu' => $dataOrangTua['nik_ortu'],
                'pangkat_ortu' => $dataOrangTua['pangkat_ortu'],
                'instansi_ortu' => $dataOrangTua['instansi_ortu'],
                'alamat_kantor_ortu' => $dataOrangTua['alamat_kantor_ortu'],
                // File pendukung
                'file_ktm' => $surat->file_ktm ?? null,
                'bukti_pembayaran' => $surat->bukti_pembayaran ?? null,
                'file_pendukung' => $surat->file_pendukung ?? null,
            ];

            Log::info('Response data keys:', array_keys($responseData));

            return response()->json([
                'success' => true,
                'data' => $responseData
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting surat data: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getHistoryDetail($id)
    {
        try {
            $surat = Surat::with(['mahasiswa.prodi.fakultas', 'jenisSurat', 'approvedBy'])->findOrFail($id);

            $mahasiswa = $surat->mahasiswa;

            // Siapkan data untuk replace variabel di template
            $replacements = [
                '{nama_mahasiswa}' => $mahasiswa->nama_lengkap ?? '-',
                '{npm}' => $mahasiswa->npm ?? '-',
                '{alamat}' => $mahasiswa->alamat ?? '-',
                '{fakultas}' => $mahasiswa->prodi->fakultas->nama_fakultas ?? '-',
                '{prodi}' => $mahasiswa->prodi->nama_prodi ?? '-',
                '{semester}' => $mahasiswa->semester ?? '-',
                '{nomor_surat}' => $surat->nomor_surat ?? '-',
                '{tanggal_surat}' => $surat->approved_at ? $surat->approved_at->format('d F Y') : now()->format('d F Y'),
                '{perihal}' => $surat->keperluan ?? '-',
                '{dekan}' => $surat->ttd_nama ?? 'Dr. Oki Mardiawan, M.Psi., Psikolog.',
                '{nip_dekan}' => $surat->ttd_nip ?? 'D.07.0.464',
                '{nama_orangtua}' => $surat->nama_ortu ?? '-',
                '{nrp_nik_nip}' => $surat->nik_ortu ?? '-',
                '{pangkat_orangtua}' => $surat->pangkat_ortu ?? '-',
                '{instansi_orangtua}' => $surat->instansi_ortu ?? '-',
                '{alamat_kantor}' => $surat->alamat_kantor_ortu ?? '-',
            ];

            // Ambil template dan replace variabel
            $templateContent = $surat->jenisSurat->template_content ?? '';
            $content = str_replace(array_keys($replacements), array_values($replacements), $templateContent);

            // Jika tidak ada template, gunakan content dari surat
            if (empty($content) && $surat->content) {
                $content = $surat->content;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $surat->id,
                    'mahasiswa_nama' => $mahasiswa->nama_lengkap,
                    'mahasiswa_npm' => $mahasiswa->npm,
                    'fakultas' => $mahasiswa->prodi->fakultas->nama_fakultas ?? '-',
                    'jenis_surat' => $surat->jenisSurat->nama_surat,
                    'keperluan' => $surat->keperluan,
                    'content' => $content,
                    'tanggal_pengajuan' => $surat->created_at->format('d/m/Y H:i'),
                    'tanggal_diproses' => $surat->approved_at ? $surat->approved_at->format('d/m/Y H:i') : '-',
                    'status' => $surat->status,
                    'alasan_reject' => $surat->alasan_reject,
                    'nomor_surat' => $surat->nomor_surat,
                    'ttd_nama' => $surat->ttd_nama,
                    'ttd_nip' => $surat->ttd_nip,
                    'ttd_jabatan' => $surat->ttd_jabatan,
                    'approved_by' => $surat->approvedBy->name ?? '-',
                    'nama_ortu' => $surat->nama_ortu ?? '-',
                    'nik_ortu' => $surat->nik_ortu ?? '-',
                    'pangkat_ortu' => $surat->pangkat_ortu ?? '-',
                    'instansi_ortu' => $surat->instansi_ortu ?? '-',
                    'alamat_kantor_ortu' => $surat->alamat_kantor_ortu ?? '-',
                    'file_ktm' => $surat->file_ktm,
                    'bukti_pembayaran' => $surat->bukti_pembayaran,
                    'file_pendukung' => $surat->file_pendukung,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadSurat($id)
    {
        try {
            $surat = Surat::with(['mahasiswa', 'jenisSurat', 'approvedBy'])->findOrFail($id);

            if ($surat->status != 'approved') {
                return redirect()->back()->with('error', 'Surat belum disetujui');
            }

            // Cek apakah PDF sudah ada
            if (empty($surat->pdf_path) || !Storage::disk('public')->exists($surat->pdf_path)) {
                Log::warning('PDF not found for surat ID: ' . $id . ', regenerating...');

                $pdfGenerator = new PDFGenerator();
                $pdfGenerator->generateAndSave($surat);
                $surat->refresh();
            }

            if (!Storage::disk('public')->exists($surat->pdf_path)) {
                throw new \Exception('PDF file not found');
            }

            // ============================================
            // Nama file: {jenis_surat} - {npm mahasiswa}.pdf
            // ============================================
            $jenisSurat = $surat->jenisSurat->nama_surat ?? 'Surat';
            $npm = $surat->mahasiswa->npm ?? 'unknown';

            $filename = $jenisSurat . ' - ' . $npm . '.pdf';
            $filename = preg_replace('/[\/\\\\:*?"<>|]/', '-', $filename);

            $fullPath = storage_path('app/public/' . $surat->pdf_path);

            Log::info('Petugas download surat ID: ' . $id . ' - File: ' . $filename);

            return response()->download($fullPath, $filename, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'public, max-age=86400',
                'Pragma' => 'public',
            ]);
        } catch (\Exception $e) {
            Log::error('Petugas download error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->back()->with('error', 'Gagal mendownload surat: ' . $e->getMessage());
        }
    }

    /**
     * Generate dan download PDF (digunakan oleh petugas)
    private function generateAndDownloadPDF($surat)
    {
        // Cek cache PDF
        $cacheKey = 'surat_pdf_' . $surat->id . '_' . md5($surat->updated_at);
        $cachePath = storage_path('app/cache/pdf/' . $cacheKey . '.pdf');

        // Buat folder cache jika belum ada
        if (!file_exists(storage_path('app/cache/pdf'))) {
            mkdir(storage_path('app/cache/pdf'), 0777, true);
        }

        // Jika cache ada dan masih fresh (kurang dari 1 jam)
        if (file_exists($cachePath) && (time() - filemtime($cachePath) < 3600)) {
            Log::info('Menggunakan cache PDF untuk surat ID: ' . $surat->id);

            $filename = 'Surat_' . ($surat->nomor_surat ?? $surat->id) . '.pdf';

            return response()->file($cachePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, must-revalidate',
                'Pragma' => 'no-cache',
            ]);
        }

        // Generate HTML
        $html = $surat->generateSuratHtml();

        Log::info('HTML size: ' . strlen($html) . ' bytes');

        // Generate PDF dengan optimasi
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'defaultFont' => 'Times New Roman',
            'logErrors' => false,
            'dpi' => 72,
            'enable_remote' => true,
            'font_cache' => storage_path('fonts/'),
            'tempDir' => storage_path('temp/'),
            'chroot' => public_path(),
        ]);

        // Simpan ke cache
        $pdf->save($cachePath);

        Log::info('PDF cache saved: ' . $cachePath);

        $filename = 'Surat_' . ($surat->nomor_surat ?? $surat->id) . '.pdf';

        return response()->download($cachePath, $filename, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }
     */
}

<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\JenisSurat;
use App\Models\Surat;
use App\Traits\PDFGenerationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PengajuanSuratController extends Controller
{

    public function index()
    {
        $jenisSurats = JenisSurat::where('is_active', true)->with('kategoriSurat')->get();
        return view('mahasiswa.pengajuan.index', compact('jenisSurats'));
    }

    public function getFormFields($jenisSuratId)
    {
        try {
            $jenisSurat = JenisSurat::with('kategoriSurat')->findOrFail($jenisSuratId);
            $kategori = $jenisSurat->kategoriSurat->nama_kategori ?? '';

            $fields = $this->getDefaultFields($jenisSurat);

            return response()->json([
                'success' => true,
                'fields' => $fields,
                'kategori' => $kategori,
                'jenis_surat' => $jenisSurat->nama_surat,
                'jenis_surat_id' => $jenisSurat->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat form: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getDefaultFields($jenisSurat)
    {
        $namaSurat = $jenisSurat->nama_surat;
        $kategori = $jenisSurat->kategoriSurat->nama_kategori ?? '';
        $fields = [];

        // ============================================
        // FIELD SEMESTER - TAMBAHKAN UNTUK SEMUA JENIS SURAT
        // ============================================
        $fields['semester'] = [
            'type' => 'select',
            'label' => 'Semester',
            'required' => true,
            'options' => [
                '1' => 'Semester 1',
                '2' => 'Semester 2',
                '3' => 'Semester 3',
                '4' => 'Semester 4',
                '5' => 'Semester 5',
                '6' => 'Semester 6',
                '7' => 'Semester 7',
                '8' => 'Semester 8',
                '9' => 'Semester 9',
                '10' => 'Semester 10',
                '11' => 'Semester 11',
                '12' => 'Semester 12',
                '13' => 'Semester 13',
                '14' => 'Semester 14',
            ],
            'help_text' => 'Pilih semester saat ini',
            'order' => 0 // Urutan pertama
        ];

        // ============================================
        // KATEGORI SURAT KETERANGAN
        // ============================================
        if ($kategori == 'Surat Keterangan') {
            if (strpos($namaSurat, 'Aktif Kuliah') !== false) {
                $fields['nama_ortu'] = [
                    'type' => 'text',
                    'label' => 'Nama Orang Tua / Wali',
                    'required' => true,
                    'placeholder' => 'Masukkan nama lengkap orang tua/wali',
                    'help_text' => 'Nama sesuai KTP',
                    'order' => 1
                ];
                $fields['nik_ortu'] = [
                    'type' => 'text',
                    'label' => 'NRP/NIK/NIP Orang Tua',
                    'required' => true,
                    'placeholder' => 'Masukkan NIK/NIP',
                    'help_text' => 'Nomor Induk Kependudukan atau NIP',
                    'order' => 2
                ];
                $fields['pangkat_ortu'] = [
                    'type' => 'text',
                    'label' => 'Pangkat/Golongan Orang Tua',
                    'required' => true,
                    'placeholder' => 'Contoh: Golongan IV-B',
                    'help_text' => 'Untuk PNS/TNI/Polri',
                    'order' => 3
                ];
                $fields['instansi_ortu'] = [
                    'type' => 'text',
                    'label' => 'Instansi/Tempat Kerja Orang Tua',
                    'required' => true,
                    'placeholder' => 'Nama instansi atau perusahaan',
                    'help_text' => 'Jika tidak bekerja, isi dengan "-"',
                    'order' => 4
                ];
                $fields['alamat_kantor_ortu'] = [
                    'type' => 'textarea',
                    'label' => 'Alamat Kantor Orang Tua',
                    'required' => true,
                    'placeholder' => 'Masukkan alamat kantor/instansi',
                    'help_text' => 'Alamat lengkap tempat bekerja',
                    'order' => 5
                ];
            }
        }

        // Urutkan fields berdasarkan order
        uasort($fields, function ($a, $b) {
            $orderA = $a['order'] ?? 999;
            $orderB = $b['order'] ?? 999;
            return $orderA <=> $orderB;
        });

        return $fields;
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_surat_id' => 'required|exists:jenis_surats,id',
            'keperluan' => 'required|string',
            'semester' => 'required|string|in:1,2,3,4,5,6,7,8,9,10,11,12,13,14'
        ]);

        $user = auth()->user();

        if (!$user->mahasiswa) {
            return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        $mahasiswa = $user->mahasiswa;
        $jenisSurat = JenisSurat::findOrFail($request->jenis_surat_id);

        $kategori = $jenisSurat->kategoriSurat->nama_kategori ?? '';
        $rules = [];

        if ($kategori == 'Surat Keterangan') {
            $rules = [
                'nama_ortu' => 'required|string|max:255',
                'nik_ortu' => 'required|string|max:50',
                'pangkat_ortu' => 'required|string|max:100',
                'instansi_ortu' => 'required|string|max:255',
                'alamat_kantor_ortu' => 'required|string',
            ];
        }

        if (!empty($rules)) {
            $request->validate($rules);
        }

        $dataOrangTua = [
            'nama_ortu' => $request->nama_ortu,
            'nik_ortu' => $request->nik_ortu,
            'pangkat_ortu' => $request->pangkat_ortu,
            'instansi_ortu' => $request->instansi_ortu,
            'alamat_kantor_ortu' => $request->alamat_kantor_ortu,
        ];

        Log::info('Data Orang Tua:', $dataOrangTua);

        $content = $jenisSurat->generateSuratContent($request->all(), $mahasiswa);

        $dataTambahan = [
            'semester' => $request->semester,
            'tipe_pengajuan' => $request->tipe_pengajuan,
            'nama_kelompok' => $request->nama_kelompok,
        ];

        // Tambahkan field lain yang mungkin ada
        $extraFields = ['tahun_lulus', 'ipk_lulus', 'predikat', 'pekerjaan_ortu', 'penghasilan_ortu', 'tanggungan'];
        foreach ($extraFields as $field) {
            if ($request->has($field)) {
                $dataTambahan[$field] = $request->$field;
            }
        }

        Surat::create([
            'mahasiswa_id' => $mahasiswa->id,
            'jenis_surat_id' => $request->jenis_surat_id,
            'keperluan' => $request->keperluan,
            'content' => $content,
            'status' => 'pending',
            'nama_ortu' => $request->nama_ortu,
            'nik_ortu' => $request->nik_ortu,
            'pangkat_ortu' => $request->pangkat_ortu,
            'instansi_ortu' => $request->instansi_ortu,
            'alamat_kantor_ortu' => $request->alamat_kantor_ortu,
            'data_tambahan' => json_encode($dataTambahan),
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

            // Cek apakah PDF sudah ada
            if (empty($surat->pdf_path) || !Storage::disk('public')->exists($surat->pdf_path)) {
                Log::warning('PDF not found for surat ID: ' . $id . ', regenerating...');

                $pdfGenerator = new \App\Services\PDFGenerator();
                $pdfGenerator->generateAndSave($surat);
                $surat->refresh();
            }

            // Pastikan file benar-benar ada
            if (!Storage::disk('public')->exists($surat->pdf_path)) {
                throw new \Exception('PDF file not found after regeneration');
            }

            // ============================================
            // PERBAIKAN: Nama file: {jenis_surat} - {npm mahasiswa}.pdf
            // ============================================
            $jenisSurat = $surat->jenisSurat->nama_surat ?? 'Surat';
            $npm = $surat->mahasiswa->npm ?? 'unknown';

            // Buat nama file
            $filename = $jenisSurat . ' - ' . $npm . '.pdf';

            // Hapus karakter ilegal untuk nama file (Windows/Linux)
            $filename = preg_replace('/[\/\\\\:*?"<>|]/', '-', $filename);

            $fullPath = storage_path('app/public/' . $surat->pdf_path);

            Log::info('Download surat ID: ' . $id . ' - File: ' . $filename);

            // Return download dengan response yang benar
            return response()->download($fullPath, $filename, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'public, max-age=86400',
                'Pragma' => 'public',
            ]);
        } catch (\Exception $e) {
            Log::error('Download error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->back()->with('error', 'Gagal mendownload surat: ' . $e->getMessage());
        }
    }
}

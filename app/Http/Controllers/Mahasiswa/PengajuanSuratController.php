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
        try {
            $jenisSurat = JenisSurat::with('kategoriSurat')->findOrFail($jenisSuratId);
            $kategori = $jenisSurat->kategoriSurat->nama_kategori ?? '';

            // Ambil field dari database (jika ada) atau gunakan default
            $fields = $jenisSurat->getFormFields();

            // Jika tidak ada field di database, gunakan default berdasarkan kategori dan jenis surat
            if (empty($fields)) {
                $fields = $this->getDefaultFields($jenisSurat);
            }

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
        // KATEGORI SURAT KETERANGAN
        // ============================================
        if ($kategori == 'Surat Keterangan') {

            // 1. SURAT KETERANGAN AKTIF KULIAH
            if (strpos($namaSurat, 'Aktif Kuliah') !== false) {
                $fields = [
                    'nama_ortu' => [
                        'type' => 'text',
                        'label' => 'Nama Orang Tua / Wali',
                        'required' => true,
                        'placeholder' => 'Masukkan nama lengkap orang tua/wali',
                        'help_text' => 'Nama sesuai KTP'
                    ],
                    'nik_ortu' => [
                        'type' => 'text',
                        'label' => 'NRP/NIK/NIP Orang Tua',
                        'required' => true,
                        'placeholder' => 'Masukkan NIK/NIP',
                        'help_text' => 'Nomor Induk Kependudukan atau NIP'
                    ],
                    'pangkat_ortu' => [
                        'type' => 'text',
                        'label' => 'Pangkat/Golongan Orang Tua',
                        'required' => true,
                        'placeholder' => 'Contoh: Golongan IV-B',
                        'help_text' => 'Untuk PNS/TNI/Polri'
                    ],
                    'instansi_ortu' => [
                        'type' => 'text',
                        'label' => 'Instansi/Tempat Kerja Orang Tua',
                        'required' => true,
                        'placeholder' => 'Nama instansi atau perusahaan',
                        'help_text' => 'Jika tidak bekerja, isi dengan "-"'
                    ],
                    'alamat_kantor_ortu' => [
                        'type' => 'textarea',
                        'label' => 'Alamat Kantor Orang Tua',
                        'required' => true,
                        'placeholder' => 'Masukkan alamat kantor/instansi',
                        'help_text' => 'Alamat lengkap tempat bekerja'
                    ],
                    'pekerjaan_ortu' => [
                        'type' => 'text',
                        'label' => 'Pekerjaan Orang Tua',
                        'required' => false,
                        'placeholder' => 'Contoh: PNS, Wiraswasta, dll',
                        'help_text' => 'Opsional'
                    ],
                    'no_hp_ortu' => [
                        'type' => 'text',
                        'label' => 'No HP Orang Tua',
                        'required' => false,
                        'placeholder' => 'Contoh: 081234567890',
                        'help_text' => 'Nomor yang dapat dihubungi'
                    ]
                ];
            }

            // 2. SURAT KETERANGAN LULUS
            elseif (strpos($namaSurat, 'Lulus') !== false) {
                $fields = [
                    'tahun_lulus' => [
                        'type' => 'number',
                        'label' => 'Tahun Lulus',
                        'required' => true,
                        'placeholder' => 'Contoh: 2024',
                        'help_text' => 'Tahun kelulusan'
                    ],
                    'ipk_lulus' => [
                        'type' => 'text',
                        'label' => 'IPK Lulus',
                        'required' => true,
                        'placeholder' => 'Contoh: 3.75',
                        'help_text' => 'IPK akhir saat lulus'
                    ],
                    'predikat' => [
                        'type' => 'select',
                        'label' => 'Predikat Kelulusan',
                        'required' => true,
                        'options' => [
                            'Cumlaude' => 'Cumlaude',
                            'Sangat Memuaskan' => 'Sangat Memuaskan',
                            'Memuaskan' => 'Memuaskan'
                        ]
                    ]
                ];
            }

            // 3. SURAT KETERANGAN PENGHASILAN ORANG TUA
            elseif (strpos($namaSurat, 'Penghasilan') !== false) {
                $fields = [
                    'nama_ortu' => [
                        'type' => 'text',
                        'label' => 'Nama Orang Tua / Wali',
                        'required' => true,
                        'placeholder' => 'Masukkan nama lengkap orang tua/wali'
                    ],
                    'pekerjaan_ortu' => [
                        'type' => 'text',
                        'label' => 'Pekerjaan Orang Tua',
                        'required' => true,
                        'placeholder' => 'Contoh: PNS, Wiraswasta, Petani, dll'
                    ],
                    'penghasilan_ortu' => [
                        'type' => 'number',
                        'label' => 'Penghasilan Orang Tua per Bulan',
                        'required' => true,
                        'placeholder' => 'Contoh: 5000000',
                        'help_text' => 'Dalam Rupiah (Rp)'
                    ],
                    'tanggungan' => [
                        'type' => 'number',
                        'label' => 'Jumlah Tanggungan Keluarga',
                        'required' => true,
                        'placeholder' => 'Contoh: 3',
                        'help_text' => 'Jumlah orang yang ditanggung'
                    ]
                ];
            }

            // 4. SURAT KETERANGAN DOMISILI
            elseif (strpos($namaSurat, 'Domisili') !== false) {
                $fields = [
                    'alamat_ktp' => [
                        'type' => 'textarea',
                        'label' => 'Alamat Sesuai KTP',
                        'required' => true,
                        'placeholder' => 'Masukkan alamat sesuai KTP',
                        'help_text' => 'Alamat yang tertera di KTP'
                    ],
                    'alamat_domisili' => [
                        'type' => 'textarea',
                        'label' => 'Alamat Domisili Saat Ini',
                        'required' => true,
                        'placeholder' => 'Masukkan alamat domisili saat ini',
                        'help_text' => 'Alamat tempat tinggal sekarang'
                    ],
                    'lama_tinggal' => [
                        'type' => 'text',
                        'label' => 'Lama Tinggal',
                        'required' => true,
                        'placeholder' => 'Contoh: 5 tahun',
                        'help_text' => 'Lama tinggal di alamat domisili'
                    ]
                ];
            }
        }

        // ============================================
        // KATEGORI SURAT IZIN
        // ============================================
        elseif ($kategori == 'Surat Izin') {

            // 1. SURAT IZIN MAGANG
            if (strpos($namaSurat, 'Magang') !== false) {
                $fields = [
                    'tipe_pengajuan' => [
                        'type' => 'select',
                        'label' => 'Tipe Pengajuan',
                        'required' => true,
                        'options' => ['individu' => 'Individu', 'kelompok' => 'Kelompok']
                    ],
                    'nama_kelompok' => [
                        'type' => 'text',
                        'label' => 'Nama Kelompok',
                        'required' => false,
                        'placeholder' => 'Masukkan nama kelompok (jika kelompok)',
                        'help_text' => 'Isi jika pengajuan kelompok'
                    ],
                    'instansi_magang' => [
                        'type' => 'text',
                        'label' => 'Nama Instansi Magang',
                        'required' => true,
                        'placeholder' => 'Nama perusahaan/instansi tempat magang'
                    ],
                    'alamat_magang' => [
                        'type' => 'textarea',
                        'label' => 'Alamat Instansi Magang',
                        'required' => true,
                        'placeholder' => 'Alamat lengkap instansi magang'
                    ],
                    'tgl_mulai' => [
                        'type' => 'date',
                        'label' => 'Tanggal Mulai Magang',
                        'required' => true
                    ],
                    'tgl_selesai' => [
                        'type' => 'date',
                        'label' => 'Tanggal Selesai Magang',
                        'required' => true
                    ],
                    'file_ktm' => [
                        'type' => 'file',
                        'label' => 'Upload KTM',
                        'required' => true,
                        'accept' => '.pdf,.jpg,.jpeg,.png',
                        'help_text' => 'Upload Kartu Tanda Mahasiswa'
                    ]
                ];
            }

            // 2. SURAT IZIN PENELITIAN
            elseif (strpos($namaSurat, 'Penelitian') !== false) {
                $fields = [
                    'judul_penelitian' => [
                        'type' => 'text',
                        'label' => 'Judul Penelitian',
                        'required' => true,
                        'placeholder' => 'Masukkan judul penelitian',
                        'help_text' => 'Judul lengkap penelitian'
                    ],
                    'lokasi_penelitian' => [
                        'type' => 'textarea',
                        'label' => 'Lokasi Penelitian',
                        'required' => true,
                        'placeholder' => 'Alamat lengkap lokasi penelitian'
                    ],
                    'dosen_pembimbing' => [
                        'type' => 'text',
                        'label' => 'Dosen Pembimbing',
                        'required' => true,
                        'placeholder' => 'Nama dosen pembimbing'
                    ],
                    'tgl_penelitian' => [
                        'type' => 'date',
                        'label' => 'Tanggal Penelitian',
                        'required' => true
                    ],
                    'file_proposal' => [
                        'type' => 'file',
                        'label' => 'Upload Proposal Penelitian',
                        'required' => true,
                        'accept' => '.pdf,.doc,.docx',
                        'help_text' => 'Upload proposal penelitian (max 5MB)'
                    ]
                ];
            }
        }

        // ============================================
        // KATEGORI SURAT PENGAJUAN
        // ============================================
        elseif ($kategori == 'Surat Pengajuan') {

            // 1. SURAT PENGAJUAN BEASISWA
            if (strpos($namaSurat, 'Beasiswa') !== false) {
                $fields = [
                    'jenis_beasiswa' => [
                        'type' => 'select',
                        'label' => 'Jenis Beasiswa',
                        'required' => true,
                        'options' => [
                            'Pemerintah' => 'Pemerintah',
                            'Swasta' => 'Swasta',
                            'Lembaga' => 'Lembaga'
                        ]
                    ],
                    'tujuan_beasiswa' => [
                        'type' => 'text',
                        'label' => 'Tujuan Pengajuan Beasiswa',
                        'required' => true,
                        'placeholder' => 'Contoh: Biaya Pendidikan, Biaya Hidup, dll'
                    ],
                    'file_syarat' => [
                        'type' => 'file',
                        'label' => 'Upload Syarat Pendukung',
                        'required' => true,
                        'accept' => '.pdf,.jpg,.jpeg,.png',
                        'help_text' => 'Upload file syarat yang diminta (max 5MB)'
                    ]
                ];
            }
        }

        return $fields;
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

        // Validasi berdasarkan kategori
        $kategori = $jenisSurat->kategoriSurat->nama_kategori ?? '';
        $rules = [];

        if ($kategori == 'Surat Keterangan') {
            $rules = [
                'nama_ortu' => 'required|string|max:255',
                'nik_ortu' => 'required|string|max:50',
                'pangkat_ortu' => 'required|string|max:100',
                'instansi_ortu' => 'required|string|max:255',
                'alamat_kantor_ortu' => 'required|string',
                'pekerjaan_ortu' => 'nullable|string|max:100',
                'no_hp_ortu' => 'nullable|string|max:15',
            ];
        }

        if (!empty($rules)) {
            $request->validate($rules);
        }

        // Generate content dari template menggunakan method di model
        $content = $jenisSurat->generateSuratContent($request->all(), $mahasiswa);

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
            'pekerjaan_ortu' => $request->pekerjaan_ortu,
            'no_hp_ortu' => $request->no_hp_ortu,
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

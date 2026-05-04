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

    public function store(Request $request)
    {
        $request->validate([
            'jenis_surat_id' => 'required|exists:jenis_surats,id',
            'keperluan' => 'required|string'
        ]);

        // Ambil user yang login
        $user = auth()->user();
        
        // Cek apakah user memiliki relasi mahasiswa
        if (!$user->mahasiswa) {
            return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan. Silakan hubungi administrator.');
        }

        $mahasiswa = $user->mahasiswa;

        Surat::create([
            'mahasiswa_id' => $mahasiswa->id,
            'jenis_surat_id' => $request->jenis_surat_id,
            'keperluan' => $request->keperluan,
            'content' => $request->content ?? '',
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
        $user = auth()->user();
        
        if (!$user->mahasiswa) {
            return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan');
        }
        
        $surat = Surat::with(['mahasiswa', 'jenisSurat'])->findOrFail($id);
        
        // Pastikan surat milik mahasiswa yang login
        if ($surat->mahasiswa_id != $user->mahasiswa->id) {
            abort(403);
        }

        // Pastikan surat sudah disetujui
        if ($surat->status != 'approved') {
            return redirect()->back()->with('error', 'Surat belum disetujui');
        }

        // Generate surat dari template
        $template = $surat->jenisSurat->template_content ?? '<p>Template tidak tersedia</p>';
        
        // Replace variables
        $replacements = [
            '{nama_mahasiswa}' => $surat->mahasiswa->nama_lengkap ?? '',
            '{npm}' => $surat->mahasiswa->npm ?? '',
            '{fakultas}' => $surat->mahasiswa->prodi->fakultas->nama_fakultas ?? '',
            '{prodi}' => $surat->mahasiswa->prodi->nama_prodi ?? '',
            '{tanggal_surat}' => now()->format('d F Y'),
            '{keperluan}' => $surat->keperluan ?? '',
            '{nomor_surat}' => $surat->id,
            '{dekan}' => 'Dekan Fakultas',
            '{nip_dekan}' => '-'
        ];
        
        $content = str_replace(array_keys($replacements), array_values($replacements), $template);
        
        return view('surat.cetak', compact('content'));
    }

    private function generateSuratHtml($surat)
    {
        $template = $surat->jenisSurat->template_content ?? $this->getDefaultTemplate();
        
        // Data untuk template
        $data = [
            '{nomor_surat}' => $surat->nomor_surat ?? '-',
            '{tanggal_surat}' => $surat->approved_at ? $surat->approved_at->format('d F Y') : date('d F Y'),
            '{nama_mahasiswa}' => $surat->mahasiswa->nama_lengkap ?? '-',
            '{npm}' => $surat->mahasiswa->npm ?? '-',
            '{tempat_lahir}' => $surat->mahasiswa->tempat_lahir ?? '-',
            '{tanggal_lahir}' => $surat->mahasiswa->tanggal_lahir ? $surat->mahasiswa->tanggal_lahir->format('d F Y') : '-',
            '{alamat}' => $surat->mahasiswa->alamat ?? '-',
            '{fakultas}' => $surat->mahasiswa->prodi->fakultas->nama_fakultas ?? '-',
            '{prodi}' => $surat->mahasiswa->prodi->nama_prodi ?? '-',
            '{jenjang}' => $surat->mahasiswa->prodi->jenjang ?? '-',
            '{ipk}' => $surat->mahasiswa->ipk ?? '-',
            '{semester}' => $this->getSemester($surat->mahasiswa->tanggal_masuk),
            '{keperluan}' => $surat->keperluan,
            '{ttd_nama}' => $surat->ttd_nama ?? 'Dekan Fakultas',
            '{ttd_nip}' => $surat->ttd_nip ?? '-',
            '{ttd_jabatan}' => $surat->ttd_jabatan ?? 'Dekan',
            '{ttd_elektronik}' => $surat->ttd_elektronik ?? ''
        ];
        
        // Replace variables
        $html = str_replace(array_keys($data), array_values($data), $template);
        
        return $this->addPrintStyles($html);
    }

    private function getSemester($tanggalMasuk)
    {
        if (!$tanggalMasuk) return '-';
        
        $tahunMasuk = $tanggalMasuk->year;
        $tahunSekarang = date('Y');
        $selisihTahun = $tahunSekarang - $tahunMasuk;
        $semester = ($selisihTahun * 2) + 1;
        
        return $semester . ' (Genap)';
    }

    private function addPrintStyles($html)
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Surat Keterangan</title>
            <style>
                @page {
                    size: A4;
                    margin: 2cm;
                }
                body {
                    font-family: "Times New Roman", Times, serif;
                    font-size: 12pt;
                    line-height: 1.5;
                }
                .kop-surat {
                    text-align: center;
                    border-bottom: 3px solid #6f42c1;
                    padding-bottom: 10px;
                    margin-bottom: 20px;
                }
                .logo {
                    text-align: center;
                    margin-bottom: 10px;
                }
                .universitas h1 {
                    font-size: 18pt;
                    margin: 0;
                    color: #6f42c1;
                }
                .universitas h2 {
                    font-size: 14pt;
                    margin: 5px 0;
                }
                .nomor-surat {
                    text-align: center;
                    margin: 20px 0;
                    font-weight: bold;
                }
                .hal {
                    text-align: center;
                    margin: 10px 0;
                    text-decoration: underline;
                    font-weight: bold;
                }
                .isi-surat {
                    text-align: justify;
                    margin: 20px 0;
                }
                table {
                    width: 100%;
                    margin: 15px 0;
                }
                td {
                    padding: 5px;
                    vertical-align: top;
                }
                .label {
                    width: 140px;
                    font-weight: bold;
                }
                .tanda-tangan {
                    margin-top: 40px;
                    text-align: right;
                }
                .ttd-image {
                    max-width: 200px;
                    height: auto;
                    margin-top: 10px;
                }
                .footer {
                    margin-top: 30px;
                    font-size: 10pt;
                    text-align: center;
                    border-top: 1px solid #ccc;
                    padding-top: 10px;
                }
            </style>
        </head>
        <body>
            ' . $html . '
        </body>
        </html>
        ';
    }

}
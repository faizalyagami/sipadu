<?php

namespace App\Services;

use App\Models\Surat;
use Illuminate\Support\Facades\Log;

class SuratHtmlGenerator
{
    /**
     * Generate HTML surat - SEDERHANA
     */
    public function generate(Surat $surat): string
    {
        $surat->loadMissing(['mahasiswa.prodi.fakultas', 'jenisSurat.kategoriSurat']);

        $mahasiswa = $surat->mahasiswa;
        $jenisSurat = $surat->jenisSurat;

        // Ambil template dari database
        $templateContent = $jenisSurat->template_content ?? $this->getDefaultTemplate();

        // Prepare data untuk replace
        $data = $this->prepareData($surat, $mahasiswa);

        // Replace semua variabel
        $htmlContent = strtr($templateContent, $data);

        $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Surat Keterangan</title>
        <style>
            @page { size: A4; margin: 0; }
            body { 
                font-family: "Times New Roman", Times, serif; 
                font-size: 12pt; 
                line-height: 1.5; 
                background: white; 
                margin: 0; 
                padding: 0; 
            }
            .kop-surat { 
                margin: 0; 
                padding: 0; 
                width: 100%; 
            }
            .kop-surat img { 
                width: 100%; 
                max-width: 100%; 
                height: auto; 
                display: block; 
                margin: 0; 
            }
            .surat-container {
                max-width: 210mm;
                margin: 0 auto;
                padding: 0 20mm 20mm 20mm;
                background: white;
            }
            .surat-content {
                padding-top: 8mm;
            }
            table { width: 100%; border-collapse: collapse; }
            td { padding: 3px 0; vertical-align: top; border: none; }
            img { max-width: 100%; height: auto; }
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .text-justify { text-align: justify; }
            .surat-title { 
                font-size: 14pt; 
                font-weight: bold; 
                text-align: center; 
                margin: 8px 0 4px; 
            }
            .surat-nomor { 
                font-weight: bold; 
                text-align: center; 
                margin-bottom: 12px; 
            }
            .label-col { width: 120px; }
            .ttd-area { margin-top: 35px; text-align: right; }
            .ttd-image { max-width: 150px; height: auto; margin-top: 5px; }
            @media print { body { margin: 0; padding: 0; } }
        </style>
    </head>
    <body>
        <!-- KOP SURAT FULL WIDTH -->
        <div class="kop-surat">
            ' . $data['{kop_surat}'] . '
        </div>
        
        <!-- ISI SURAT -->
        <div class="surat-container">
            <div class="surat-content">
                ' . $htmlContent . '
            </div>
        </div>
    </body>
    </html>';

        // Debug
        Log::info('HTML size: ' . strlen($html) . ' bytes');
        file_put_contents(storage_path('app/debug_final_' . $surat->id . '.html'), $html);

        return $html;
    }

    /**
     * Prepare data untuk replace variabel
     */
    private function prepareData(Surat $surat, $mahasiswa): array
    {
        $dataTambahan = json_decode($surat->data_tambahan, true) ?? [];

        $data = [
            // Data Mahasiswa
            '{nama_mahasiswa}' => $mahasiswa->nama_lengkap ?? '-',
            '{npm}' => $mahasiswa->npm ?? '-',
            '{alamat}' => $mahasiswa->alamat ?? '-',
            '{tempat_lahir}' => $mahasiswa->tempat_lahir ?? '-',
            '{tanggal_lahir}' => $mahasiswa->tanggal_lahir ? date('d F Y', strtotime($mahasiswa->tanggal_lahir)) : '-',
            '{no_hp}' => $mahasiswa->no_hp ?? '-',

            // Data Akademik
            '{fakultas}' => $mahasiswa->prodi->fakultas->nama_fakultas ?? '-',
            '{prodi}' => $mahasiswa->prodi->nama_prodi ?? '-',
            '{jenjang}' => $mahasiswa->prodi->jenjang ?? 'S1',
            '{ipk}' => $mahasiswa->ipk ?? '-',
            '{semester}' => $surat->semester,

            // Data Surat
            '{nomor_surat}' => $surat->nomor_surat ?? $surat->id,
            '{tanggal_surat}' => $surat->approved_at ? $surat->approved_at->setTimezone('Asia/Jakarta')->format('d F Y') : date('d F Y'),
            '{perihal}' => $surat->keperluan ?? '-',
            '{keperluan}' => $surat->keperluan ?? '-',

            // Data Tanda Tangan
            '{ttd_nama}' => $surat->ttd_nama ?? 'Dekan Fakultas',
            '{ttd_nip}' => $surat->ttd_nip ?? '-',
            '{ttd_jabatan}' => $surat->ttd_jabatan ?? 'Dekan',
            '{dekan}' => $surat->ttd_nama ?? 'Dekan Fakultas',
            '{nip_dekan}' => $surat->ttd_nip ?? '-',

            // Data Orang Tua
            '{nama_orangtua}' => $surat->nama_ortu ?? '-',
            '{nrp_nik_nip}' => $surat->nik_ortu ?? '-',
            '{pangkat_orangtua}' => $surat->pangkat_ortu ?? '-',
            '{instansi_orangtua}' => $surat->instansi_ortu ?? '-',
            '{alamat_kantor}' => $surat->alamat_kantor_ortu ?? '-',

            // ============================================
            // KOP SURAT - LANGSUNG BASE64
            // ============================================
            '{kop_surat}' => $this->getKopSuratBase64($surat),

            // ============================================
            // TTD - LANGSUNG BASE64
            // ============================================
            '{ttd_elektronik}' => $this->getTTDImageBase64(),
        ];

        // Tambahkan data dari data_tambahan
        foreach ($dataTambahan as $key => $value) {
            if (!is_array($value)) {
                $data['{' . $key . '}'] = $value ?? '-';
            }
        }

        return $data;
    }

    /**
     * Get kop surat dengan Base64
     */
    private function getKopSuratBase64(Surat $surat): string
    {
        $jenisSurat = $surat->jenisSurat;

        if ($jenisSurat && $jenisSurat->kop_surat_path) {
            $paths = [
                public_path($jenisSurat->kop_surat_path),
                storage_path('app/public/' . $jenisSurat->kop_surat_path),
                public_path('storage/' . $jenisSurat->kop_surat_path),
            ];

            foreach ($paths as $path) {
                if (file_exists($path) && filesize($path) > 0) {
                    try {
                        $imageData = file_get_contents($path);
                        $base64 = base64_encode($imageData);
                        $mimeType = mime_content_type($path);

                        Log::info('Kop Base64 length: ' . strlen($base64));

                        return '<img src="data:' . $mimeType . ';base64,' . $base64 . '" style="width:100%; max-width:100%; height:auto;" alt="Kop Surat">';
                    } catch (\Exception $e) {
                        Log::error('Error loading kop surat: ' . $e->getMessage());
                    }
                }
            }
        }

        return $this->getDefaultKopBase64();
    }

    /**
     * Get TTD dengan Base64
     */
    private function getTTDImageBase64(): string
    {
        $paths = [
            public_path('images/ttd_dekan_cap.jpeg'),
            storage_path('app/public/images/ttd_dekan_cap.jpeg'),
            public_path('storage/images/ttd_dekan_cap.jpeg'),
        ];

        foreach ($paths as $path) {
            if (file_exists($path) && filesize($path) > 0) {
                try {
                    $imageData = file_get_contents($path);
                    $base64 = base64_encode($imageData);
                    $mimeType = mime_content_type($path);

                    Log::info('TTD Base64 length: ' . strlen($base64));

                    return '<img src="data:' . $mimeType . ';base64,' . $base64 . '" style="max-width:150px; height:auto; margin-top:5px;" alt="TTD dan Cap">';
                } catch (\Exception $e) {
                    Log::error('Error loading TTD image: ' . $e->getMessage());
                }
            }
        }

        return '<div style="border-top:2px solid #000; width:150px; padding-top:5px; text-align:center; display:inline-block;">
                    <strong>Dekan Fakultas</strong><br>
                    <small>NIP. -</small>
                </div>';
    }

    /**
     * Get default kop surat
     */
    private function getDefaultKopBase64(): string
    {
        $logoPath = public_path('images/logo-unisba.png');
        $logoHtml = '';

        if (file_exists($logoPath)) {
            try {
                $imageData = file_get_contents($logoPath);
                $base64 = base64_encode($imageData);
                $mimeType = mime_content_type($logoPath);
                $logoHtml = '<img src="data:' . $mimeType . ';base64,' . $base64 . '" style="max-height:60px; width:auto;" alt="Logo Unisba">';
            } catch (\Exception $e) {
                Log::error('Error loading logo: ' . $e->getMessage());
            }
        }

        return '
        <div style="text-align:center; border-bottom:3px solid #6f42c1; padding-bottom:10px; margin-bottom:15px;">
            <table style="width:100%; border:none;">
                <tr>
                    <td style="width:80px; text-align:center; vertical-align:middle; border:none;">
                        ' . $logoHtml . '
                    </td>
                    <td style="text-align:center; vertical-align:middle; border:none;">
                        <div style="font-family:Times New Roman, Times, serif;">
                            <h1 style="margin:0; color:#6f42c1; font-size:16pt; font-weight:bold;">UNIVERSITAS ISLAM BANDUNG</h1>
                            <h2 style="margin:0; font-size:12pt; font-weight:normal;">(UNISBA)</h2>
                            <p style="margin:2px 0 0 0; font-size:8pt;">Jl. Tamansari No. 20-22, Bandung 40116</p>
                            <p style="margin:0; font-size:8pt;">Telp. (022) 4203368 | www.unisba.ac.id</p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>';
    }

    /**
     * Default template (fallback)
     */
    private function getDefaultTemplate(): string
    {
        return '
        <div style="font-family: Times New Roman, Times, serif; font-size: 12pt; max-width: 210mm; margin: 0 auto; padding: 10mm;">
            <div style="text-align:center; margin-bottom:20px;">
                {kop_surat}
            </div>
            <div style="text-align:center; margin:20px 0;">
                <strong style="font-size:14pt;">SURAT KETERANGAN AKTIF KULIAH</strong><br>
                <strong>Nomor : {nomor_surat}</strong>
            </div>
            <div style="text-align:justify; line-height:1.6;">
                <p>Yang bertanda tangan di bawah ini:</p>
                <table style="width:100%; border:none;">
                    <tr><td style="width:120px; border:none;">Nama</td><td style="border:none;">: {dekan}</td></tr>
                    <tr><td style="border:none;">NIP</td><td style="border:none;">: {nip_dekan}</td></tr>
                    <tr><td style="border:none;">Jabatan</td><td style="border:none;">: Dekan Fakultas</td></tr>
                </table>
                <p>Menerangkan bahwa mahasiswa:</p>
                <table style="width:100%; border:none;">
                    <tr><td style="width:120px; border:none;">Nama</td><td style="border:none;">: {nama_mahasiswa}</td></tr>
                    <tr><td style="border:none;">NPM</td><td style="border:none;">: {npm}</td></tr>
                    <tr><td style="border:none;">Fakultas</td><td style="border:none;">: {fakultas}</td></tr>
                    <tr><td style="border:none;">Program Studi</td><td style="border:none;">: {prodi}</td></tr>
                    <tr><td style="border:none;">Semester</td><td style="border:none;">: {semester}</td></tr>
                    <tr><td style="border:none;">Alamat</td><td style="border:none;">: {alamat}</td></tr>
                </table>
                <p>Adalah benar-benar mahasiswa aktif Universitas pada semester yang tertera.</p>
                <p>Surat keterangan ini dibuat untuk memenuhi persyaratan administrasi.</p>
                <p>Demikian surat ini dibuat dengan sebenarnya dan dapat dipergunakan sebagaimana mestinya.</p>
            </div>
            <div style="margin-top:50px; text-align:right;">
                <p>Bandung, {tanggal_surat}</p>
                <p>Dekan Fakultas,</p>
                <br><br>
                {ttd_elektronik}
                <p><strong><u>{dekan}</u></strong></p>
                <p>{nip_dekan}</p>
            </div>
        </div>';
    }

    /**
     * Add CSS styles
     */
    private function addStyles(string $html): string
    {
        $styles = '
    <style>
        @page { 
            size: A4; 
            margin: 0; /* Hapus margin untuk kop full */
        }
        
        body { 
            font-family: "Times New Roman", Times, serif; 
            font-size: 12pt; 
            line-height: 1.5; 
            background: white; 
            margin: 0; 
            padding: 0; 
        }
        
        /* ============================================ */
        /* KOP SURAT - FULL WIDTH */
        /* ============================================ */
        .kop-surat {
            margin: 0;
            padding: 0;
            width: 100%;
            position: relative;
        }
        
        .kop-surat img {
            width: 100%;
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0;
        }
        
        /* ============================================ */
        /* KONTEN SURAT - DENGAN MARGIN */
        /* ============================================ */
        .surat-container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 0 20mm 20mm 20mm; /* Top:0, Left/Right/Bottom:20mm */
            background: white;
            position: relative;
        }
        
        /* Jika kop surat sudah di dalam container, atur jarak */
        .surat-content {
            padding-top: 10mm; /* Jarak dari kop ke isi surat */
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        
        td { 
            padding: 3px 0; 
            vertical-align: top; 
            border: none; 
        }
        
        img { 
            max-width: 100%; 
            height: auto; 
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-justify { text-align: justify; }
        .mt-30 { margin-top: 30px; }
        .mb-10 { margin-bottom: 10px; }
        
        .surat-title { 
            font-size: 14pt; 
            font-weight: bold; 
            text-align: center; 
            margin: 10px 0 5px; 
        }
        
        .surat-nomor { 
            font-weight: bold; 
            text-align: center; 
            margin-bottom: 15px; 
        }
        
        .label-col { width: 120px; }
        
        .ttd-area { 
            margin-top: 40px; 
            text-align: right; 
        }
        
        .ttd-image { 
            max-width: 150px; 
            height: auto; 
            margin-top: 5px; 
        }
        
        @media print { 
            body { margin: 0; padding: 0; } 
        }
    </style>
    ';

        return '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Surat</title>' . $styles . '</head><body>' . $html . '</body></html>';
    }
}

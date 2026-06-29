<?php

namespace App\Services;

use App\Models\Surat;
use Illuminate\Support\Facades\Log;

class SuratHtmlGenerator
{
    /**
     * Generate HTML surat dari template yang disimpan di database
     */
    public function generate(Surat $surat): string
    {
        // Load relasi
        $surat->loadMissing(['mahasiswa.prodi.fakultas', 'jenisSurat.kategoriSurat']);

        $mahasiswa = $surat->mahasiswa;
        $jenisSurat = $surat->jenisSurat;

        // Ambil template dari database, jika kosong gunakan default
        $templateContent = $jenisSurat->template_content ?? $this->getDefaultTemplate();

        // Prepare data untuk replace
        $data = $this->prepareData($surat, $mahasiswa);

        // Replace variabel
        $html = strtr($templateContent, $data);

        // Tambahkan kop surat jika template tidak memiliki kop
        if (!strpos($html, '{kop_surat}') && !strpos($html, 'kop-surat')) {
            $kop = $this->getKopSurat($surat);
            if (!empty($kop)) {
                $html = $kop . $html;
            }
        }

        // Tambahkan CSS style jika template tidak memiliki style
        if (!strpos($html, '<style>')) {
            $html = $this->addStyles($html);
        }

        // Minify HTML untuk mempercepat
        $html = $this->minifyHtml($html);

        return $html;
    }

    /**
     * Prepare data untuk replace variabel
     */
    private function prepareData(Surat $surat, $mahasiswa): array
    {
        $dataTambahan = json_decode($surat->data_tambahan, true) ?? [];

        // Data dasar
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

            // Kop Surat & TTD
            '{kop_surat}' => $this->getKopSurat($surat),
            '{ttd_elektronik}' => $this->getTTDImage(),
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
     * Get default template (fallback jika template kosong)
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
     * Add CSS styles to HTML
     */
    private function addStyles(string $html): string
    {
        $styles = '
        <style>
            @page { size: A4; margin: 1.5cm; }
            body { font-family: "Times New Roman", Times, serif; font-size: 12pt; line-height: 1.5; background: white; margin: 0; padding: 0; }
            table { width: 100%; border-collapse: collapse; }
            td { padding: 3px 0; vertical-align: top; border: none; }
            img { max-width: 100%; height: auto; }
            .kop-surat { margin-bottom: 20px; }
            @media print { body { margin: 0; padding: 0; } }
            .surat-container { max-width: 210mm; margin: 0 auto; padding: 20px; background: white; }
        </style>
        ';

        return '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Surat</title>' . $styles . '</head><body><div class="surat-container">' . $html . '</div></body></html>';
    }

    /**
     * Get TTD image
     */
    private function getTTDImage(): string
    {
        $paths = [
            public_path('images/ttd_dekan_cap.jpeg'),
            storage_path('app/public/images/ttd_dekan_cap.jpeg'),
            public_path('storage/images/ttd_dekan_cap.jpeg'),
        ];

        foreach ($paths as $path) {
            if (file_exists($path) && filesize($path) > 0) {
                $relativePath = str_replace(public_path(), '', $path);
                return '<img src="' . $relativePath . '" style="max-width:150px; height:auto; margin-top:5px;" alt="TTD dan Cap">';
            }
        }

        return '<div style="border-top:2px solid #000; width:150px; padding-top:5px; text-align:center; display:inline-block;">
                    <strong>Dekan Fakultas</strong><br>
                    <small>NIP. -</small>
                </div>';
    }

    /**
     * Get kop surat dari jenis surat
     */
    private function getKopSurat(Surat $surat): string
    {
        $jenisSurat = $surat->jenisSurat;

        // Cek apakah ada kop surat di jenis surat
        if ($jenisSurat && $jenisSurat->kop_surat_path) {
            $paths = [
                public_path($jenisSurat->kop_surat_path),
                storage_path('app/public/' . $jenisSurat->kop_surat_path),
                public_path('storage/' . $jenisSurat->kop_surat_path),
            ];

            foreach ($paths as $path) {
                if (file_exists($path) && filesize($path) > 0) {
                    $relativePath = str_replace(public_path(), '', $path);
                    return '<div class="kop-surat"><img src="' . $relativePath . '" style="width:100%; max-width:100%; height:auto;" alt="Kop Surat"></div>';
                }
            }
        }

        return $this->getDefaultKop();
    }

    /**
     * Get default kop surat
     */
    private function getDefaultKop(): string
    {
        $logoPath = public_path('images/logo-unisba.png');
        $logoHtml = '';

        if (file_exists($logoPath)) {
            $relativePath = str_replace(public_path(), '', $logoPath);
            $logoHtml = '<img src="' . $relativePath . '" style="max-height:60px; width:auto;" alt="Logo Unisba">';
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
     * Minify HTML
     */
    private function minifyHtml(string $html): string
    {
        // Hapus komentar
        $html = preg_replace('/<!--.*?-->/s', '', $html);

        // Hapus whitespace berlebih
        $html = preg_replace('/\s+/', ' ', $html);

        // Hapus spasi di antara tag
        $html = str_replace('> <', '><', $html);

        return trim($html);
    }
}

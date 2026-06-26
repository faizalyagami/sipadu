<?php
// app/Models/Surat.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    protected $fillable = [
        'mahasiswa_id',
        'jenis_surat_id',
        'approved_by',
        'content',
        'keperluan',
        'status',
        'alasan_reject',
        'approved_at',
        'ttd_elektronik',
        'ttd_nama',
        'ttd_nip',
        'ttd_jabatan',
        'nomor_surat',
        'nama_ortu',
        'nik_ortu',
        'pangkat_ortu',
        'instansi_ortu',
        'alamat_kantor_ortu',
        'bukti_pembayaran',
        'tipe_pengajuan',
        'nama_kelompok',
        'file_ktm',
        'file_pendukung',
        'data_tambahan',
        'nama_ortu',
        'nik_ortu',
        'pangkat_ortu',
        'instansi_ortu',
        'alamat_kantor_ortu',
        'pekerjaan_ortu',
        'no_hp_ortu',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'data_tambahan' => 'array'
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function jenisSurat()
    {
        return $this->belongsTo(JenisSurat::class, 'jenis_surat_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Generate nomor surat otomatis
     */
    public static function generateNomorSurat()
    {
        $tahun = date('Y');
        $bulan = date('m');
        $bulanRomawi = [
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
        $lastSurat = self::whereYear('created_at', $tahun)->count() + 1;
        $nomor = str_pad($lastSurat, 3, '0', STR_PAD_LEFT);
        return "{$nomor}/M.10/Dek.Psi-k/{$bulanRomawi[(int)$bulan]}/{$tahun}";
    }

    /**
     * Approve surat dengan TTD + Cap dari gambar
     */
    public function approveWithTTD($userId)
    {
        $this->status = 'approved';
        $this->approved_by = $userId;
        $this->approved_at = now();
        $this->nomor_surat = self::generateNomorSurat();
        $this->ttd_nama = 'Dr. Oki Mardiawan, M.Psi., Psikolog.';
        $this->ttd_nip = 'D.07.0.464';
        $this->ttd_jabatan = 'Wakil Dekan Bidang Pembelajaran dan Kemahasiswaan';
        $this->ttd_elektronik = 'ttd_dekan_cap.jpeg';
        $this->save();
    }

    /**
     * Generate HTML surat lengkap dengan TTD + Cap
     */
    public function generateSuratHtml()
    {
        try {
            $template = $this->jenisSurat->template_content ?? $this->getDefaultTemplate();
            $mahasiswa = $this->mahasiswa;

            if (!$mahasiswa) {
                throw new \Exception('Data mahasiswa tidak ditemukan');
            }

            // Data dasar untuk template
            $data = [
                '{nomor_surat}' => $this->nomor_surat ?? $this->id,
                '{tanggal_surat}' => $this->approved_at ? $this->approved_at->format('d F Y') : date('d F Y'),
                '{nama_mahasiswa}' => $mahasiswa->nama_lengkap ?? '-',
                '{npm}' => $mahasiswa->npm ?? '-',
                '{tempat_lahir}' => $mahasiswa->tempat_lahir ?? '-',
                '{tanggal_lahir}' => $mahasiswa->tanggal_lahir ? date('d F Y', strtotime($mahasiswa->tanggal_lahir)) : '-',
                '{alamat}' => $mahasiswa->alamat ?? '-',
                '{fakultas}' => $mahasiswa->prodi->fakultas->nama_fakultas ?? '-',
                '{prodi}' => $mahasiswa->prodi->nama_prodi ?? '-',
                '{jenjang}' => $mahasiswa->prodi->jenjang ?? 'S1',
                '{ipk}' => $mahasiswa->ipk ?? '-',
                '{semester}' => $this->getSemester($mahasiswa->tanggal_masuk),
                '{keperluan}' => $this->keperluan ?? '-',
                '{ttd_nama}' => $this->ttd_nama ?? 'Dekan Fakultas',
                '{ttd_nip}' => $this->ttd_nip ?? '-',
                '{ttd_jabatan}' => $this->ttd_jabatan ?? 'Dekan',
                '{ttd_elektronik}' => $this->getTTDImage(),
            ];

            // Tambahkan data dari field tambahan
            $extraFields = [
                'nama_ortu',
                'nik_ortu',
                'pangkat_ortu',
                'instansi_ortu',
                'alamat_kantor_ortu',
                'tipe_pengajuan',
                'nama_kelompok'
            ];

            foreach ($extraFields as $field) {
                $value = $this->$field ?? '-';
                if (is_array($value)) {
                    $value = json_encode($value);
                }
                $data['{' . $field . '}'] = $value;
            }

            // Tambahkan data dari data_tambahan (JSON)
            $dataTambahan = json_decode($this->data_tambahan, true) ?? [];
            foreach ($dataTambahan as $key => $value) {
                if (is_array($value)) {
                    $value = json_encode($value);
                }
                $data['{' . $key . '}'] = $value;
            }

            $html = str_replace(array_keys($data), array_values($data), $template);

            // Jika template tidak memiliki kop surat, tambahkan kop surat default
            if (!strpos($html, 'KOP SURAT') && !strpos($html, 'kop-surat')) {
                $html = $this->getDefaultKopSurat() . $html;
            }

            return $this->addPrintStyles($html);
        } catch (\Exception $e) {
            \Log::error('Error generating surat HTML: ' . $e->getMessage());
            return '<div style="padding: 20px; color: red;">Error generating surat: ' . $e->getMessage() . '</div>';
        }
    }

    /**
     * Get TTD + Cap image in base64 format
     */
    private function getTTDImage()
    {
        // Coba dari berbagai lokasi
        $paths = [
            public_path('images/ttd_dekan_cap.jpeg'),
            storage_path('app/public/images/ttd_dekan_cap.jpeg'),
            public_path('storage/images/ttd_dekan_cap.jpeg'),
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                try {
                    $imageData = base64_encode(file_get_contents($path));
                    return '<img src="data:image/jpeg;base64,' . $imageData . '" style="max-width: 200px; height: auto; margin-top: 10px;" alt="TTD dan Cap">';
                } catch (\Exception $e) {
                    \Log::error('Error loading TTD image: ' . $e->getMessage());
                }
            }
        }

        // Fallback jika gambar tidak ditemukan
        return '<div style="border-top: 2px solid #000; width: 200px; margin-top: 10px; padding-top: 10px; text-align: center;">
                <strong>' . $this->ttd_nama . '</strong><br>
                <small>NIP. ' . $this->ttd_nip . '</small>
            </div>';
    }

    /**
     * Get semester berdasarkan tanggal masuk
     */
    private function getSemester($tanggalMasuk)
    {
        if (!$tanggalMasuk) return '-';
        $tahunMasuk = date('Y', strtotime($tanggalMasuk));
        $tahunSekarang = date('Y');
        $selisihTahun = $tahunSekarang - $tahunMasuk;
        return ($selisihTahun * 2) + 1;
    }

    /**
     * Default template surat
     */
    private function getDefaultTemplate()
    {
        return '
        <div style="font-family: Times New Roman, Times, serif; max-width: 210mm; margin: 0 auto; padding: 10mm;">
            <div style="text-align: center; border-bottom: 3px solid #6f42c1; padding-bottom: 15px; margin-bottom: 20px;">
                <h1 style="margin: 0; color: #6f42c1; font-size: 20pt;">UNIVERSITAS ISLAM BANDUNG</h1>
                <h2 style="margin: 5px 0; font-size: 14pt;">(UNISBA)</h2>
                <p style="margin: 0; font-size: 10pt;">Jl. Tamansari No. 20-22, Bandung 40116</p>
                <p style="margin: 0; font-size: 10pt;">Telp. (022) 4203368 | Email: rektorat@unisba.ac.id</p>
            </div>
            
            <div style="text-align: center; margin: 20px 0;">
                <strong style="font-size: 14pt;">SURAT KETERANGAN</strong>
            </div>
            
            <div style="text-align: center; margin: 10px 0;">
                <strong>Nomor : {nomor_surat}</strong>
            </div>
            
            <div style="text-align: justify; line-height: 1.6;">
                <p>Yang bertanda tangan di bawah ini:</p>
                <table style="width: 100%; margin: 10px 0;">
                    <tr><td style="width: 120px;">Nama</td><td>: {ttd_nama}</td></tr>
                    <tr><td>NIP</td><td>: {ttd_nip}</td></tr>
                    <tr><td>Jabatan</td><td>: {ttd_jabatan}</td></tr>
                </table>
                <p>Menerangkan bahwa:</p>
                <table style="width: 100%; margin: 10px 0;">
                    <tr><td style="width: 120px;">Nama</td><td>: {nama_mahasiswa}</td></tr>
                    <tr><td>NPM</td><td>: {npm}</td></tr>
                    <tr><td>Fakultas</td><td>: {fakultas}</td></tr>
                    <tr><td>Program Studi</td><td>: {prodi}</td></tr>
                    <tr><td>Semester</td><td>: {semester}</td></tr>
                </table>
                <p>{keperluan}</p>
            </div>
            
            <div style="margin-top: 50px; text-align: right;">
                <p>Bandung, {tanggal_surat}</p>
                <p>{ttd_jabatan},</p>
                <br><br>
                {ttd_elektronik}
                <p><strong><u>{ttd_nama}</u></strong></p>
                <p>NIP. {ttd_nip}</p>
            </div>
        </div>';
    }

    /**
     * Add print styles to HTML
     */
    private function addPrintStyles($html)
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Surat Keterangan</title>
            <style>
                @page { size: A4; margin: 2cm; }
                body { font-family: "Times New Roman", Times, serif; font-size: 12pt; line-height: 1.5; }
                table { width: 100%; border-collapse: collapse; }
                td { padding: 3px 0; vertical-align: top; }
                @media print { body { margin: 0; } }
            </style>
        </head>
        <body>' . $html . '</body>
        </html>';
    }

    public function getContentHtmlAttribute()
    {
        // Hapus tag HTML yang tidak perlu dan beri style
        $content = $this->content;

        // Jika content kosong, tampilkan pesan
        if (empty($content)) {
            return '<p class="text-muted text-center">Tidak ada konten surat</p>';
        }

        return $content;
    }
}

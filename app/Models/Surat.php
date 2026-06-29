<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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
        'tipe_pengajuan',
        'nama_kelompok',
        'file_ktm',
        'file_pendukung',
        'data_tambahan',
        'pdf_path',
        'html_content',
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
        $bulan = date('n');
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
        $count = self::whereYear('created_at', $tahun)->whereMonth('created_at', $bulan)->count() + 1;
        $nomor = str_pad($count, 3, '0', STR_PAD_LEFT);
        return "{$nomor}/M.10/Dek.Psi-k/{$bulanRomawi[$bulan]}/{$tahun}";
    }

    /**
     * Approve surat dengan TTD + nomor surat
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
     * Get semester dari data_tambahan atau hitung otomatis
     */
    public function getSemesterAttribute()
    {
        $dataTambahan = json_decode($this->data_tambahan, true) ?? [];

        if (isset($dataTambahan['semester'])) {
            return $dataTambahan['semester'];
        }

        // Fallback: hitung dari tanggal masuk
        if ($this->mahasiswa && $this->mahasiswa->tanggal_masuk) {
            return $this->calculateSemester($this->mahasiswa->tanggal_masuk);
        }

        return '-';
    }

    /**
     * Calculate semester from entry date
     */
    private function calculateSemester($tanggalMasuk)
    {
        if (!$tanggalMasuk) return '-';

        try {
            $tahunMasuk = date('Y', strtotime($tanggalMasuk));
            $tahunSekarang = date('Y');
            $selisihTahun = $tahunSekarang - $tahunMasuk;
            $semester = ($selisihTahun * 2) + 1;
            return $semester > 14 ? 14 : $semester;
        } catch (\Exception $e) {
            return '-';
        }
    }

    public function getNamaOrtuAttribute($value)
    {
        return $value ?? '-';
    }

    public function getNikOrtuAttribute($value)
    {
        return $value ?? '-';
    }

    public function getPangkatOrtuAttribute($value)
    {
        return $value ?? '-';
    }

    public function getInstansiOrtuAttribute($value)
    {
        return $value ?? '-';
    }

    public function getAlamatKantorOrtuAttribute($value)
    {
        return $value ?? '-';
    }
}

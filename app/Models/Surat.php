<?php
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
        'nomor_surat'
    ];

    protected $dates = ['approved_at'];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function jenisSurat()
    {
        return $this->belongsTo(JenisSurat::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public static function generateNomorSurat()
    {
        $tahun = date('Y');
        $bulan = date('m');
        $lastSurat = self::whereYear('created_at', $tahun)->count() + 1;
        $nomor = str_pad($lastSurat, 3, '0', STR_PAD_LEFT);
        return "{$nomor}/M.10/Dek.Psi-k/{$bulan}/{$tahun}";
    }

    // Generate TTD Elektronik
    public function generateTTD()
    {
        $this->ttd_elektronik = 'data:image/png;base64,' . base64_encode($this->createSignatureImage());
        $this->ttd_nama = 'Dr. Oki Mardiawan, M.Psi., Psikolog.';
        $this->ttd_nip = 'D.07.0.464';
        $this->ttd_jabatan = 'Wakil Dekan Bidang Pembelajaran dan Kemahasiswaan';
        $this->nomor_surat = self::generateNomorSurat();
        $this->save();
    }

    // Buat gambar tanda tangan sederhana
    private function createSignatureImage()
    {
        $width = 300;
        $height = 100;
        $image = imagecreate($width, $height);
        
        // Background putih
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        $purple = imagecolorallocate($image, 111, 66, 193);
        
        // Text tanda tangan
        $text = "TTD Elektronik\n" . $this->ttd_nama . "\nNIP. " . $this->ttd_nip;
        
        // Simpan sebagai PNG
        ob_start();
        imagepng($image);
        $imageData = ob_get_contents();
        ob_end_clean();
        imagedestroy($image);
        
        return $imageData;
    }
}
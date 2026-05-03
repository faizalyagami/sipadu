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

    public function approve($userId)
    {
        $this->status = 'approved';
        $this->approved_by = $userId;
        $this->approved_at = now();
        $this->ttd_elektronik = $this->generateTTD();
        $this->save();
    }

    public function reject($userId, $reason)
    {
        $this->status = 'rejected';
        $this->approved_by = $userId;
        $this->approved_at = now();
        $this->alasan_reject = $reason;
        $this->save();
    }

    private function generateTTD()
    {
        return base64_encode("TTD_Dekan_" . $this->id . "_" . now()->timestamp);
    }
}
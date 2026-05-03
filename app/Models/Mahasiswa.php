<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $fillable = [
        'user_id',
        'prodi_id',
        'npm',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'no_hp',
        'no_wa',
        'email_pribadi',
        'tanggal_masuk',
        'status_mahasiswa',
        'ipk',
        'sks_tempuh',
        'dosen_wali',
        'dosen_wali_nik',
        'jenis_kelamin'
    ];

    protected $dates = ['tanggal_lahir', 'tanggal_masuk'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    public function surats()
    {
        return $this->hasMany(Surat::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    protected $fillable = [
        'fakultas_id',
        'nama_prodi',
        'jenjang',
        'kode_prodi',
        'akreditasi',
        'kajur'
    ];

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class);
    }

    public function mahasiswas()
    {
        return $this->hasMany(Mahasiswa::class);
    }
}
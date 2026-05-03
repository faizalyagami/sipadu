<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fakultas extends Model
{
    protected $table = 'fakultas';
    
    protected $fillable = [
        'nama_fakultas',
        'kode_fakultas',
        'dekan',
        'nip_dekan'
    ];

    public function prodis()
    {
        return $this->hasMany(Prodi::class);
    }
}
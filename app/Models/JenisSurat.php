<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisSurat extends Model
{
    protected $table = 'jenis_surats';
    
    protected $fillable = [
        'nama_surat',
        'kategori_surat_id',
        'kode_surat',
        'deskripsi',
        'is_active',
        'processing_time',
        'syarat_surat',
        'template_content',
        'logo_path',
        'kop_surat_path'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function kategoriSurat()
    {
        return $this->belongsTo(KategoriSurat::class, 'kategori_surat_id');
    }

    public static function generateNomorSurat($kode = 'M.10/Dek.Psi-k')
    {
        $bulanRomawi = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 
                        7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];
        
        $bulan = $bulanRomawi[now()->month];
        $tahun = now()->year;
        $count = Surat::whereYear('created_at', $tahun)->count() + 1;
        $no = str_pad($count, 3, '0', STR_PAD_LEFT);
        
        return "{$no}/{$kode}/{$bulan}/{$tahun}";
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }

    public function getKopUrlAttribute()
    {
        return $this->kop_surat_path ? asset('storage/' . $this->kop_surat_path) : null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function surats()
    {
        return $this->hasMany(Surat::class);
    }

    public function templates()
    {
        return $this->hasMany(TemplateSurat::class);
    }

    public function activeTemplate()
    {
        return $this->hasOne(TemplateSurat::class)->where('status', 'active');
    }
}
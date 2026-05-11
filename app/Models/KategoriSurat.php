<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class KategoriSurat extends Model
{
    use HasFactory;
    protected $table = 'kategori_surats';
    
    protected $fillable = [
        'nama_kategori',
        'slug',
        'deskripsi',
        'is_active',
    ];
    
    protected $casts = [
        'is_active' => 'boolean'
    ];
    
    // Generate slug otomatis
    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($kategori) {
            $kategori->slug = Str::slug($kategori->nama_kategori);
        });
        
        static::updating(function ($kategori) {
            $kategori->slug = Str::slug($kategori->nama_kategori);
        });
    }
    
    // Relasi ke JenisSurat
    public function jenisSurats()
    {
        return $this->hasMany(JenisSurat::class, 'kategori_surat_id');
    }
    
    // Scope untuk kategori aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

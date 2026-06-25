<?php
// app/Models/JenisSurat.php

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
        'variable_fields',
        'variable_options',
        'deskripsi_template',
        'logo_path',
        'kop_surat_path'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'variable_fields' => 'array',
        'variable_options' => 'array'
    ];

    public function kategoriSurat()
    {
        return $this->belongsTo(KategoriSurat::class, 'kategori_surat_id');
    }

    public function surats()
    {
        return $this->hasMany(Surat::class);
    }

    /**
     * Generate konten surat dengan replace variabel
     */
    public function generateSuratContent($data, $mahasiswa)
    {
        $template = $this->template_content ?? $this->getDefaultTemplate();

        // Data dasar mahasiswa
        $replaceData = [
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
            '{tanggal_surat}' => now()->format('d F Y'),
            '{keperluan}' => $data['keperluan'] ?? '',
        ];

        // Data dari variabel tambahan
        $variables = $this->getVariableFields();
        foreach ($variables as $variable) {
            $key = '{' . $variable['name'] . '}';
            $value = $data[$variable['name']] ?? $variable['default'] ?? '';
            $replaceData[$key] = $value;
        }

        // Replace semua variabel di template
        $content = str_replace(array_keys($replaceData), array_values($replaceData), $template);

        return $content;
    }

    /**
     * Get variable fields
     */
    public function getVariableFields()
    {
        return $this->variable_fields ?? [];
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
     * Default template
     */
    private function getDefaultTemplate()
    {
        return '<div style="font-family: Times New Roman, Times, serif; padding: 20px;">
            <h2 style="text-align: center;">SURAT KETERANGAN</h2>
            <p>Yang bertanda tangan di bawah ini menerangkan bahwa:</p>
            <p>Nama: {nama_mahasiswa}<br>NPM: {npm}<br>Fakultas: {fakultas}</p>
            <p>Adalah benar mahasiswa aktif Universitas Islam Bandung.</p>
            <p>Surat ini dibuat untuk {keperluan}.</p>
        </div>';
    }
}

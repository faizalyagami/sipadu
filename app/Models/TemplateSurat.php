<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateSurat extends Model
{
    protected $fillable = [
        'jenis_surat_id',
        'nama_template',
        'template_html',
        'template_docx',
        'variables',
        'status',
        'version',
        'keterangan'
    ];

    protected $casts = [
        'variables' => 'array'
    ];

    public function jenisSurat() {
        return $this->belongsTo(JenisSurat::class);
    }

    public function render(array $data) {
        $html = $this->template_html;

        foreach ($data as $key => $value) {
            $html = str_replace('{' . $key . '}', $value, $html);
        }
        return $html;
    }

    public function getAvailableVariables() {
        preg_match_all('/\{([^}]+)\}/', $this->template_html, $matches);
        return $matches[1] ?? [];
    }
}

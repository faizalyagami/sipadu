<?php

namespace App\Services;

class SuratCssGenerator
{
    public static function generateCss(): string
    {
        return '
        <style>
            /* HANYA UNTUK LAYOUT KOP SURAT */
            @page { 
                size: A4; 
                margin: 0;
            }
            
            body { 
                font-family: "Times New Roman", Times, serif; 
                font-size: 12pt; 
                background: white; 
                margin: 0; 
                padding: 0; 
            }
            
            .kop-surat { 
                margin: 0; 
                padding: 0; 
                width: 100%; 
            }
            
            .kop-surat img { 
                width: 100%; 
                max-width: 100%; 
                height: auto; 
                display: block; 
                margin: 0; 
                padding: 0; 
            }
            
            /* TIDAK ADA FORMAT PARAGRAF DI SINI */
            /* SEMUA FORMAT DARI CKEDITOR */
        </style>';
    }
}

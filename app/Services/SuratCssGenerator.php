<?php

namespace App\Services;

class SuratCssGenerator
{

    public static function generateCss(): string
    {
        return '
        <style>
            /* ============================================ */
            /* PAGE SETUP - Layout Halaman */
            /* ============================================ */
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
            
            /* ============================================ */
            /* KOP SURAT - Full Width */
            /* ============================================ */
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
            }
            
            /* ============================================ */
            /* KONTEN SURAT */
            /* ============================================ */
            .surat-container {
                max-width: 210mm;
                margin: 0 auto;
                background: white;
            }
            
            .surat-content {
                padding-top: 5mm;
            }
            
            /* ============================================ */
            /* TABLE */
            /* ============================================ */
            table { 
                width: 100%; 
                border-collapse: collapse; 
            }
            
            td { 
                padding: 3px 0; 
                vertical-align: top; 
                border: none; 
            }
            
            .label-col { 
                width: 120px; 
            }
            
            /* ============================================ */
            /* TEXT ALIGN - Utility Classes */
            /* ============================================ */
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .text-left { text-align: left; }
            .text-justify { text-align: justify; }
            
            /* ============================================ */
            /* SURAT TITLE & NOMOR */
            /* ============================================ */
            .surat-title { 
                font-size: 14pt; 
                font-weight: bold; 
                text-align: center; 
                margin: 8px 0 4px; 
            }
            
            .surat-nomor { 
                font-weight: bold; 
                text-align: center; 
                margin-bottom: 12px; 
            }
            
            /* ============================================ */
            /* TTD */
            /* ============================================ */
            .ttd-area { 
                margin-top: 35px; 
                text-align: right; 
            }
            
            .ttd-image { 
                max-width: 150px; 
                height: auto; 
                margin-top: 5px; 
            }
            
            /* ============================================ */
            /* IMAGE */
            /* ============================================ */
            img { 
                max-width: 100%; 
                height: auto; 
            }
            
            /* ============================================ */
            /* PRINT */
            /* ============================================ */
            @media print { 
                body { margin: 0; padding: 0; } 
            }
            
        </style>';
    }
}

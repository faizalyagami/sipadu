<?php

namespace App\Traits;

use App\Services\SuratHtmlGenerator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

trait PDFGenerationTrait
{

    protected function generateAndDownloadPDF($surat)
    {
        $cacheKey = 'surat_pdf_' . $surat->id . '_' . md5($surat->updated_at);
        $cacheDir = storage_path('app/cache/pdf');
        $cachePath = $cacheDir . '/' . $cacheKey . '.pdf';

        if (!file_exists($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        // Cache hit
        if (file_exists($cachePath) && (time() - filemtime($cachePath) < 7200)) {
            Log::info('PDF cache hit: ' . $surat->id);
            $filename = 'Surat_' . ($surat->nomor_surat ?? $surat->id) . '.pdf';
            return response()->file($cachePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        }

        // Generate HTML
        $html = app(SuratHtmlGenerator::class)->generate($surat);

        // Generate PDF
        $pdf = Pdf::loadHTML($html)
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'dpi' => 96,
                'defaultFont' => 'Times New Roman',
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => false,
            ]);

        $pdf->save($cachePath);

        Log::info('PDF generated: ' . $cachePath);

        $filename = 'Surat_' . ($surat->nomor_surat ?? $surat->id) . '.pdf';
        return response()->download($cachePath, $filename, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'public, max-age=7200',
        ]);
    }
}

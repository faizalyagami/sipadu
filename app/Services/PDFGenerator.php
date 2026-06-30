<?php

namespace App\Services;

use App\Models\Surat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PDFGenerator
{
    /**
     * Generate dan simpan PDF untuk surat yang sudah approved
     */
    public function generateAndSave(Surat $surat): string
    {
        try {
            // Generate HTML
            $htmlGenerator = app(SuratHtmlGenerator::class);
            $html = $htmlGenerator->generate($surat);

            // Debug: Simpan HTML untuk debugging
            $debugPath = storage_path('app/debug.html');
            file_put_contents($debugPath, $html);
            Log::info('HTML debug saved to: ' . $debugPath);

            // Generate PDF dengan optimasi
            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => true,
                'defaultFont' => 'Helvetica',
                'dpi' => 72,
                'logErrors' => true,
                'enable_remote' => false,
                'font_cache' => storage_path('fonts/'),
                'tempDir' => storage_path('temp/'),
                'chroot' => public_path(),
            ]);

            // Nama file
            $filename = 'surat_' . ($surat->nomor_surat ?? $surat->id) . '_' . time() . '.pdf';
            $path = 'surat/' . $filename;

            // Buat folder jika belum ada
            $fullPath = storage_path('app/public/' . $path);
            $dir = dirname($fullPath);
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            // Simpan PDF
            $pdfContent = $pdf->output();
            Log::info('PDF size: ' . strlen($pdfContent) . ' bytes');

            if (strlen($pdfContent) < 100) {
                throw new \Exception('PDF content is too small, likely empty');
            }

            file_put_contents($fullPath, $pdfContent);
            Log::info('Saved PDF size: ' . filesize($fullPath) . ' bytes');

            // Verifikasi file tersimpan
            if (!file_exists($fullPath) || filesize($fullPath) < 100) {
                throw new \Exception('PDF file not saved properly or empty');
            }

            // Update surat dengan path
            $surat->pdf_path = $path;
            $surat->save();

            Log::info('PDF generated and saved for surat ID: ' . $surat->id . ' at: ' . $path);

            return $path;
        } catch (\Exception $e) {
            Log::error('Error generating PDF: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Regenerate PDF untuk surat yang sudah ada
     */
    public function regenerate(Surat $surat): string
    {
        if ($surat->pdf_path && Storage::disk('public')->exists($surat->pdf_path)) {
            Storage::disk('public')->delete($surat->pdf_path);
        }

        return $this->generateAndSave($surat);
    }
}

<?php

namespace App\Services;

use App\Models\Surat;
use App\Services\SuratHtmlGenerator;
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

            // Generate PDF
            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => false,
                'defaultFont' => 'Times New Roman   ',
                'dpi' => 96,
                'logErrors' => true,
                'enable_remote' => true,
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

            // Simpan PDF - PASTIKAN MENYIMPAN DENGAN BENAR
            $pdfContent = $pdf->output();
            file_put_contents($fullPath, $pdfContent);

            // Verifikasi file tersimpan
            if (!file_exists($fullPath) || filesize($fullPath) < 100) {
                throw new \Exception('PDF file not saved properly or empty');
            }

            // Update surat dengan path
            $surat->pdf_path = $path;
            $surat->save();

            Log::info('PDF generated and saved for surat ID: ' . $surat->id . ' at: ' . $path . ' Size: ' . filesize($fullPath));

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
        // Hapus file lama jika ada
        if ($surat->pdf_path && Storage::disk('public')->exists($surat->pdf_path)) {
            Storage::disk('public')->delete($surat->pdf_path);
        }

        return $this->generateAndSave($surat);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Services\PDFGenerator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DebugController extends Controller
{
    /**
     * Cek status PDF
     */
    public function checkPdf($id)
    {
        try {
            $surat = Surat::findOrFail($id);

            $data = [
                'surat_id' => $surat->id,
                'status' => $surat->status,
                'nomor_surat' => $surat->nomor_surat,
                'pdf_path' => $surat->pdf_path,
                'exists_in_db' => !empty($surat->pdf_path),
                'storage_exists' => $surat->pdf_path ? Storage::disk('public')->exists($surat->pdf_path) : false,
                'full_path' => $surat->pdf_path ? storage_path('app/public/' . $surat->pdf_path) : null,
                'file_exists' => $surat->pdf_path ? file_exists(storage_path('app/public/' . $surat->pdf_path)) : false,
                'file_size' => $surat->pdf_path && file_exists(storage_path('app/public/' . $surat->pdf_path)) ? filesize(storage_path('app/public/' . $surat->pdf_path)) : 0,
            ];

            // Jika file ada, cek header PDF
            if ($data['file_exists'] && $data['file_size'] > 0) {
                $fullPath = storage_path('app/public/' . $surat->pdf_path);
                $content = file_get_contents($fullPath, false, null, 0, 50);
                $data['pdf_header'] = substr($content, 0, 50);
                $data['is_pdf'] = substr($content, 0, 4) === '%PDF';
                $data['pdf_valid'] = $data['is_pdf'] && $data['file_size'] > 100;
            }

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Regenerate PDF
     */
    public function regeneratePdf($id)
    {
        try {
            $surat = Surat::findOrFail($id);

            // Generate ulang PDF
            $generator = new PDFGenerator();
            $path = $generator->generateAndSave($surat);

            // Refresh surat
            $surat->refresh();

            return response()->json([
                'success' => true,
                'message' => 'PDF regenerated successfully',
                'path' => $path,
                'full_path' => storage_path('app/public/' . $path),
                'file_exists' => file_exists(storage_path('app/public/' . $path)),
                'file_size' => file_exists(storage_path('app/public/' . $path)) ? filesize(storage_path('app/public/' . $path)) : 0,
                'pdf_path' => $surat->pdf_path,
            ]);
        } catch (\Exception $e) {
            Log::error('Regenerate PDF error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download PDF untuk debug
     */
    public function downloadPdf($id)
    {
        try {
            $surat = Surat::findOrFail($id);

            if (!$surat->pdf_path || !Storage::disk('public')->exists($surat->pdf_path)) {
                return response()->json(['error' => 'PDF not found'], 404);
            }

            $fullPath = storage_path('app/public/' . $surat->pdf_path);
            $filename = 'debug_surat_' . $surat->id . '.pdf';

            return response()->download($fullPath, $filename, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test PDF generation dengan HTML sederhana
     */
    public function testPdf()
    {
        try {
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Test PDF</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 40px; }
                    h1 { color: #6f42c1; }
                    .container { max-width: 600px; margin: 0 auto; }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>Test PDF Generation</h1>
                    <p>This is a test PDF generated at ' . date('Y-m-d H:i:s') . '</p>
                    <p>If you can see this, DomPDF is working correctly.</p>
                </div>
            </body>
            </html>';

            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'defaultFont' => 'Helvetica',
                'dpi' => 72,
                'logErrors' => true,
                'enable_remote' => true,
            ]);

            return $pdf->download('test.pdf');
        } catch (\Exception $e) {
            Log::error('Test PDF error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

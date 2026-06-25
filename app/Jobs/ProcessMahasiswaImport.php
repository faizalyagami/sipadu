<?php

namespace App\Jobs;

use App\Imports\MahasiswaImport;
use App\Models\ImportProgress;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProcessMahasiswaImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $batchId;

    public $timeout = 3600;
    public $tries = 3;

    public function __construct($filePath, $batchId)
    {
        $this->filePath = $filePath;
        $this->batchId = $batchId;
    }

    public function handle()
    {
        try {
            Log::info("Import dimulai untuk batch: " . $this->batchId);

            if (!Storage::exists($this->filePath)) {
                throw new \Exception("File tidak ditemukan: " . $this->filePath);
            }

            $fullPath = Storage::path($this->filePath);
            Log::info('File Path: ' . $fullPath);

            // Baca total rows dari file Excel
            try {
                $spreadsheet = IOFactory::load($fullPath);
                $worksheet = $spreadsheet->getActiveSheet();
                $totalRows = $worksheet->getHighestRow() - 1; // minus header

                Log::info('Total rows ditemukan: ' . $totalRows);

                // Update total rows
                ImportProgress::where('batch_id', $this->batchId)->update([
                    'total_rows' => max(0, $totalRows),
                    'status' => 'processing'
                ]);

                Log::info('Progress updated dengan total_rows: ' . $totalRows);
            } catch (\Throwable $e) {
                Log::error("Gagal membaca Excel: " . $e->getMessage());
                ImportProgress::where('batch_id', $this->batchId)->update([
                    'total_rows' => 0
                ]);
                // Jangan throw, lanjutkan proses
            }

            // Proses import
            Log::info("Memulai proses import data...");
            $import = new MahasiswaImport($this->batchId);
            Excel::import($import, $fullPath);

            // Ambil hasil import
            $failedRows = method_exists($import, 'getFailedRows') ? $import->getFailedRows() : [];

            // Update status selesai
            $progress = ImportProgress::where('batch_id', $this->batchId)->first();
            if ($progress) {
                Log::info("Import selesai untuk batch: " . $this->batchId, [
                    'success' => $progress->success_rows ?? 0,
                    'failed' => $progress->failed_rows ?? 0,
                    'total' => $progress->total_rows ?? 0
                ]);

                $progress->update([
                    'status' => 'completed',
                    'errors' => $failedRows
                ]);
            }

            // Hapus file temporary
            if (Storage::exists($this->filePath)) {
                Storage::delete($this->filePath);
                Log::info("File temporary dihapus: " . $this->filePath);
            }

            Log::info("Import selesai untuk batch: " . $this->batchId);
        } catch (\Exception $e) {
            Log::error('Import gagal untuk batch ' . $this->batchId . ': ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            ImportProgress::where('batch_id', $this->batchId)->update([
                'status' => 'failed',
                'errors' => [['message' => $e->getMessage()]]
            ]);

            if (Storage::exists($this->filePath)) {
                Storage::delete($this->filePath);
            }

            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('Job gagal untuk batch ' . $this->batchId . ': ' . $exception->getMessage());

        ImportProgress::where('batch_id', $this->batchId)->update([
            'status' => 'failed',
            'errors' => [['message' => 'Job gagal: ' . $exception->getMessage()]]
        ]);

        if (Storage::exists($this->filePath)) {
            Storage::delete($this->filePath);
        }
    }
}

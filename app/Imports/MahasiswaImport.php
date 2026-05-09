<?php
namespace App\Imports;

use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\ImportProgress;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class MahasiswaImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    protected $batchId;
    protected $failedRows = [];

    public function __construct($batchId)
    {
        $this->batchId = $batchId;
    }

    public function batchSize(): int
    {
        return 50;
    }

    public function model(array $row)
    {
        try {
            // Ambil semua data - sesuaikan dengan kolom di Excel
            $npm = $this->getValue($row, ['npm', 'nim']);
            $nikDosenWali = $this->getValue($row, ['nik_dosen_wali', 'nik_dosen', 'nik_wali', 'dosen_wali_nik']);
            $dosenWali = $this->getValue($row, ['dosen_wali', 'nama_dosen_wali', 'dosen']);
            $namaLengkap = $this->getValue($row, ['nama_lengkap', 'nama', 'nama_mahasiswa']);
            $email = $this->getValue($row, ['email']);
            $tempatLahir = $this->getValue($row, ['tempat_lahir']);
            $tanggalLahir = $this->getValue($row, ['tanggal_lahir', 'tgl_lahir']);
            $alamat = $this->getValue($row, ['alamat']);
            $noHp = $this->getValue($row, ['no_hp', 'no_telepon']);
            $namaFakultas = $this->getValue($row, ['nama_fakultas', 'fakultas']);
            $namaProdi = $this->getValue($row, ['program_studi', 'prodi']);
            $jenjang = $this->getValue($row, ['jenjang', 'jenjang_s1_s2_s3_profesi']);
            $status = $this->getValue($row, ['status', 'status_aktif_cuti_lulus']);
            $tanggalMasuk = $this->getValue($row, ['tanggal_masuk', 'tgl_masuk']);
            $ipk = $this->getValue($row, ['ipk']);
            $jenisKelamin = $this->getValue($row, ['jenis_kelamin', 'jk', 'gender']);
            $sksTempuh = $this->getValue($row, ['sks_tempuh', 'sks']);

            // Log untuk debugging
            Log::info("Processing row: NPM={$npm}, NIK Dosen={$nikDosenWali}, Dosen Wali={$dosenWali}");

            // Skip data kosong
            if (!$npm || !$namaLengkap) {
                $this->addError($row, 'NPM atau Nama Lengkap kosong');
                $this->incrementFailed();
                return null;
            }

            // Skip jika sudah ada
            if (Mahasiswa::where('npm', $npm)->exists()) {
                $this->addError($row, "NPM {$npm} sudah terdaftar");
                $this->incrementFailed();
                return null;
            }

            DB::beginTransaction();
            
            // Fakultas
            $fakultas = null;
            if ($namaFakultas) {
                $fakultas = Fakultas::firstOrCreate([
                    'nama_fakultas' => $namaFakultas
                ]);
            }

            // Prodi
            $prodi = null;
            if ($namaProdi && $fakultas) {
                $prodi = Prodi::firstOrCreate(
                    [
                        'nama_prodi' => $namaProdi,
                        'fakultas_id' => $fakultas->id
                    ],
                    [
                        'jenjang' => $jenjang ?? 'S1'
                    ]
                );
            }

            // Email generate
            $userEmail = $email;
            if (!$userEmail) {
                $userEmail = strtolower(str_replace(' ', '', $namaLengkap)) . "@student.unisba.ac.id";
            }

            if (User::where('email', $userEmail)->exists()) {
                $userEmail = $npm . '@student.unisba.ac.id';
            }

            // Buat user
            $user = User::create([
                'name' => $namaLengkap,
                'email' => $userEmail,
                'password' => Hash::make(Str::random(8)),
                'role' => 'mahasiswa'
            ]);

            // Parse tanggal
            $parsedTglLahir = $this->parseDate($tanggalLahir);
            $parsedTglMasuk = $this->parseDate($tanggalMasuk);

            // Parse IPK
            $parsedIpk = null;
            if ($ipk && $ipk !== '-') {
                $parsedIpk = floatval(str_replace(',', '.', $ipk));
                if ($parsedIpk > 4) $parsedIpk = null;
            }

            // Map jenis kelamin
            $jk = strtolower(trim((string) $jenisKelamin));

            $mapJk = [
                'l' => 'L',
                'lk' => 'L',
                'laki' => 'L',
                'laki-laki' => 'L',
                'pria' => 'L',
                'male' => 'L',

                'p' => 'P',
                'pr' => 'P',
                'perempuan' => 'P',
                'wanita' => 'P',
                'female' => 'P',
            ];

            $jenisKelaminValue = $mapJk[$jk] ?? 'L';

            // Buat mahasiswa dengan semua kolom termasuk dosen wali
            Mahasiswa::create([
                'user_id' => $user->id,
                'prodi_id' => $prodi ? $prodi->id : null,
                'npm' => $npm,
                'dosen_wali' => $dosenWali,           // sesuai migration
                'dosen_wali_nik' => $nikDosenWali,    // sesuai migration
                'nama_lengkap' => $namaLengkap,
                'tempat_lahir' => $tempatLahir,
                'tanggal_lahir' => $parsedTglLahir,
                'alamat' => $alamat,
                'no_hp' => $noHp,
                'tanggal_masuk' => $parsedTglMasuk,
                'status_mahasiswa' => $this->mapStatus($status),
                'ipk' => $parsedIpk,
                'jenis_kelamin' => $jenisKelaminValue,
                'sks_tempuh' => $sksTempuh ? (int) $sksTempuh : null,
            ]);

            DB::commit();
            $this->incrementSuccess();
            
            Log::info("Berhasil import mahasiswa: {$npm} - {$namaLengkap}");
            
            return null;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError($row, $e->getMessage());
            $this->incrementFailed();
            Log::error("Import error: " . $e->getMessage());
            return null;
        }
    }

    public function chunkSize(): int
    {
        return 50;
    }

    private function incrementSuccess()
    {
        try {
            $progress = ImportProgress::where('batch_id', $this->batchId)->first();
            if ($progress) {
                $progress->increment('success_rows');
                $progress->increment('processed_rows');
            }
        } catch (\Exception $e) {
            Log::error("Gagal update progress success: " . $e->getMessage());
        }
    }

    private function incrementFailed()
    {
        try {
            $progress = ImportProgress::where('batch_id', $this->batchId)->first();
            if ($progress) {
                $progress->increment('failed_rows');
                $progress->increment('processed_rows');
            }
        } catch (\Exception $e) {
            Log::error("Gagal update progress failed: " . $e->getMessage());
        }
    }

    private function addError($row, $message)
    {
        $npm = $this->getValue($row, ['npm', 'nim']) ?? 'Unknown';
        $this->failedRows[] = [
            'npm' => $npm,
            'message' => $message,
            'row_data' => json_encode($row)
        ];
    }

    private function getValue($row, $keys)
    {
        foreach ($row as $rowKey => $rowValue) {
            foreach ($keys as $key) {
                if (strtolower(trim($rowKey)) == strtolower(trim($key)) && $rowValue !== null && $rowValue !== '') {
                    return trim((string) $rowValue);
                }
            }
        }
        return null;
    }

    private function parseDate($dateString)
    {
        if (!$dateString || $dateString == '-' || $dateString == '') {
            return now()->format('Y-m-d');
        }

        try {

            // Excel numeric date
            if (is_numeric($dateString)) {
                return Date::excelToDateTimeObject($dateString)
                    ->format('Y-m-d');
            }

            // d/m/Y
            if (strpos($dateString, '/') !== false) {
                return Carbon::createFromFormat('d/m/Y', trim($dateString))
                    ->format('Y-m-d');
            }

            return Carbon::parse($dateString)->format('Y-m-d');

        } catch (\Exception $e) {

            Log::warning("Tanggal gagal parse: " . $dateString);

            return now()->format('Y-m-d');
        }
    }

    private function mapStatus($status)
    {
        $map = [
            'aktif' => 'Aktif',
            'cuti' => 'Cuti',
            'lulus' => 'Lulus',
            'active' => 'Aktif',
        ];

        $statusLower = strtolower(trim((string) $status));
        return $map[$statusLower] ?? 'Aktif';
    }

    public function getFailedRows()
    {
        return $this->failedRows;
    }
}
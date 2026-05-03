<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Fakultas;
use App\Models\Prodi;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MahasiswaImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($rows as $index => $row) {
            try {
                // Ambil data dengan key yang fleksibel
                $npm = $this->getValue($row, ['npm', 'NPM', 'nim', 'NIM']);
                $namaLengkap = $this->getValue($row, ['nama_lengkap', 'nama_mahasiswa', 'Nama Mahasiswa', 'nama', 'NAMA']);
                $email = $this->getValue($row, ['email', 'Email', 'EMAIL']);
                $tempatLahir = $this->getValue($row, ['tempat_lahir', 'tmpt_lahir', 'Tempat Lahir', 'Tmpt Lahir', 'TEMPAT LAHIR']);
                $tanggalLahir = $this->getValue($row, ['tanggal_lahir', 'tgl_lahir', 'Tanggal Lahir', 'Tgl Lahir', 'TANGGAL LAHIR']);
                $alamat = $this->getValue($row, ['alamat', 'Alamat', 'ALAMAT']);
                $noHp = $this->getValue($row, ['no_hp', 'no_hp', 'No HP', 'no_telepon', 'No Telepon', 'NOMOR HP']);
                $namaFakultas = $this->getValue($row, ['nama_fakultas', 'fakultas', 'Fakultas', 'NAMA FAKULTAS']);
                $namaProdi = $this->getValue($row, ['program_studi', 'prodi', 'Program Studi', 'PROGRAM STUDI']);
                $jenjang = $this->getValue($row, ['jenjang', 'Jenjang', 'jenjang_s1_s2_s3_profesi', 'JENJANG']);
                $status = $this->getValue($row, ['status', 'Status', 'status_mahasiswa', 'Status Mahasiswa', 'STATUS']);
                $tanggalMasuk = $this->getValue($row, ['tanggal_masuk', 'tgl_masuk', 'Tanggal Masuk', 'TANGGAL MASUK']);
                $ipk = $this->getValue($row, ['ipk', 'IPK', 'ipk_3_digit', 'IPK 3 digit']);
                $dosenWali = $this->getValue($row, ['dosen_wali', 'Dosen Wali', 'DOSEN WALI']);
                $dosenWaliNik = $this->getValue($row, ['nik_dosen_wali', 'NIK Dosen Wali', 'NIK DOSEN WALI']);
                $jenisKelamin = $this->getValue($row, ['jenis_kelamin', 'jk', 'Jenis Kelamin', 'JK']);
                
                // Skip jika tidak ada NPM atau Nama
                if (!$npm || !$namaLengkap) {
                    Log::warning("Baris " . ($index + 2) . ": NPM atau Nama kosong, dilewati");
                    $errorCount++;
                    continue;
                }
                
                // Cek apakah NPM sudah ada
                if (Mahasiswa::where('npm', $npm)->exists()) {
                    Log::info("NPM {$npm} sudah ada, dilewati");
                    $errorCount++;
                    continue;
                }
                
                // Proses fakultas (opsional)
                $fakultas = null;
                if ($namaFakultas) {
                    $fakultas = Fakultas::firstOrCreate(
                        ['nama_fakultas' => $namaFakultas],
                        ['nama_fakultas' => $namaFakultas]
                    );
                }
                
                // Proses prodi (opsional)
                $prodi = null;
                if ($namaProdi && $fakultas) {
                    $prodi = Prodi::firstOrCreate(
                        [
                            'nama_prodi' => $namaProdi,
                            'fakultas_id' => $fakultas->id
                        ],
                        [
                            'nama_prodi' => $namaProdi,
                            'fakultas_id' => $fakultas->id,
                            'jenjang' => $jenjang ?? 'S1'
                        ]
                    );
                }
                
                // Buat user account
                $password = Str::random(8);
                $userEmail = $email ?: strtolower(str_replace(' ', '', $namaLengkap)) . "@student.unisba.ac.id";
                
                // Cek email sudah ada atau belum
                if (User::where('email', $userEmail)->exists()) {
                    $userEmail = $npm . '@student.unisba.ac.id';
                }
                
                $user = User::create([
                    'name' => $namaLengkap,
                    'email' => $userEmail,
                    'password' => Hash::make($password),
                    'role' => 'mahasiswa'
                ]);
                
                // Parse tanggal lahir
                $parsedTglLahir = $this->parseDate($tanggalLahir);
                $parsedTglMasuk = $this->parseDate($tanggalMasuk);
                
                // Parse IPK (ganti koma dengan titik)
                $parsedIpk = null;
                if ($ipk && $ipk !== '-') {
                    $ipkStr = str_replace(',', '.', $ipk);
                    $parsedIpk = floatval($ipkStr);
                    if ($parsedIpk > 4) $parsedIpk = null;
                }
                
                // Buat data mahasiswa dengan handling null
                $mahasiswaData = [
                    'user_id' => $user->id,
                    'prodi_id' => $prodi ? $prodi->id : null,
                    'npm' => $npm,
                    'nama_lengkap' => $namaLengkap,
                    'dosen_wali' => $dosenWali ?: null,
                    'dosen_wali_nik' => $dosenWaliNik ?: null,
                    'tempat_lahir' => $tempatLahir ?: null,
                    'tanggal_lahir' => $parsedTglLahir,
                    'alamat' => $alamat ?: null,
                    'no_hp' => $noHp ?: null,
                    'jenis_kelamin' => $jenisKelamin ?: null,
                    'tanggal_masuk' => $parsedTglMasuk ?: null,
                    'status_mahasiswa' => $this->mapStatus($status) ?: 'Aktif',
                    'ipk' => $parsedIpk,
                ];
                
                // Hapus key dengan value null
                $mahasiswaData = array_filter($mahasiswaData, function($value) {
                    return $value !== null;
                });
                
                Mahasiswa::create($mahasiswaData);
                
                $successCount++;
                Log::info("Berhasil import: {$npm} - {$namaLengkap}");
                
            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Error import baris " . ($index + 2) . ": " . $e->getMessage());
            }
        }
        
        // Simpan hasil ke session
        session()->flash('import_summary', [
            'success' => $successCount,
            'error' => $errorCount,
            'total' => $rows->count()
        ]);
    }
    
    /**
     * Get value from row with multiple possible keys
     */
    private function getValue($row, $keys)
    {
        foreach ($keys as $key) {
            // Cek case-sensitive
            if (isset($row[$key]) && $row[$key] !== null && $row[$key] !== '') {
                return trim($row[$key]);
            }
            // Cek case-insensitive untuk string key
            foreach ($row as $rowKey => $rowValue) {
                if (strtolower($rowKey) == strtolower($key) && $rowValue !== null && $rowValue !== '') {
                    return trim($rowValue);
                }
            }
        }
        return null;
    }
    
    /**
     * Parse date from various formats
     */
    private function parseDate($dateString)
    {
        if (!$dateString || $dateString == '-' || $dateString == '') {
            return null;
        }
        
        try {
            // Coba berbagai format tanggal
            $formats = [
                'd/m/Y',
                'd/m/y',
                'Y-m-d',
                'd-m-Y',
                'm/d/Y',
                'd M Y',
                'd F Y',
                'j/n/Y',
            ];
            
            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, $dateString);
                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            }
            
            // Jika semua format gagal, coba Carbon parse
            $date = Carbon::parse($dateString);
            return $date->format('Y-m-d');
            
        } catch (\Exception $e) {
            Log::warning("Gagal parse tanggal: {$dateString}");
            return null;
        }
    }
    
    /**
     * Map status to valid values
     */
    private function mapStatus($status)
    {
        if (!$status) return 'Aktif';
        
        $statusMap = [
            'aktif' => 'Aktif',
            'Aktif' => 'Aktif',
            'ACTIVE' => 'Aktif',
            'active' => 'Aktif',
            'cuti' => 'Cuti',
            'Cuti' => 'Cuti',
            'lulus' => 'Lulus',
            'Lulus' => 'Lulus',
            'GRADUATED' => 'Lulus',
            'graduated' => 'Lulus',
        ];
        
        return $statusMap[trim($status)] ?? 'Aktif';
    }
}
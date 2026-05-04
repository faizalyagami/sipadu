<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Fakultas;
use App\Models\Prodi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;

class MahasiswaImport implements ToModel, WithHeadingRow, WithChunkReading, ShouldQueue
{
    public function model(array $row)
    {
        try {
            // Ambil data fleksibel
            $npm = $this->getValue($row, ['npm', 'nim']);
            $namaLengkap = $this->getValue($row, ['nama_lengkap', 'nama', 'nama_mahasiswa']);
            $email = $this->getValue($row, ['email']);
            $tempatLahir = $this->getValue($row, ['tempat_lahir']);
            $tanggalLahir = $this->getValue($row, ['tanggal_lahir', 'tgl_lahir']);
            $alamat = $this->getValue($row, ['alamat']);
            $noHp = $this->getValue($row, ['no_hp', 'no_telepon']);
            $namaFakultas = $this->getValue($row, ['nama_fakultas', 'fakultas']);
            $namaProdi = $this->getValue($row, ['program_studi', 'prodi']);
            $jenjang = $this->getValue($row, ['jenjang']);
            $status = $this->getValue($row, ['status']);
            $tanggalMasuk = $this->getValue($row, ['tanggal_masuk', 'tgl_masuk']);
            $ipk = $this->getValue($row, ['ipk']);

            // Skip data kosong
            if (!$npm || !$namaLengkap) {
                return null;
            }

            // Skip jika sudah ada
            if (Mahasiswa::where('npm', $npm)->exists()) {
                return null;
            }

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
            $userEmail = $email ?: strtolower(str_replace(' ', '', $namaLengkap)) . "@student.unisba.ac.id";

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

            return new Mahasiswa([
                'user_id' => $user->id,
                'prodi_id' => $prodi ? $prodi->id : null,
                'npm' => $npm,
                'nama_lengkap' => $namaLengkap,
                'tempat_lahir' => $tempatLahir,
                'tanggal_lahir' => $parsedTglLahir,
                'alamat' => $alamat,
                'no_hp' => $noHp,
                'tanggal_masuk' => $parsedTglMasuk,
                'status_mahasiswa' => $this->mapStatus($status),
                'ipk' => $parsedIpk,
            ]);

        } catch (\Exception $e) {
            Log::error("Import error: " . $e->getMessage());
            return null;
        }
    }

    public function chunkSize(): int
    {
        return 100;
    }

    private function getValue($row, $keys)
    {
        foreach ($row as $rowKey => $rowValue) {
            foreach ($keys as $key) {
                if (strtolower($rowKey) == strtolower($key) && $rowValue !== null && $rowValue !== '') {
                    return trim($rowValue);
                }
            }
        }
        return null;
    }

    private function parseDate($dateString)
    {
        if (!$dateString || $dateString == '-') return null;

        try {
            return Carbon::parse($dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function mapStatus($status)
    {
        $map = [
            'aktif' => 'Aktif',
            'cuti' => 'Cuti',
            'lulus' => 'Lulus',
        ];

        return $map[strtolower(trim($status))] ?? 'Aktif';
    }
}
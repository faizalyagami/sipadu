<?php

namespace App\Exports;

use App\Models\Mahasiswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class MahasiswaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $status;

    public function __construct($status = null)
    {
        $this->status = $status;
    }

    public function collection()
    {
        $query = Mahasiswa::with(['prodi.fakultas', 'user']);
        
        if ($this->status && $this->status != 'semua') {
            $query->where('status_mahasiswa', $this->status);
        }
        
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'NPM',
            'Nama Mahasiswa',
            'NIK Dosen Wali',
            'Dosen Wali',
            'SKS Lulus',
            'SKS Tempuh',
            'SKS Sisa',
            'IPK',
            'IPK (3 digit)',
            'Tmpt Lahir',
            'Tgl Lahir',
            'Jenis Kelamin',
            'No HP',
            'Email',
            'Fakultas',
            'Program Studi',
            'Jenjang',
            'Status Mahasiswa',
            'Tanggal Masuk',
            'Alamat'
        ];
    }

    public function map($mahasiswa): array
    {
        // Hitung SKS (contoh: 144 total SKS untuk S1)
        $totalSKS = 144;
        $sksTempuh = $mahasiswa->sks_tempuh ?? 0;
        $sksLulus = $sksTempuh;
        $sksSisa = $totalSKS - $sksTempuh;
        
        return [
            $mahasiswa->npm,
            $mahasiswa->nama_lengkap,
            $mahasiswa->dosen_wali_nik ?? '-',
            $mahasiswa->dosen_wali ?? '-',
            $sksLulus,
            $sksTempuh,
            $sksSisa,
            $mahasiswa->ipk ? number_format($mahasiswa->ipk, 2, ',', '') : '-',
            $mahasiswa->ipk ? number_format($mahasiswa->ipk, 3, ',', '') : '-',
            $mahasiswa->tempat_lahir,
            $mahasiswa->tanggal_lahir ? $mahasiswa->tanggal_lahir->format('d/m/Y') : '-',
            $mahasiswa->jenis_kelamin ?? '-',
            $mahasiswa->no_hp ?? '-',
            $mahasiswa->user->email ?? '-',
            $mahasiswa->prodi->fakultas->nama_fakultas ?? '-',
            $mahasiswa->prodi->nama_prodi ?? '-',
            $mahasiswa->prodi->jenjang ?? '-',
            $mahasiswa->status_mahasiswa,
            $mahasiswa->tanggal_masuk ? $mahasiswa->tanggal_masuk->format('d/m/Y') : '-',
            $mahasiswa->alamat,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk header
        $sheet->getStyle('A1:T1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '6F42C1'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style untuk border
        $sheet->getStyle('A1:T' . ($sheet->getHighestRow()))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DDDDDD'],
                ],
            ],
        ]);

        // Style untuk baris data
        $sheet->getStyle('A2:T' . ($sheet->getHighestRow()))->applyFromArray([
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Set tinggi baris header
        $sheet->getRowDimension(1)->setRowHeight(25);
        
        return $sheet;
    }
}
<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class MahasiswaTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        // Data contoh untuk template
        return [
            [
                '10050019001',
                'Ahmad Fauzi',
                'D123456',
                'Dr. Budi Santoso, M.Pd.',
                'BANDUNG',
                '15/05/2000',
                'Jl. Merdeka No. 123',
                'ahmad@student.unisba.ac.id',
                '081234567890',
                'Fakultas Teknik',
                'Teknik Informatika',
                'S1',
                'Aktif',
                '2020-09-01',
                '3.75'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'NPM',
            'Nama Lengkap',
            'NIK Dosen Wali',
            'Dosen Wali',
            'Tempat Lahir',
            'Tanggal Lahir (dd/mm/yyyy)',
            'Alamat',
            'Email',
            'No HP',
            'Nama Fakultas',
            'Program Studi',
            'Jenjang (S1/S2/S3/Profesi)',
            'Status (Aktif/Cuti/Lulus)',
            'Tanggal Masuk (yyyy-mm-dd)',
            'IPK'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style header
        $sheet->getStyle('A1:O1')->applyFromArray([
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

        // Set kolom agar lebih lebar
        foreach(range('A', 'O') as $column) {
            $sheet->getColumnDimension($column)->setWidth(20);
        }

        // Border untuk seluruh data
        $sheet->getStyle('A1:O2')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        return $sheet;
    }
}
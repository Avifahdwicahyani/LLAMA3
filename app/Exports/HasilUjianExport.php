<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Events\AfterSheet;

class HasilUjianExport implements FromView, WithEvents, WithTitle
{
   protected $ujian, $hasilujian, $kelas, $kolomNilai;

    public function __construct($ujian, $hasilujian, $kelas, $kolomNilai)
    {
        $this->ujian = $ujian;
        $this->hasilujian = $hasilujian;
        $this->kelas = $kelas;
        $this->kolomNilai = $kolomNilai;
    }

    public function view(): View
    {
        return view('guru.ujian.hasilnilai_excel', [
            'ujian' => $this->ujian,
            'hasilujian' => $this->hasilujian,
            'kelas' => $this->kelas,
            'kolomNilai' => $this->kolomNilai
        ]);
    }

    public function title(): string
    {
        return 'Hasil Ujian';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->mergeCells('A1:E1');
                $sheet->setCellValue('A1', 'SMA NEGERI 4 PAMEKASAN');
                $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                $sheet->mergeCells('A2:E2');
                $sheet->setCellValue('A2', 'Jl. Pintu Gerbang No. 39a Telp (0342) 322595 Pamekasan Kode Pos 69316');
                $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

                $sheet->mergeCells('A3:E3');
                $sheet->setCellValue('A3', 'Website: sman4pamekasan.sch.id | Email: admin@sman4pamekasan.sch.id');
                $sheet->getStyle('A3')->getAlignment()->setHorizontal('center');

                $sheet->mergeCells('A5:E5');
                $sheet->setCellValue('A5', 'HASIL UJIAN ' . strtoupper($this->ujian->name ?? 'XXXX'));
                $sheet->getStyle('A5')->getFont()->setSize(14)->setBold(true)->setUnderline(true);
                $sheet->getStyle('A5')->getAlignment()->setHorizontal('center');

                $highestRow = $sheet->getHighestRow();
                $highestCol = $sheet->getHighestColumn();
                $sheet->getStyle("A7:{$highestCol}{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $sheet->getStyle('A7:' . $highestCol . '7')->getFont()->setBold(true);

                foreach (range('A', $highestCol) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            }
        ];
    }
}

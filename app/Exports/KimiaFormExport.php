<?php

namespace App\Exports;

use App\Models\KimiaForm;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KimiaFormExport implements FromArray, WithStyles, WithTitle, WithColumnWidths, WithEvents
{
    protected $form;
    
    public function __construct(KimiaForm $form)
    {
        $this->form = $form;
    }
    
    public function array(): array
    {
        $header = [
            [$this->form->title],
            ['No Form', $this->form->no],
            ['Tanggal', $this->form->tanggal],
            [''],
        ];
        
        $allData = [];
        $tables = $this->form->tables()->with(['columns' => function($q){ $q->orderBy('urutan'); }, 'entries'])->get();
        
        foreach ($tables as $table) {
            $allData[] = [$table->name];
            $allData[] = [''];
            
            $columns = $table->columns;
            $entries = $table->entries()->orderBy('id')->get();
            
            $tableHeader = $columns->map(fn($col) => $col->nama_kolom)->toArray();
            $allData[] = $tableHeader;
            
            $tableRows = [];
            foreach ($entries as $entry) {
                $row = [];
                foreach ($columns as $col) {
                    $value = $entry->data[$col->id] ?? '';
                    
                    // Format data berdasarkan tipe kolom
                    if ($col->tipe_kolom === 'date' && $value) {
                        $value = \Carbon\Carbon::parse($value)->format('d/m/Y');
                    } elseif ($col->tipe_kolom === 'time' && $value) {
                        $value = $value;
                    } elseif ($col->tipe_kolom === 'decimal' && is_numeric($value)) {
                        $value = number_format($value, 2);
                    } elseif ($col->tipe_kolom === 'integer' && is_numeric($value)) {
                        $value = number_format($value);
                    }
                    
                    $row[] = $value;
                }
                $tableRows[] = $row;
            }
            
            $allData = array_merge($allData, $tableRows);
            $allData[] = [''];
        }
        
        $approvalRows = [
            ['Approval / Signature'],
            ['Nama', 'Jabatan', 'Status', 'Tanggal'],
        ];
        
        $approval = $this->form->signatures;
        foreach ($approval as $sig) {
            $approvalRows[] = [
                $sig->name ?? '-',
                $sig->jabatan ?? '-',
                ucfirst($sig->status),
                $sig->tanggal ? \Carbon\Carbon::parse($sig->tanggal)->format('d/m/Y') : '-',
            ];
        }
        
        $approvalRows[] = [''];
        $approvalRows[] = ['', '', '', 'No. Dokumen: ' . ($this->form->no_dokumen ?? '-')];
        
        return array_merge($header, $allData, $approvalRows);
    }
    
    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        return [];
    }
    
    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 25,
            'C' => 25,
            'D' => 25,
            'E' => 25,
            'F' => 25,
            'G' => 25,
            'H' => 25,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->mergeCells('A1:E1');
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A1')->getFill()->setFillType('solid')->getStartColor()->setRGB('dbeafe');

                $currentRow = 6;
                $tables = $this->form->tables()->with(['columns' => function($q){ $q->orderBy('urutan'); }, 'entries'])->get();

                $tableColors = [
                    '3b82f6', // biru BIASAA
                    '2563eb', // biru gelap
                    '1d4ed8', // biru lebih gelap
                    '1e40af', // biru sangat gelap
                    '1e3a8a', // biru navy
                    '1e293b', // biru slate
                    '0f172a', // biru hitam
                    '312e81', // biru indigo
                ];

                foreach ($tables as $index => $table) {
                    $currentRow += 2;

                    $columns = $table->columns;
                    $colCount = count($columns);
                    $colLetterEnd = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);
                    $entryCount = $table->entries()->count();

                    if ($entryCount > 0) {
                        $dataEnd = $currentRow + $entryCount;
                        $sheet->getStyle('A'.$currentRow.':'.$colLetterEnd.$dataEnd)->getBorders()->getAllBorders()->setBorderStyle('thin');
                    } else {
                        $sheet->getStyle('A'.$currentRow.':'.$colLetterEnd.$currentRow)->getBorders()->getAllBorders()->setBorderStyle('thin');
                    }
                    $headerRange = 'A'.$currentRow.':'.$colLetterEnd.$currentRow;
                    $colorHex = $tableColors[$index % count($tableColors)];
                    $sheet->getStyle($headerRange)->getBorders()->getTop()
                        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE);
                    $sheet->getStyle($headerRange)->getBorders()->getTop()->getColor()->setRGB($colorHex);

                    $currentRow += $entryCount + 2;
                }

                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getRowDimension(5)->setRowHeight(15);
            }
        ];
    }
    
    
    public function title(): string
    {
        return 'Form_'.$this->form->id;
    }
}

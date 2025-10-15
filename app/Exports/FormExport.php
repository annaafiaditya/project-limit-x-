<?php

namespace App\Exports;

use App\Models\MikrobiologiForm;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FormExport implements FromArray, WithStyles, WithTitle, WithColumnWidths, WithEvents
{
    protected $form;
    
    public function __construct(MikrobiologiForm $form)
    {
        $this->form = $form;
    }
    
    public function array(): array
    {
        $observations = $this->form->observations()->orderBy('tanggal')->get();
        $tglPengamatanText = '';
        
        if ($observations->count() > 0) {
        $tglPengamatanList = [];
        foreach ($observations as $obs) {
            $tglPengamatanList[] = \Carbon\Carbon::parse($obs->tanggal)->format('d/m/Y') . 
                ($obs->keterangan ? ' (' . $obs->keterangan . ')' : '');
        }
        $tglPengamatanText = implode(', ', $tglPengamatanList);
    } else {
        $tglPengamatanText = \Carbon\Carbon::parse($this->form->tgl_pengamatan)->format('d/m/Y');
    }
        
        $header = [
            [$this->form->title],
            ['No Form', $this->form->no],
            ['Tanggal Inokulasi', \Carbon\Carbon::parse($this->form->tgl_inokulasi)->format('d/m/Y')],
            ['Tanggal Pengamatan', $tglPengamatanText],
            [''],
        ];
        if ($this->form->judul_tabel) {
            $header[] = [$this->form->judul_tabel];
            $header[] = [''];
        }
        
        $columns = $this->form->columns()->orderBy('urutan')->get();
        $entries = $this->form->entries()->orderBy('id')->get();
        $approval = $this->form->signatures()->get();
        
        $tableHeader = $columns->map(fn($col) => $col->nama_kolom)->toArray();
        $tableRows = [];
        
        foreach ($entries as $entry) {
            $row = [];
            foreach ($columns as $col) {
                $value = $entry->data[$col->id] ?? '';

                if ($col->tipe_kolom === 'date' && $value) {
                    $value = \Carbon\Carbon::parse($value)->format('d/m/Y');
                } elseif ($col->tipe_kolom === 'time' && $value) {
                    $value = $value;
                } elseif ($col->tipe_kolom === 'integer' && is_numeric($value)) {
                    $value = number_format($value);
                } elseif ($col->tipe_kolom === 'decimal' && is_numeric($value)) {
                    $value = number_format($value, 2);
                }
                
                $row[] = $value;
            }
            $tableRows[] = $row;
        }
        
        $approvalRows = [
            [''],
            ['Approval / Signature'],
            ['Nama', 'Jabatan', 'Status', 'Tanggal'],
        ];
        
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
        
        return array_merge($header, [$tableHeader], $tableRows, $approvalRows);
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
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A1')->getFill()->setFillType('solid')->getStartColor()->setRGB('dbeafe');

                $sheet->getStyle('A2:B4')->getFont()->setBold(true);
                $sheet->getStyle('A2:B4')->getFill()->setFillType('solid')->getStartColor()->setRGB('f8fafc');

                $columns = $this->form->columns()->orderBy('urutan')->get();
                $colCount = count($columns);
                $colLetterEnd = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);
                $entryCount = $this->form->entries()->count();

                $headerRow = 6;
                if ($this->form->judul_tabel) {
                    $headerRow = 8;
                }

                $dataStart = $headerRow + 1;
                $dataEnd = $headerRow + $entryCount;

                $sheet->getStyle('A'.$headerRow.':'.$colLetterEnd.$headerRow)->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A'.$headerRow.':'.$colLetterEnd.$headerRow)->getFont()->setBold(true);
                $sheet->getStyle('A'.$headerRow.':'.$colLetterEnd.$headerRow)->getFill()->setFillType('solid')->getStartColor()->setRGB('3b82f6');
                $sheet->getStyle('A'.$headerRow.':'.$colLetterEnd.$headerRow)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));

                if ($entryCount > 0) {
                    $sheet->getStyle('A'.$headerRow.':'.$colLetterEnd.$dataEnd)->getBorders()->getAllBorders()->setBorderStyle('thin');

                    for ($i = $dataStart; $i <= $dataEnd; $i++) {
                        if (($i - $dataStart) % 2 == 0) {
                            $sheet->getStyle('A'.$i.':'.$colLetterEnd.$i)->getFill()->setFillType('solid')->getStartColor()->setRGB('f0f9ff');
                        }
                    }
                } else {
                    $sheet->getStyle('A'.$headerRow.':'.$colLetterEnd.$headerRow)->getBorders()->getAllBorders()->setBorderStyle('thin');
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
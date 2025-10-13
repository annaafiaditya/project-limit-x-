<?php

namespace App\Exports;

use App\Models\KimiaForm;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class KimiaCombinedExport implements WithMultipleSheets
{
    protected array $formIds;

    public function __construct(array $formIds)
    {
        $this->formIds = $formIds;
    }

    public function sheets(): array
    {
        $sheets = [];
        $forms = KimiaForm::with(['tables.columns' => function($q){ $q->orderBy('urutan'); }, 'tables.entries', 'signatures'])
            ->whereIn('id', $this->formIds)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($forms as $form) {
            $sheets[] = new KimiaFormExport($form);
        }

        return $sheets;
    }
}



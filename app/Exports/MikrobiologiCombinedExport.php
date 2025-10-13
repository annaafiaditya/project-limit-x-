<?php

namespace App\Exports;

use App\Models\MikrobiologiForm;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MikrobiologiCombinedExport implements WithMultipleSheets
{
    protected array $formIds;

    public function __construct(array $formIds)
    {
        $this->formIds = $formIds;
    }

    public function sheets(): array
    {
        $sheets = [];
        $forms = MikrobiologiForm::with(['columns' => function($q){ $q->orderBy('urutan'); }, 'entries', 'signatures', 'observations'])
            ->whereIn('id', $this->formIds)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($forms as $form) {
            $sheets[] = new FormExport($form);
        }

        return $sheets;
    }
}



<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailyLedgerExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data['entries'];
    }

    public function headings(): array
    {
        return [
            'Date',
            'Reference',
            'Account',
            'Sub-Account',
            'Description',
            'Debit',
            'Credit',
            'Running Balance'
        ];
    }

    public function map($entry): array
    {
        $subAccount = $entry->customer ? $entry->customer->name : ($entry->vendor ? $entry->vendor->name : ($entry->employee ? $entry->employee->name : '-'));

        return [
            $entry->transaction_date->format('Y-m-d'),
            $entry->reference_number,
            $entry->chartOfAccount->code . ' - ' . $entry->chartOfAccount->name,
            $subAccount,
            $entry->description,
            $entry->debit,
            $entry->credit,
            $entry->running_balance
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

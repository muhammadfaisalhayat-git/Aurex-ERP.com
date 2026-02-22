<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerStatementExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $customer;
    protected $entries;
    protected $openingBalance;
    protected $runningBalance;

    public function __construct($customer, $entries, $openingBalance)
    {
        $this->customer = $customer;
        $this->entries = $entries;
        $this->openingBalance = $openingBalance;
        $this->runningBalance = $openingBalance;
    }

    public function collection()
    {
        return $this->entries;
    }

    public function headings(): array
    {
        return [
            ['Customer Statement'],
            ['Customer:', $this->customer->name . ' (' . $this->customer->code . ')'],
            ['Date:', now()->format('Y-m-d')],
            [],
            ['Date', 'Reference', 'Description', 'Debit', 'Credit', 'Balance']
        ];
    }

    public function map($entry): array
    {
        $this->runningBalance += ($entry->debit - $entry->credit);

        return [
            $entry->transaction_date->format('Y-m-d'),
            $entry->reference_number,
            $entry->description,
            $entry->debit,
            $entry->credit,
            $this->runningBalance
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            5 => ['font' => ['bold' => true]],
        ];
    }
}

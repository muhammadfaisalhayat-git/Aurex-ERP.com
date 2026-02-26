<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UniversalStatementExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;
    protected $currentBalance;

    public function __construct($data)
    {
        $this->data = $data;
        $this->currentBalance = $data['openingBalance'];
    }

    public function collection()
    {
        return $this->data['results'];
    }

    public function headings(): array
    {
        $isStock = in_array($this->data['type'], ['product', 'warehouse', 'category', 'stock_supply', 'stock_receiving', 'stock_transfer', 'transfer_request', 'issue_order', 'composite_assembly']);

        if ($isStock) {
            return [
                'Date',
                'Reference',
                'Description',
                'Stock In',
                'Stock Out',
                'Running Balance'
            ];
        }

        return [
            'Date',
            'Reference',
            'Description',
            'Debit',
            'Credit',
            'Running Balance'
        ];
    }

    public function map($item): array
    {
        $isStock = in_array($this->data['type'], ['product', 'warehouse', 'category', 'stock_supply', 'stock_receiving', 'stock_transfer', 'transfer_request', 'issue_order', 'composite_assembly']);

        if ($isStock) {
            $in = $item->movement_type === 'in' ? $item->quantity : 0;
            $out = $item->movement_type === 'out' ? $item->quantity : 0;
            $this->currentBalance += ($in - $out);

            return [
                $item->transaction_date->format('Y-m-d'),
                $item->reference_number,
                $item->notes,
                $in > 0 ? number_format($in, 3) : '-',
                $out > 0 ? number_format($out, 3) : '-',
                number_format($this->currentBalance, 3)
            ];
        }

        $debit = $item->debit;
        $credit = $item->credit;
        $balanceFactor = ($this->data['type'] === 'vendor') ? ($credit - $debit) : ($debit - $credit);
        $this->currentBalance += $balanceFactor;

        return [
            $item->transaction_date->format('Y-m-d'),
            $item->reference_number,
            $item->description,
            $debit > 0 ? number_format($debit, 2) : '-',
            $credit > 0 ? number_format($credit, 2) : '-',
            number_format($this->currentBalance, 2)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

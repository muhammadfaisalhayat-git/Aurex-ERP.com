<?php

namespace App\Exports\SalesReport;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesByItemExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function collection()
    {
        return $this->items;
    }

    public function headings(): array
    {
        return [
            __('reports.invoice'),
            __('reports.date'),
            __('reports.item_code'),
            __('reports.item_name'),
            __('reports.quantity'),
            __('reports.unit_price'),
            __('reports.net'),
            __('reports.tax'),
            __('reports.gross'),
        ];
    }

    public function map($item): array
    {
        return [
            $item->salesInvoice?->document_number ?? 'N/A',
            $item->salesInvoice?->invoice_date ? $item->salesInvoice->invoice_date->format('Y-m-d') : 'N/A',
            $item->product?->code ?? 'N/A',
            $item->product?->name ?? 'N/A',
            $item->quantity ?? 0,
            $item->unit_price ?? 0,
            $item->net_amount ?? 0,
            $item->tax_amount ?? 0,
            $item->gross_amount ?? 0,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

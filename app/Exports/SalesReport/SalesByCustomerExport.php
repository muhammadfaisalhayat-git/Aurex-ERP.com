<?php

namespace App\Exports\SalesReport;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesByCustomerExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $invoices;

    public function __construct($invoices)
    {
        $this->invoices = $invoices;
    }

    public function collection()
    {
        return $this->invoices;
    }

    public function headings(): array
    {
        return [
            __('reports.invoice'),
            __('reports.date'),
            __('reports.customer_code'),
            __('reports.customer_name'),
            __('reports.net'),
            __('reports.tax'),
            __('reports.gross'),
            __('reports.status'),
        ];
    }

    public function map($invoice): array
    {
        return [
            $invoice->document_number ?? 'N/A',
            $invoice->invoice_date?->format('Y-m-d') ?? 'N/A',
            $invoice->customer?->code ?? 'N/A',
            $invoice->customer?->name ?? 'N/A',
            $invoice->subtotal ?? 0,
            $invoice->tax_amount ?? 0,
            $invoice->total_amount ?? 0,
            $invoice->status ?? 'unknown',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

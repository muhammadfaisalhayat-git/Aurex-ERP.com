<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('reports.sales_by_customer') }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
        }

        .report-title {
            font-size: 14px;
            margin-top: 5px;
            color: #1e293b;
        }

        .filters-info {
            margin-bottom: 15px;
            background: #f8fafc;
            padding: 10px;
            border-radius: 5px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th {
            background-color: #f1f5f9;
            border-bottom: 2px solid #e2e8f0;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }

        .items-table td {
            padding: 8px;
            border-bottom: 1px solid #f1f5f9;
        }

        .text-right {
            text-align: right !important;
        }

        [dir="rtl"] .items-table th,
        [dir="rtl"] .items-table td {
            text-align: right;
        }

        [dir="rtl"] .text-right {
            text-align: left !important;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="company-branding">
            @if(isset($logoBase64) && $logoBase64)
                <img src="{{ $logoBase64 }}" alt="Logo" style="max-height: 50px; margin-bottom: 5px;"><br>
            @elseif(isset($company) && $company?->logo)
                <img src="{{ public_path('storage/' . $company->logo) }}" alt="Logo"
                    style="max-height: 50px; margin-bottom: 5px;"><br>
            @endif
            <div class="company-name" style="font-family: 'DejaVu Sans', sans-serif;">
                {{ $company_name_ar ?: ($company?->name ?? config('app.name', 'Aurex ERP')) }}
            </div>
        </div>
        <div class="report-title">{{ __('reports.sales_by_customer') }}</div>
        <div style="font-size: 9px; color: #64748b;">{{ __('reports.date') }}: {{ now()->format('Y-m-d H:i') }}</div>
    </div>

    <div class="filters-info">
        <strong>{{ __('reports.filters') }}:</strong>
        @if(!empty($validated['date_from'])) {{ __('reports.date_from') }}: {{ $validated['date_from'] }} @endif
        @if(!empty($validated['date_to'])) {{ __('reports.date_to') }}: {{ $validated['date_to'] }} @endif
        @if(!empty($validated['customer_code'])) | {{ __('reports.customer_code') }}: {{ $validated['customer_code'] }}
        @endif
        @if(!empty($validated['customer_name'])) | {{ __('reports.customer_name') }}: {{ $validated['customer_name'] }}
        @endif
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>{{ __('reports.invoice') }}</th>
                <th>{{ __('reports.date') }}</th>
                <th>{{ __('reports.customer') }}</th>
                <th class="text-right">{{ __('reports.net') }}</th>
                <th class="text-right">{{ __('reports.tax') }}</th>
                <th class="text-right">{{ __('reports.gross') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->document_number }}</td>
                    <td>{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                    <td style="font-family: 'DejaVu Sans', sans-serif;">
                        {{ $invoice->customer_name_ar_reshaped ?? (optional($invoice->customer)->name ?? '') }}</td>
                    <td class="text-right">{{ number_format($invoice->subtotal, 2) }}</td>
                    <td class="text-right">{{ number_format($invoice->tax_amount, 2) }}</td>
                    <td class="text-right">{{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        {{ config('app.name') }} - {{ __('reports.sales_by_customer') }} - {{ now()->format('Y') }}
    </div>
</body>

</html>
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('reports.sales_by_item') }}</title>
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

        .summary-boxes {
            width: 100%;
            margin-bottom: 20px;
        }

        .summary-box {
            width: 24%;
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
        }

        .summary-box h6 {
            margin: 0;
            font-size: 8px;
            text-transform: uppercase;
        }

        .summary-box h3 {
            margin: 5px 0 0 0;
            font-size: 14px;
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
            @if(isset($company) && $company?->logo)
                <img src="{{ public_path('storage/' . $company->logo) }}" alt="Logo"
                    style="max-height: 50px; margin-bottom: 5px;"><br>
            @endif
            <div class="company-name">
                {{ $company_name_ar ?: ($company?->name ?? config('app.name', 'Aurex ERP')) }}
            </div>
        </div>
        <div class="report-title">{{ __('reports.sales_by_item') }}</div>
        <div style="font-size: 9px; color: #64748b;">{{ __('reports.date') }}: {{ now()->format('Y-m-d H:i') }}</div>
    </div>

    <div class="filters-info">
        <strong>{{ __('reports.filters') }}:</strong>
        @if(!empty($validated['date_from'])) {{ __('reports.date_from') }}: {{ $validated['date_from'] }} @endif
        @if(!empty($validated['date_to'])) {{ __('reports.date_to') }}: {{ $validated['date_to'] }} @endif
        @if(!empty($validated['item_code'])) | {{ __('reports.item_code') }}: {{ $validated['item_code'] }} @endif
        @if(!empty($validated['invoice_number'])) | {{ __('reports.invoice_number') }}:
        {{ $validated['invoice_number'] }} @endif
    </div>

    <div class="summary-boxes">
        <div class="summary-box">
            <h6>{{ __('reports.total_quantity') }}</h6>
            <h3>{{ number_format($totals->total_quantity ?? 0, 2) }}</h3>
        </div>
        <div class="summary-box" style="background: #10b981;">
            <h6>{{ __('reports.total_net') }}</h6>
            <h3>{{ number_format($totals->total_net ?? 0, 2) }}</h3>
        </div>
        <div class="summary-box" style="background: #06b6d4;">
            <h6>{{ __('reports.total_tax') }}</h6>
            <h3>{{ number_format($totals->total_tax ?? 0, 2) }}</h3>
        </div>
        <div class="summary-box" style="background: #f59e0b;">
            <h6>{{ __('reports.total_gross') }}</h6>
            <h3>{{ number_format($totals->total_gross ?? 0, 2) }}</h3>
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>{{ __('reports.invoice') }}</th>
                <th>{{ __('reports.date') }}</th>
                <th>{{ __('reports.item') }}</th>
                <th class="text-right">{{ __('reports.qty') }}</th>
                <th class="text-right">{{ __('reports.amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item->salesInvoice?->document_number ?? '' }}</td>
                    <td>{{ $item->salesInvoice?->invoice_date->format('Y-m-d') }}</td>
                    <td>{{ $item->product_name_ar_reshaped ?? $item->product?->name }}</td>
                    <td class="text-right">{{ number_format($item->quantity, 2) }}</td>
                    <td class="text-right">{{ number_format($item->gross_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        {{ config('app.name') }} - {{ __('reports.sales_by_item') }} - {{ now()->format('Y') }}
    </div>
</body>

</html>
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('sales.invoice') }} #{{ $invoice->invoice_number }}</title>
    <style>
        @page {
            margin: 20px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #334155;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 15px;
            padding-bottom: 10px;
        }

        .logo-cell {
            width: 20%;
            vertical-align: middle;
        }

        .branding-cell {
            width: 45%;
            vertical-align: middle;
            padding-left: 10px;
        }

        .meta-cell {
            width: 35%;
            vertical-align: top;
            text-align: right;
        }

        .company-name-en {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
        }

        .company-name-ar {
            font-size: 14px;
            color: #1e293b;
            margin-top: 2px;
        }

        .company-info {
            font-size: 9px;
            color: #64748b;
            margin-top: 5px;
        }

        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            color: #1e40af;
            margin: 0 0 5px 0;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        .meta-table td {
            padding: 2px 5px;
            border: 1px solid #cbd5e1;
        }

        .meta-label {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: bold;
            width: 40%;
            text-align: left;
        }

        .customer-section {
            margin-bottom: 15px;
        }

        .customer-title {
            font-weight: bold;
            color: #1e40af;
            border-bottom: 2px solid #1e40af;
            padding-bottom: 2px;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-size: 10px;
        }

        .customer-details {
            line-height: 1.3;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .items-table th {
            background-color: #f8fafc;
            border: 2px solid #1e40af;
            padding: 5px 10px;
            text-align: left;
            font-weight: bold;
            color: #1e40af;
            font-size: 9px;
        }

        .items-table td {
            padding: 5px 10px;
            border: 1px solid #cbd5e1;
            font-size: 9px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals-container {
            width: 100%;
        }

        .totals-table {
            width: 200px;
            margin-left: auto;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 4px 10px;
            font-size: 10px;
        }

        .total-label {
            text-align: right;
            font-weight: bold;
            color: #64748b;
        }

        .total-value {
            text-align: right;
            font-weight: bold;
            color: #0f172a;
        }

        .grand-total-row td {
            border-top: 2px solid #1e40af;
            font-size: 12px;
            padding-top: 8px;
            color: #1e40af !important;
        }

        /* RTL Support */
        [dir="rtl"] {
            text-align: right;
        }

        [dir="rtl"] .meta-cell {
            text-align: left;
        }

        [dir="rtl"] .meta-label {
            text-align: right;
        }

        [dir="rtl"] .items-table th,
        [dir="rtl"] .items-table td {
            text-align: right;
        }

        [dir="rtl"] .totals-table {
            margin-left: 0;
            margin-right: auto;
        }

        [dir="rtl"] .total-label,
        [dir="rtl"] .total-value {
            text-align: left;
        }
    </style>
</head>

<body dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                @if(isset($logoBase64) && $logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo" style="max-height: 70px;">
                @elseif($invoice->company?->logo)
                    <img src="{{ public_path('storage/' . $invoice->company->logo) }}" alt="Logo" style="max-height: 70px;">
                @endif
            </td>
            <td class="branding-cell">
                <div class="company-name-en">
                    {{ strtoupper($invoice->company?->name_en ?? $invoice->company?->name ?? 'BIN AWF AGRICULTURAL') }}
                </div>
                <div class="company-name-ar">
                    {{ $invoice->company_name_ar ?? $invoice->company?->name_ar ?? 'بن عوف الزراعية' }}</div>
                <div class="company-info">
                    @if($invoice->company?->vat_number)
                        <div><strong>VAT: {{ $invoice->company->vat_number }}</strong></div>
                    @endif
                    <div>{{ $invoice->company?->address ?? 'Street Address' }}, {{ $invoice->company?->city ?? 'City' }}
                    </div>
                    <div>{{ $invoice->company?->phone ?? 'Phone' }}</div>
                </div>
            </td>
            <td class="meta-cell">
                <div class="invoice-title">VAT Invoice</div>
                <table class="meta-table">
                    <tr>
                        <td class="meta-label">Date:</td>
                        <td>{{ $invoice->invoice_date->format('F d, Y') }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Invoice #:</td>
                        <td>{{ $invoice->document_number }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Customer ID:</td>
                        <td>{{ $invoice->customer?->code ?? 'N/A' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="customer-section">
        <div class="customer-title">Customer Information</div>
        <div class="customer-details">
            <div style="font-weight: bold; font-size: 11px;">
                {{ $invoice->customer_name_ar ?? $invoice->customer?->name_ar ?? $invoice->customer?->company_name ?? __('sales.cash_customer') }}
            </div>
            @if($invoice->customer?->tax_number)
                <div>VAT Number: {{ $invoice->customer->tax_number }}</div>
            @endif
            <div>{{ $invoice->customer?->address ?? 'Street Address' }}</div>
            <div>
                {{ implode(', ', array_filter([$invoice->customer?->city, $invoice->customer?->state, $invoice->customer?->zip_code])) ?: 'City, ST ZIP Code' }}
            </div>
            @if($invoice->customer?->phone)
                <div>Phone: {{ $invoice->customer->phone }}</div>
            @endif
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 15%;">Item Code</th>
                <th style="width: 45%;">Description</th>
                <th style="width: 10%; text-align: center;">Qty</th>
                <th style="width: 15%; text-align: right;">Unit Price</th>
                <th style="width: 15%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->product?->code ?? $item->product?->sku ?? '-' }}</td>
                    <td>
                        <strong>{{ $item->product_name_ar ?? $item->product->name_ar ?? $item->product->name_en }}</strong>
                        @if($item->description && $item->description !== $item->product?->name)
                            <div style="font-size: 8px; color: #64748b;">{{ $item->description_ar ?? $item->description }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">{{ number_format($item->gross_amount, 2) }}</td>
                </tr>
            @endforeach
            @for($i = count($invoice->items); $i < 3; $i++)
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <div class="totals-container">
        <table class="totals-table">
            <tr>
                <td class="total-label">Subtotal:</td>
                <td class="total-value">{{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td class="total-label">VAT:</td>
                <td class="total-value">{{ number_format($invoice->tax_amount, 2) }}</td>
            </tr>
            @if($invoice->discount_amount > 0)
                <tr>
                    <td class="total-label">Discount:</td>
                    <td class="total-value">-{{ number_format($invoice->discount_amount, 2) }}</td>
                </tr>
            @endif
            <tr class="grand-total-row">
                <td class="total-label">TOTAL:</td>
                <td class="total-value">{{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
        </table>
    </div>

    @if($invoice->notes)
        <div style="margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 10px;">
            <div style="font-weight: bold; color: #64748b; font-size: 8px; text-transform: uppercase;">Notes</div>
            <p style="color: #334155; font-size: 9px; margin-top: 3px;">{{ $invoice->notes_ar ?? $invoice->notes }}</p>
        </div>
    @endif

    <div
        style="margin-top: 30px; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #eee; padding-top: 8px;">
        <p>{{ __('messages.thank_you_for_business') }}</p>
        <p>{{ config('app.url', 'Aurex ERP') }}</p>
    </div>
</body>

</html>
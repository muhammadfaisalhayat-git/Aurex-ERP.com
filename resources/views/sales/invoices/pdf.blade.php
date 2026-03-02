<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('sales.invoice') }} #{{ $invoice->document_number }}</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('{{ base_path('vendor/dompdf/dompdf/lib/fonts/DejaVuSans.ttf') }}') format('truetype');
        }

        @font-face {
            font-family: 'DejaVu Sans Bold';
            src: url('{{ base_path('vendor/dompdf/dompdf/lib/fonts/DejaVuSans-Bold.ttf') }}') format('truetype');
            font-weight: bold;
        }

        @page {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #334155;
            background: #fff;
            margin: 0;
            padding: 20pt 30pt;
        }

        /* ---------- HEADER TABLE ---------- */
        .hdr {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .hdr td {
            vertical-align: top;
        }

        .hdr-left {
            width: 30%;
            text-align: left;
        }

        .hdr-mid {
            width: 40%;
            text-align: center;
        }

        .hdr-right {
            width: 30%;
            text-align: right;
        }

        .logo-img {
            max-height: 65px;
            max-width: 140px;
            margin-bottom: 6px;
        }

        .co-vat {
            font-weight: bold;
            font-size: 9pt;
            color: #1e293b;
        }

        .co-addr {
            font-size: 9pt;
            color: #475569;
        }

        .brand-en {
            font-size: 18pt;
            font-weight: bold;
            color: #1e293b;
            margin-top: 5px;
        }

        .brand-ar {
            font-size: 14pt;
            color: #1e293b;
            margin-top: 2px;
        }

        .doc-title {
            font-size: 18pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .meta {
            border-collapse: collapse;
            width: 100%;
        }

        .meta td {
            border: 0.5pt solid #cbd5e1;
            padding: 3pt 6pt;
            font-size: 9pt;
        }

        .meta-lbl {
            background: #f8fafc;
            color: #64748b;
            width: 40%;
            text-align: left;
        }

        /* ---------- CUSTOMER ---------- */
        .sect-title {
            font-weight: bold;
            color: #1e40af;
            font-size: 10pt;
            border-bottom: 1.5pt solid #1e40af;
            margin-bottom: 8px;
            padding-bottom: 2px;
        }

        .cust-box {
            margin-bottom: 15px;
        }

        .cust-name {
            font-weight: bold;
            font-size: 11pt;
            color: #0f172a;
            margin-bottom: 2px;
        }

        /* ---------- ITEMS TABLE ---------- */
        .items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .items th {
            border: 1.5pt solid #1e40af;
            background: #f8fafc;
            padding: 4pt 8pt;
            font-size: 9pt;
            font-weight: bold;
            color: #1e40af;
            text-align: left;
        }

        .items td {
            border: 0.5pt solid #cbd5e1;
            padding: 4pt 8pt;
            font-size: 9pt;
            height: 20pt;
            vertical-align: top;
        }

        .item-ar {
            font-weight: bold;
            color: #1e293b;
        }

        .item-en {
            font-size: 8pt;
            color: #64748b;
        }

        /* ---------- TOTALS ---------- */
        .totals-table {
            width: 200pt;
            border-collapse: collapse;
            margin-left: auto;
        }

        .totals-table td {
            padding: 3pt 8pt;
            font-size: 10pt;
        }

        .tot-lbl {
            text-align: right;
            color: #64748b;
        }

        .tot-val {
            text-align: right;
            font-weight: bold;
            color: #0f172a;
        }

        .tot-grand {
            border-top: 1.5pt solid #1e40af;
            padding-top: 6pt;
        }

        .tot-grand-lbl {
            color: #1e40af;
            font-size: 12pt;
            text-transform: uppercase;
        }

        .tot-grand-val {
            color: #1e40af;
            font-size: 12pt;
        }

        /* RTL support */
        [dir="rtl"] .hdr-left {
            text-align: right;
        }

        [dir="rtl"] .hdr-right {
            text-align: left;
        }

        [dir="rtl"] .meta-lbl {
            text-align: right;
        }

        [dir="rtl"] .items th {
            text-align: right;
        }

        [dir="rtl"] .totals-table {
            margin-left: 0;
            margin-right: auto;
        }

        [dir="rtl"] .tot-lbl,
        [dir="rtl"] .tot-val {
            text-align: left;
        }
    </style>
</head>

<body dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

    <!-- HEADER -->
    <table class="hdr">
        <tr>
            <td class="hdr-left">
                @if(isset($logoBase64) && $logoBase64)
                    <img src="{{ $logoBase64 }}" class="logo-img">
                @elseif($invoice->company?->logo)
                    <img src="{{ public_path('storage/' . $invoice->company->logo) }}" class="logo-img">
                @endif
                <div class="co-vat">VAT Number: {{ $invoice->company?->tax_number }}</div>
                <div class="co-addr">{{ $invoice->branch?->address ?? $invoice->company?->address ?? 'Street Address' }}
                </div>
                <div class="co-addr">{{ $invoice->branch?->phone ?? $invoice->company?->contact_phone ?? 'Phone' }}
                </div>
            </td>
            <td class="hdr-mid">
                <div class="brand-en">{{ strtoupper($invoice->company?->name_en ?? 'BIN AWF AGRICULTURAL') }}</div>
                <div class="brand-ar">{{ $invoice->company_name_ar ?? 'بن عوف الزراعية' }}</div>
            </td>
            <td class="hdr-right">
                <div class="doc-title">VAT Invoice</div>
                <table class="meta">
                    <tr>
                        <td class="meta-lbl">Date:</td>
                        <td>{{ $invoice->invoice_date->format('March d, Y') }}</td>
                    </tr>
                    <tr>
                        <td class="meta-lbl">Invoice #:</td>
                        <td>{{ $invoice->document_number }}</td>
                    </tr>
                    <tr>
                        <td class="meta-lbl">Customer ID:</td>
                        <td>{{ $invoice->customer?->code ?? 'N/A' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- CUSTOMER -->
    <div class="sect-title">Customer Information</div>
    <div class="cust-box">
        <div class="cust-name">
            {{ $invoice->customer_name_ar ?: ($invoice->customer?->company_name ?? $invoice->customer?->name ?? __('sales.cash_customer')) }}
        </div>
        @if($invoice->customer?->tax_number)
            <div style="font-weight: bold; color: #1e40af; font-size: 9pt;">VAT Number: {{ $invoice->customer->tax_number }}
            </div>
        @endif
        <div class="co-addr">{{ $invoice->customer?->address ?? 'Street Address' }}</div>
        <div class="co-addr">
            {{ implode(', ', array_filter([$invoice->customer?->city, $invoice->customer?->state, $invoice->customer?->zip_code])) ?: 'City, ST ZIP Code' }}
        </div>
    </div>

    <!-- ITEMS -->
    <table class="items">
        <thead>
            <tr>
                <th style="width: 15%;">Item Code #</th>
                <th style="width: 45%;">Item Description</th>
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
                        @if(isset($item->product_name_ar))
                            <div class="item-ar">{{ $item->product_name_ar }}</div>
                            <div class="item-en">{{ $item->product?->name_en ?? '-' }}</div>
                        @else
                            <div class="item-ar">{{ $item->product?->name_en ?? $item->product?->name ?? '-' }}</div>
                        @endif
                        @if(isset($item->description_ar))
                            <div class="item-en" style="margin-top: 1pt;">{{ $item->description_ar }}</div>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ number_format($item->quantity, 2) }}</td>
                    <td style="text-align: right;">{{ number_format($item->unit_price, 2) }}</td>
                    <td style="text-align: right;">{{ number_format($item->gross_amount, 2) }}</td>
                </tr>
            @endforeach
            @for($i = count($invoice->items); $i < 10; $i++)
                <tr>
                    <td>&nbsp;</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
        </tbody>
    </table>

    <!-- TOTALS -->
    <table class="totals-table">
        <tr>
            <td class="tot-lbl">Subtotal:</td>
            <td class="tot-val">{{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td class="tot-lbl">Tax:</td>
            <td class="tot-val">{{ number_format($invoice->tax_amount, 2) }}</td>
        </tr>
        @if($invoice->discount_amount > 0)
            <tr>
                <td class="tot-lbl">Discount:</td>
                <td class="tot-val">-{{ number_format($invoice->discount_amount, 2) }}</td>
            </tr>
        @endif
        <tr class="tot-grand">
            <td class="tot-lbl tot-grand-lbl">TOTAL:</td>
            <td class="tot-val tot-grand-val">{{ number_format($invoice->total_amount, 2) }}</td>
        </tr>
    </table>

    <!-- NOTES -->
    @if($invoice->notes)
        <div style="margin-top: 30pt; border-top: 0.5pt solid #cbd5e1; padding-top: 10pt;">
            <div style="font-weight: bold; color: #64748b; font-size: 8pt; text-transform: uppercase;">
                {{ __('sales.notes') }}</div>
            <div style="font-size: 9pt; color: #334155; margin-top: 2pt;">{{ $invoice->notes_ar ?? $invoice->notes }}</div>
        </div>
    @endif

</body>

</html>
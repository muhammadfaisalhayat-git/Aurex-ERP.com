<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('sales.invoice') }} #{{ $invoice->document_number }}</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('{{ storage_path('fonts/DejaVuSans.ttf') }}') format('truetype');
        }

        @page {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11pt;
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
            padding-bottom: 12px;
            border-bottom: 1.5pt solid #e2e8f0;
            margin-bottom: 16px;
        }

        .hdr-left {
            width: 30%;
            vertical-align: top;
            text-align: left;
        }

        .hdr-mid {
            width: 38%;
            vertical-align: top;
            text-align: center;
        }

        .hdr-right {
            width: 32%;
            vertical-align: top;
            text-align: right;
        }

        .logo-img {
            max-height: 65px;
            max-width: 140px;
            margin-bottom: 6px;
            display: block;
        }

        .co-vat {
            font-weight: 700;
            font-size: 10.5pt;
            color: #1e293b;
            margin-top: 6px;
        }

        .co-vat-num {
            font-weight: 700;
            font-size: 10.5pt;
            color: #1e293b;
        }

        .co-addr {
            font-size: 9.5pt;
            color: #475569;
            margin-top: 1px;
        }

        .brand-en {
            font-size: 22pt;
            font-weight: 800;
            color: #1e293b;
            line-height: 1.05;
        }

        .brand-ar {
            font-size: 16pt;
            color: #1e293b;
            margin-top: 4px;
        }

        .doc-title {
            font-size: 22pt;
            font-weight: 800;
            color: #1e40af;
            margin: 0 0 6px 0;
        }

        .meta {
            border-collapse: collapse;
            width: 220px;
            margin-left: auto;
        }

        .meta td {
            border: 1pt solid #cbd5e1;
            padding: 4px 9px;
            font-size: 10pt;
        }

        .meta-lbl {
            background: #f8fafc;
            color: #64748b;
            width: 45%;
            text-align: left;
        }

        .meta-val {
            text-align: right;
        }

        /* ---------- CUSTOMER ---------- */
        .cust-title {
            font-weight: 700;
            color: #1e40af;
            font-size: 11pt;
            border-bottom: 2pt solid #1e40af;
            padding-bottom: 2px;
            margin-bottom: 6px;
            display: block;
            width: 100%;
        }

        .cust-name {
            font-weight: 700;
            font-size: 12pt;
            color: #0f172a;
            margin-bottom: 1px;
        }

        .cust-vat {
            font-weight: 600;
            color: #1e40af;
            font-size: 10.5pt;
            margin-bottom: 2px;
        }

        .cust-addr {
            font-size: 10pt;
            color: #334155;
        }

        /* ---------- ITEMS TABLE ---------- */
        .items {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0 10px;
        }

        .items th {
            border: 2pt solid #1e40af;
            background: #f8fafc;
            padding: 5px 11px;
            font-size: 10pt;
            font-weight: 700;
            color: #1e40af;
            text-align: left;
        }

        .items td {
            border: 1pt solid #cbd5e1;
            padding: 4px 11px;
            font-size: 10pt;
            height: 24px;
            vertical-align: top;
        }

        .item-ar {
            font-weight: 700;
            color: #1e293b;
        }

        .item-en {
            font-size: 9pt;
            color: #64748b;
        }

        /* ---------- TOTALS ---------- */
        .tot {
            width: 240px;
            border-collapse: collapse;
            margin-left: auto;
        }

        .tot td {
            padding: 5px 11px;
            font-size: 10.5pt;
        }

        .tot-lbl {
            text-align: right;
            font-weight: 600;
            color: #64748b;
        }

        .tot-val {
            text-align: right;
            font-weight: 700;
            color: #0f172a;
        }

        .grand td {
            border-top: 2pt solid #1e40af;
            font-size: 14pt !important;
            font-weight: 800;
            color: #1e40af !important;
            padding-top: 8px;
        }

        /* RTL flip */
        [dir="rtl"] .hdr-left {
            text-align: right;
        }

        [dir="rtl"] .hdr-right {
            text-align: left;
        }

        [dir="rtl"] .meta {
            margin-left: 0;
            margin-right: auto;
        }

        [dir="rtl"] .meta-lbl {
            text-align: right;
        }

        [dir="rtl"] .cust-title {
            text-align: right;
        }

        [dir="rtl"] .items th {
            text-align: right;
        }

        [dir="rtl"] .tot {
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

    {{-- ===== HEADER ===== --}}
    <table class="hdr">
        <tr>
            {{-- LEFT: logo + company info --}}
            <td class="hdr-left">
                @if(isset($logoBase64) && $logoBase64)
                    <img src="{{ $logoBase64 }}" class="logo-img">
                @elseif($invoice->company?->logo)
                    <img src="{{ public_path('storage/' . $invoice->company->logo) }}" class="logo-img">
                @endif
                @if($invoice->company?->tax_number)
                    <div class="co-vat">VAT Number:</div>
                    <div class="co-vat-num">{{ $invoice->company->tax_number }}</div>
                @endif
                <div class="co-addr">{{ $invoice->branch?->address ?? $invoice->company?->address ?? 'Street Address' }}
                </div>
                <div class="co-addr">{{ $invoice->branch?->phone ?? $invoice->company?->contact_phone ?? 'Phone' }}
                </div>
            </td>

            {{-- CENTRE: branding EN + AR --}}
            <td class="hdr-mid">
                <div class="brand-en">
                    {{ strtoupper($invoice->company?->name_en ?? $invoice->company?->name ?? 'BIN AWF AGRICULTURAL') }}
                </div>
                <div class="brand-ar">
                    {{ $invoice->company_name_ar ?? $invoice->company?->name_ar ?? 'بن عوف الزراعية' }}</div>
            </td>

            {{-- RIGHT: doc title + meta grid --}}
            <td class="hdr-right">
                <div class="doc-title">VAT Invoice</div>
                <table class="meta">
                    <tr>
                        <td class="meta-lbl">Date:</td>
                        <td class="meta-val">{{ $invoice->invoice_date->format('F d, Y') }}</td>
                    </tr>
                    <tr>
                        <td class="meta-lbl">Invoice #:</td>
                        <td class="meta-val">{{ $invoice->document_number }}</td>
                    </tr>
                    <tr>
                        <td class="meta-lbl">Customer ID:</td>
                        <td class="meta-val">{{ $invoice->customer?->code ?? 'N/A' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ===== CUSTOMER SECTION ===== --}}
    <div style="margin-bottom: 10px;">
        <span class="cust-title">Customer Information</span>
        <div class="cust-name">
            {{ $invoice->customer_name_ar ?: ($invoice->customer?->company_name ?? $invoice->customer?->name ?? __('sales.cash_customer')) }}
        </div>
        @if($invoice->customer?->tax_number)
            <div class="cust-vat">VAT Number: {{ $invoice->customer->tax_number }}</div>
        @endif
        <div class="cust-addr">{{ $invoice->customer?->address ?? 'Street Address' }}</div>
        <div class="cust-addr">
            {{ implode(', ', array_filter([$invoice->customer?->city, $invoice->customer?->state, $invoice->customer?->zip_code])) ?: 'City, ST ZIP Code' }}
        </div>
        @if($invoice->customer?->phone)
            <div class="cust-addr">Phone: {{ $invoice->customer->phone }}</div>
        @endif
    </div>

    {{-- ===== ITEMS TABLE ===== --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width:15%;">Item Code #</th>
                <th style="width:45%;">Item Description</th>
                <th style="width:10%;text-align:center;">Qty</th>
                <th style="width:15%;text-align:right;">Unit Price</th>
                <th style="width:15%;text-align:right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->product?->code ?? $item->product?->sku ?? '-' }}</td>
                    <td>
                        @if($item->product_name_ar ?? $item->product?->name_ar)
                            <div class="item-ar">{{ $item->product_name_ar ?? $item->product->name_ar }}</div>
                            <div class="item-en">{{ $item->product?->name_en ?? '-' }}</div>
                        @else
                            <div class="item-ar">{{ $item->product?->name_en ?? $item->product?->name ?? '-' }}</div>
                        @endif
                        @if($item->description_ar ?? ($item->description && $item->description !== $item->product?->name && $item->description !== $item->product?->name_ar && $item->description !== $item->product?->name_en))
                            <div class="item-en" style="margin-top:2px;">{{ $item->description_ar ?? $item->description }}</div>
                        @endif
                    </td>
                    <td style="text-align:center;">{{ number_format($item->quantity, 2) }}</td>
                    <td style="text-align:right;">{{ number_format($item->unit_price, 2) }}</td>
                    <td style="text-align:right;">{{ number_format($item->gross_amount, 2) }}</td>
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

    {{-- ===== TOTALS ===== --}}
    <table style="width:100%;border-collapse:collapse;">
        <tr>
            <td style="width:60%;"></td>
            <td>
                <table class="tot">
                    <tr>
                        <td class="tot-lbl">{{ __('sales.subtotal') }}:</td>
                        <td class="tot-val">{{ number_format($invoice->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="tot-lbl">{{ __('sales.tax') }}:</td>
                        <td class="tot-val">{{ number_format($invoice->tax_amount, 2) }}</td>
                    </tr>
                    @if($invoice->discount_amount > 0)
                        <tr>
                            <td class="tot-lbl">{{ __('sales.discount') }}:</td>
                            <td class="tot-val">-{{ number_format($invoice->discount_amount, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="grand">
                        <td class="tot-lbl">TOTAL:</td>
                        <td class="tot-val">{{ number_format($invoice->total_amount, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ===== NOTES ===== --}}
    @if($invoice->notes)
        <div style="margin-top:35px;border-top:1pt solid #e2e8f0;padding-top:12px;">
            <div style="font-weight:700;color:#64748b;font-size:9pt;text-transform:uppercase;">{{ __('sales.notes') }}</div>
            <p style="white-space:pre-wrap;color:#334155;font-size:10pt;margin-top:4px;">{{ $invoice->notes }}</p>
        </div>
    @endif

</body>

</html>
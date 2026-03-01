<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.return_invoice') ?? 'Return Invoice' }} #{{ $salesReturn->document_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #334155;
            background: #fff;
            padding: 0px 30px;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none !important;
            }
        }

        .toolbar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .btn {
            padding: 8px 20px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-primary {
            background: #2563eb;
            color: #fff;
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #e2e8f0;
        }

        .document-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
        }

        .header-left {
            flex: 1;
            text-align: left;
        }

        .header-center {
            flex: 1.5;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .header-right {
            flex: 1;
            text-align: right;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .branding-box-en {
            padding: 5px 0;
            width: 100%;
        }

        .branding-box-ar {
            padding: 5px 0;
            width: 100%;
        }

        .branding-box-en h1 {
            font-size: 24pt;
            font-weight: 800;
            margin: 0;
            line-height: 1.1;
            white-space: nowrap;
        }

        .branding-box-ar h2 {
            font-size: 20pt;
            margin: 0;
            line-height: 1.1;
        }

        .company-logo-img {
            max-height: 100px;
            max-width: 150px;
            margin-bottom: 10px;
        }

        .company-info-box {
            font-size: 9pt;
            color: #475569;
            line-height: 1.4;
        }

        .doc-meta-title h2 {
            font-size: 24pt;
            font-weight: 800;
            color: #dc2626;
            margin: 0 0 5px 0;
        }

        .meta-table {
            border-collapse: collapse;
            width: 280px;
        }

        .meta-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 10px;
            font-size: 10pt;
        }

        .meta-label {
            background: #f8fafc;
            color: #64748b;
            width: 40%;
            text-align: left;
        }

        /* Customer Section */
        .customer-section {
            margin-bottom: 10px;
        }

        .customer-title {
            font-weight: 700;
            color: #dc2626;
            border-bottom: 2px solid #dc2626;
            padding-bottom: 2px;
            margin-bottom: 5px;
            display: inline-block;
            width: 100%;
        }

        .customer-details {
            line-height: 1.4;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .items-table th {
            border: 2px solid #dc2626;
            background: #f8fafc;
            padding: 4px 12px;
            font-size: 10pt;
            font-weight: 700;
            color: #dc2626;
            text-align: left;
        }

        .items-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 12px;
            font-size: 10pt;
            height: 25px;
        }

        /* Totals */
        .header-details {
            width: 100%;
            margin-top: 0;
            margin-bottom: 20px;
        }

        .header-details table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 6px 12px;
        }

        .totals-container {
            display: flex;
            justify-content: flex-end;
        }

        .totals-table {
            width: 250px;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 6px 12px;
            font-size: 10pt;
        }

        .total-label {
            text-align: right;
            font-weight: 600;
            color: #64748b;
        }

        .total-value {
            text-align: right;
            font-weight: 700;
            color: #0f172a;
        }

        .grand-total-row td {
            border-top: 2px solid #dc2626;
            font-size: 12pt !important;
            padding-top: 10px;
        }

        /* Themes */
        .theme-minimalist {
            font-family: 'Inter', sans-serif;
            color: #475569;
        }

        .theme-minimalist .branding-top {
            border: none;
            padding-bottom: 5px;
        }

        .theme-minimalist .branding-top h1 {
            font-size: 28pt;
            color: #64748b;
            font-weight: 400;
            letter-spacing: 0.1em;
        }

        .theme-minimalist .branding-top h2 {
            font-size: 20pt;
            color: #94a3b8;
        }

        .theme-minimalist .doc-meta-title h2 {
            color: #64748b;
            font-weight: 400;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e2e8f0;
        }

        .theme-minimalist .customer-title {
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-size: 9pt;
        }

        .theme-minimalist .items-table th {
            background: none;
            border: none;
            border-bottom: 2px solid #64748b;
            color: #64748b;
        }

        .theme-minimalist .items-table td {
            border: none;
            border-bottom: 1px solid #f1f5f9;
        }

        .theme-minimalist .grand-total-row td {
            border-top: 2px solid #64748b;
            color: #334155 !important;
        }

        .theme-indigo {
            color: #1e1b4b;
        }

        .theme-indigo .branding-top {
            background: #1e40af;
            padding: 20px;
            border-radius: 8px;
            border: none;
        }

        .theme-indigo .branding-top h1 {
            color: #ffffff;
        }

        .theme-indigo .branding-top h2 {
            color: #bfdbfe;
        }

        .theme-indigo .doc-meta-title h2 {
            color: #1e40af !important;
            border-left: 5px solid #1e40af;
            padding-left: 10px;
            text-align: left;
        }

        .theme-indigo .customer-title {
            background: #f1f5f9;
            padding: 8px 15px;
            border: none;
            border-left: 5px solid #1e40af;
            color: #1e40af !important;
        }

        .theme-indigo .items-table th {
            background: #1e40af;
            color: #ffffff;
            border: 1px solid #1e40af;
        }

        .theme-indigo .items-table td {
            border: 1px solid #e2e8f0;
        }

        .theme-indigo .grand-total-row td {
            background: #1e40af;
            color: #ffffff !important;
            padding: 10px 15px;
        }

        .theme-elegant {
            color: #2d3748;
        }

        .theme-elegant .branding-top {
            border-bottom: 3px double #cbd5e1;
        }

        .theme-elegant .branding-top h1 {
            font-family: 'Inter', serif;
            color: #0f172a;
            font-style: italic;
        }

        .theme-elegant .doc-meta-title h2 {
            color: #0f172a !important;
            text-transform: none;
            font-weight: 300;
            font-size: 36pt;
        }

        .theme-elegant .customer-title {
            color: #0f172a !important;
            border-bottom: 1px solid #0f172a;
        }

        .theme-elegant .items-table th {
            background: #fdf2f2;
            color: #991b1b;
            border: none;
            border-bottom: 1px solid #991b1b;
        }

        .theme-elegant .items-table tr:nth-child(even) {
            background: #fafafa;
        }

        .theme-elegant .grand-total-row td {
            border-top: 1px solid #0f172a;
            border-bottom: 4px double #0f172a;
        }

        .theme-bold .branding-top {
            border: 10px solid #000;
            padding: 20px;
        }

        .theme-bold .branding-top h1 {
            font-size: 40pt;
            font-weight: 900;
        }

        .theme-bold .doc-meta-title h2 {
            background: #000;
            color: #fff !important;
            padding: 5px 20px;
            display: inline-block;
        }

        .theme-bold .customer-title {
            background: #000;
            color: #fff !important;
            padding: 5px 15px;
        }

        .theme-bold .items-table th {
            background: #000;
            color: #fff;
            border: 2px solid #000;
        }

        .theme-bold .items-table td {
            border: 2px solid #000;
            font-weight: 600;
        }

        .theme-bold .grand-total-row td {
            background: #000;
            color: #fff !important;
        }

        .theme-corporate .branding-top {
            border-left: 10px solid #92400e;
            padding-left: 20px;
            text-align: left;
        }

        .theme-corporate .branding-top h1 {
            color: #451a03;
        }

        .theme-corporate .doc-meta-title h2 {
            color: #92400e !important;
            border-bottom: 2px solid #92400e;
        }

        .theme-corporate .customer-title {
            color: #92400e !important;
            border-left: 3px solid #92400e;
            padding-left: 10px;
        }

        .theme-corporate .items-table th {
            background: #451a03;
            color: #fff;
            border: 1px solid #451a03;
        }

        .theme-corporate .items-table td {
            border: 1px solid #dcdcdc;
        }

        .theme-corporate .grand-total-row td {
            border-top: 3px solid #92400e;
            background: #fff7ed;
            color: #92400e !important;
        }

        [dir="rtl"] .doc-meta-title,
        [dir="rtl"] .meta-label,
        [dir="rtl"] .items-table th {
            text-align: right;
        }

        [dir="rtl"] .total-label,
        [dir="rtl"] .total-value {
            text-align: left;
        }

        [dir="rtl"] .theme-corporate .branding-top {
            border-left: none;
            border-right: 10px solid #92400e;
            padding-left: 0;
            padding-right: 20px;
            text-align: right;
        }

        [dir="rtl"] .theme-indigo .doc-meta-title h2 {
            border-left: none;
            border-right: 5px solid #1e40af;
            padding-left: 0;
            padding-right: 10px;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="toolbar no-print">
        <select class="btn btn-secondary" id="themeSelector" onchange="switchTheme(this.value)">
            <option value="theme-modern">Modern (Default)</option>
            <option value="theme-minimalist">Minimalist</option>
            <option value="theme-indigo">Indigo Professional</option>
            <option value="theme-elegant">Elegant Soft</option>
            <option value="theme-bold">Bold Contrast</option>
            <option value="theme-corporate">Corporate Premium</option>
        </select>
        <button class="btn btn-primary" onclick="window.print()">{{ __('messages.print') }}</button>
        <button class="btn btn-secondary" onclick="window.close()">{{ __('messages.close') }}</button>
    </div>

    <div class="document-container theme-modern" id="printContainer">
        {{-- Header Section --}}
        <div class="header">
            <div class="header-left">
                <div class="logo-section">
                    @if($salesReturn->company?->logo)
                        <img src="{{ asset('storage/' . $salesReturn->company->logo) }}"
                            alt="{{ $salesReturn->company->name_en }}" class="company-logo-img">
                    @endif
                </div>
                <div class="company-info-box">
                    @if($salesReturn->company?->tax_number)
                        <div style="font-weight: 600;">VAT Number: {{ $salesReturn->company->tax_number }}</div>
                    @endif
                    <div>{{ $salesReturn->branch?->address ?? $salesReturn->company?->address ?? 'Street Address' }}
                    </div>
                    <div>{{ $salesReturn->branch?->phone ?? $salesReturn->company?->contact_phone ?? 'Phone' }}</div>
                </div>
            </div>

            <div class="header-center">
                <div class="branding-box-en">
                    <h1>{{ strtoupper($salesReturn->company?->name_en ?? $salesReturn->company?->name ?? 'BIN AWF AGRICULTURAL') }}
                    </h1>
                </div>
                <div class="branding-box-ar">
                    <h2>{{ $salesReturn->company?->name_ar ?? 'بن عوف الزراعية' }}</h2>
                </div>
            </div>

            <div class="header-right">
                <div class="doc-meta-title">
                    <h2 style="color: #dc2626;">Return Invoice</h2>
                </div>
                <table class="meta-table">
                    <tr>
                        <td class="meta-label">Date:</td>
                        <td>{{ $salesReturn->return_date->format('F d, Y') }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Return #:</td>
                        <td>{{ $salesReturn->return_number }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Customer ID:</td>
                        <td>{{ $salesReturn->customer?->code ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Ref. Invoice #</td>
                        <td>{{ $salesReturn->salesInvoice?->document_number ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Customer Section --}}
        <div class="customer-section">
            <div class="customer-title">Customer Information</div>
            <div class="customer-details">
                <div style="font-weight: 700; font-size: 12pt; color: #0f172a;">
                    {{ $salesReturn->customer?->company_name ?? $salesReturn->customer?->name ?? __('messages.walking_customer') }}
                </div>
                @if($salesReturn->customer?->tax_number)
                    <div style="font-weight: 600; color: #dc2626;">VAT Number: {{ $salesReturn->customer->tax_number }}
                    </div>
                @endif
                <div>{{ $salesReturn->customer?->address ?? 'Street Address' }}</div>
                <div>
                    {{ implode(', ', array_filter([$salesReturn->customer?->city, $salesReturn->customer?->state, $salesReturn->customer?->zip_code])) ?: 'City, ST ZIP Code' }}
                </div>
                @if($salesReturn->customer?->phone)
                    <div>Phone: {{ $salesReturn->customer->phone }}</div>
                @endif
            </div>
        </div>

        {{-- Items Table --}}
        <table class="items-table">
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
                @foreach($salesReturn->items as $item)
                    <tr>
                        <td>{{ $item->product?->code ?? $item->product?->sku ?? '-' }}</td>
                        <td>
                            <strong>{{ $item->product?->name_en ?? $item->product?->name ?? '-' }}</strong>
                            @if($item->notes)
                                <div style="font-size: 9pt; color: #64748b; margin-top: 2px;">{{ $item->notes }}</div>
                            @endif
                        </td>
                        <td style="text-align: center;">{{ number_format($item->quantity, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($item->unit_price, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($item->total_amount, 2) }}</td>
                    </tr>
                @endforeach
                @for($i = count($salesReturn->items); $i < 10; $i++)
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

        {{-- Totals Section --}}
        <div class="totals-container">
            <table class="totals-table">
                <tr>
                    <td class="total-label">{{ __('messages.subtotal') }}:</td>
                    <td class="total-value">{{ number_format($salesReturn->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td class="total-label">{{ __('messages.tax') }}:</td>
                    <td class="total-value">{{ number_format($salesReturn->tax_amount, 2) }}</td>
                </tr>
                <tr class="grand-total-row">
                    <td class="total-label" style="color: #dc2626; font-size: 14pt;">TOTAL:</td>
                    <td class="total-value" style="color: #dc2626; font-size: 14pt;">
                        {{ number_format($salesReturn->total_amount, 2) }}
                    </td>
                </tr>
            </table>
        </div>

        {{-- Reason & Notes --}}
        <div style="margin-top: 40px; display: flex; gap: 40px;">
            <div style="flex: 1;">
                <div style="font-weight: 700; color: #64748b; font-size: 9pt; text-transform: uppercase;">Return Reason
                </div>
                <p style="color: #334155; font-size: 10pt; margin-top: 5px;">
                    <strong>{{ $salesReturn->return_reason }}</strong><br>
                    {{ $salesReturn->reason_description }}
                </p>
            </div>
            @if($salesReturn->notes)
                <div style="flex: 1;">
                    <div style="font-weight: 700; color: #64748b; font-size: 9pt; text-transform: uppercase;">
                        {{ __('messages.notes') }}
                    </div>
                    <p style="white-space: pre-wrap; color: #334155; font-size: 10pt; margin-top: 5px;">
                        {{ $salesReturn->notes }}
                    </p>
                </div>
            @endif
        </div>
    </div>

    <script>
        function switchTheme(theme) {
            const container = document.getElementById('printContainer');
            container.className = 'document-container ' + theme;
        }
    </script>
</body>

</html>
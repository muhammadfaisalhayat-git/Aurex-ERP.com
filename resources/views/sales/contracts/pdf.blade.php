<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('messages.sales_contract') ?? 'Sales Contract' }} #{{ $contract->document_number }}</title>
    <style>
        @page {
            margin: 0px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #1e293b;
            margin: 0px;
            padding: 0px;
        }

        /* ---- Document Container ---- */
        .document {
            width: 100%;
        }

        /* Header bar */
        .doc-header {
            background-color: #1e293b;
            color: #ffffff;
            padding: 25px 35px;
            width: 100%;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .company-name-en {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 0.5px;
            color: #ffffff;
        }

        .company-name-ar {
            font-size: 16px;
            margin-top: 5px;
            color: #e2e8f0;
        }

        .company-logo {
            max-height: 55px;
            max-width: 150px;
            margin-bottom: 10px;
        }

        .invoice-label {
            font-size: 26px;
            font-weight: bold;
            text-transform: uppercase;
            color: #ffffff;
            text-align: right;
        }

        .invoice-num {
            font-size: 12px;
            color: #cbd5e1;
            text-align: right;
            margin-top: 5px;
        }

        /* Body */
        .doc-body {
            padding: 30px 35px;
        }

        /* Info boxes */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .info-box {
            width: 48%;
            vertical-align: top;
        }

        .spacer {
            width: 4%;
        }

        .info-box-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #64748b;
            margin-bottom: 8px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 5px;
        }

        .info-box-value {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
        }

        .info-box-sub {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }

        /* Details (right box) */
        .details-container {
            border: 1px solid #f1f5f9;
            border-radius: 6px;
        }

        .detail-row {
            padding: 8px 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .detail-row-last {
            padding: 8px 12px;
        }

        .detail-label {
            color: #64748b;
            font-weight: normal;
        }

        .detail-value {
            font-weight: bold;
            color: #0f172a;
            text-align: right;
        }

        /* Items table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .items-table thead th {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            border-bottom: 2px solid #e2e8f0;
            padding: 10px 12px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
        }

        .items-table tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .product-desc {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 2px;
        }

        /* Totals */
        .totals-container {
            width: 100%;
            margin-top: 20px;
        }

        .totals-table {
            width: 250px;
            float: right;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            border-collapse: collapse;
        }

        .totals-row td {
            padding: 8px 15px;
            font-size: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .totals-row-grand {
            background-color: #2563eb;
            color: #ffffff;
            font-weight: bold;
        }

        .totals-row-grand td {
            color: #ffffff;
            padding: 10px 15px;
            font-size: 14px;
        }

        /* Notes */
        .notes-section {
            background-color: #f8fafc;
            border-left: 4px solid #2563eb;
            padding: 12px 15px;
            margin-top: 30px;
            clear: both;
        }

        .notes-title {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 1px;
            color: #64748b;
            margin-bottom: 5px;
        }

        /* Footer */
        .doc-footer {
            position: absolute;
            bottom: 0px;
            width: 100%;
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 15px 35px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
        }

        .footer-company {
            font-weight: bold;
            font-size: 11px;
            color: #475569;
            margin-bottom: 3px;
        }
    </style>
</head>

<body>
    <div class="document">
        {{-- ===== HEADER ===== --}}
        <div class="doc-header">
            <table class="header-table">
                <tr>
                    <td>
                        <div class="company-branding">
                            @if(isset($logoBase64) && $logoBase64)
                                <img src="{{ $logoBase64 }}" alt="Logo" class="company-logo">
                            @elseif($contract->company?->logo)
                                <img src="{{ public_path('storage/' . $contract->company->logo) }}" alt="Logo"
                                    class="company-logo">
                            @endif
                            <div class="company-name-en">
                                {{ strtoupper($contract->company?->name_en ?? $contract->company?->name ?? config('app.name')) }}
                            </div>
                            @if($contract->company_name_ar_reshaped)
                                <div class="company-name-ar">{{ $contract->company_name_ar_reshaped }}</div>
                            @endif
                        </div>
                    </td>
                    <td style="vertical-align: top;">
                        <div class="invoice-label">{{ __('messages.sales_contract') ?? 'Sales Contract' }}</div>
                        <div class="invoice-num">
                            # {{ $contract->document_number }}<br>
                            {{ $contract->start_date ? $contract->start_date->format('d M Y') : '' }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- ===== BODY ===== --}}
        <div class="doc-body">

            <table class="info-table">
                <tr>
                    {{-- Customer --}}
                    <td class="info-box">
                        <div class="info-box-title">{{ __('messages.customer_info') }}</div>
                        <div class="info-box-value">
                            {{ $contract->customer_name_ar_reshaped ?? ($contract->customer?->name_ar ?? $contract->customer?->name_en) }}
                        </div>
                        @if($contract->customer?->address)
                            <div class="info-box-sub">{{ $contract->customer->address }}</div>
                        @endif
                        @if($contract->customer?->phone)
                            <div class="info-box-sub">{{ __('messages.phone') }}: {{ $contract->customer->phone }}</div>
                        @endif
                    </td>

                    <td class="spacer"></td>

                    {{-- Contract Details --}}
                    <td class="info-box">
                        <div class="details-container">
                            <table width="100%" style="border-collapse: collapse;">
                                <tr class="detail-row">
                                    <td class="detail-label">{{ __('messages.start_date') ?? 'Start Date' }}</td>
                                    <td class="detail-value">
                                        {{ $contract->start_date ? $contract->start_date->format('d M Y') : '-' }}</td>
                                </tr>
                                <tr class="detail-row">
                                    <td class="detail-label">{{ __('messages.end_date') ?? 'End Date' }}</td>
                                    <td class="detail-value">
                                        {{ $contract->end_date ? $contract->end_date->format('d M Y') : '-' }}</td>
                                </tr>
                                <tr class="detail-row">
                                    <td class="detail-label">{{ __('messages.status') }}</td>
                                    <td class="detail-value">{{ ucfirst($contract->status) }}</td>
                                </tr>
                                <tr class="detail-row-last">
                                    <td class="detail-label">{{ __('messages.contract_type') ?? 'Type' }}</td>
                                    <td class="detail-value">{{ ucfirst($contract->contract_type) }}</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>

            {{-- Items Table --}}
            <table class="items-table">
                <thead>
                    <tr>
                        <th width="40%">{{ __('messages.product') }}</th>
                        <th class="text-right" width="10%">{{ __('messages.quantity') }}</th>
                        <th class="text-right" width="15%">{{ __('messages.unit_price') }}</th>
                        <th class="text-right" width="10%">{{ __('messages.tax') }} (%)</th>
                        <th class="text-right" width="25%">{{ __('messages.total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contract->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->product_name_ar_reshaped ?? ($item->product?->name_en ?? $item->product?->name ?? '-') }}</strong>
                                @if($item->notes_ar_reshaped || $item->notes)
                                    <div class="product-desc">{{ $item->notes_ar_reshaped ?? $item->notes }}</div>
                                @endif
                            </td>
                            <td class="text-right">{{ number_format($item->quantity, 2) }}</td>
                            <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-right">{{ number_format($item->tax_rate, 2) }}</td>
                            <td class="text-right">{{ number_format($item->total_amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Totals --}}
            <div class="totals-container">
                <table class="totals-table">
                    <tr class="totals-row">
                        <td class="detail-label">{{ __('messages.subtotal') }}</td>
                        <td class="text-right" style="font-weight: bold;">{{ number_format($contract->subtotal, 2) }}
                        </td>
                    </tr>
                    <tr class="totals-row">
                        <td class="detail-label">{{ __('messages.tax') }}</td>
                        <td class="text-right" style="font-weight: bold;">{{ number_format($contract->tax_amount, 2) }}
                        </td>
                    </tr>
                    <tr class="totals-row-grand">
                        <td>{{ __('messages.contract_value') ?? 'Total Value' }}</td>
                        <td class="text-right">{{ number_format($contract->total_amount, 2) }}</td>
                    </tr>
                </table>
                <div style="clear: both;"></div>
            </div>

            {{-- Terms & Conditions --}}
            @if($contract->terms_conditions)
                <div class="notes-section">
                    <div class="notes-title">{{ __('messages.terms_conditions') }}</div>
                    <p style="color: #475569; margin: 0;">{{ $contract->terms_conditions }}</p>
                </div>
            @endif

            {{-- Notes --}}
            @if($contract->notes_ar_reshaped || $contract->notes)
                <div class="notes-section">
                    <div class="notes-title">{{ __('messages.notes') }}</div>
                    <p style="color: #475569; margin: 0;">{{ $contract->notes_ar_reshaped ?? $contract->notes }}</p>
                </div>
            @endif
        </div>

        {{-- ===== FOOTER ===== --}}
        <div class="doc-footer">
            <div class="footer-company">{{ $contract->company?->name_en ?? config('app.name') }}</div>
            @if($contract->company?->address)
                {{ $contract->company->address }}
                @if($contract->company?->contact_phone) &nbsp;|&nbsp; {{ $contract->company->contact_phone }} @endif
            @endif
        </div>
    </div>
</body>

</html>
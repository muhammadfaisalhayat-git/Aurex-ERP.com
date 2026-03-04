<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('messages.sales_return') ?? 'Sales Return' }} #{{ $salesReturn->document_number }}</title>
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
            border-left: 4px solid #f43f5e;
            /* Red for returns */
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
                            @elseif($salesReturn->company?->logo)
                                <img src="{{ public_path('storage/' . $salesReturn->company->logo) }}" alt="Logo"
                                    class="company-logo">
                            @endif
                            <div class="company-name-en">
                                {{ strtoupper($salesReturn->company?->name_en ?? $salesReturn->company?->name ?? config('app.name')) }}
                            </div>
                            @if($salesReturn->company_name_ar_reshaped)
                                <div class="company-name-ar">{{ $salesReturn->company_name_ar_reshaped }}</div>
                            @endif
                        </div>
                    </td>
                    <td style="vertical-align: top;">
                        <div class="invoice-label">{{ __('messages.sales_return') ?? 'Sales Return' }}</div>
                        <div class="invoice-num">
                            # {{ $salesReturn->document_number }}<br>
                            {{ $salesReturn->return_date->format('d M Y') }}
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
                            {{ $salesReturn->customer_name_ar_reshaped ?? ($salesReturn->customer?->name ?? __('messages.cash_customer')) }}
                        </div>
                        <div class="info-box-sub"><strong>{{ __('messages.address') }}:</strong>
                            {{ $salesReturn->customer?->address ?? '-' }}</div>
                        <div class="info-box-sub"><strong>{{ __('messages.city') }}:</strong>
                            {{ $salesReturn->customer?->city ?? '-' }}</div>
                        <div class="info-box-sub"><strong>{{ __('messages.country') }}:</strong>
                            {{ $salesReturn->customer?->country ?? '-' }}</div>
                        <div class="info-box-sub"><strong>{{ __('messages.phone') }}:</strong>
                            {{ $salesReturn->customer?->phone ?? '-' }}</div>
                        <div class="info-box-sub"><strong>{{ __('messages.email') }}:</strong>
                            {{ $salesReturn->customer?->email ?? '-' }}</div>
                    </td>

                    <td class="spacer"></td>

                    {{-- Return Details --}}
                    <td class="info-box">
                        <div class="details-container">
                            <table width="100%" style="border-collapse: collapse;">
                                <tr class="detail-row">
                                    <td class="detail-label">{{ __('messages.return_type') ?? 'Return Type' }}</td>
                                    <td class="detail-value">{{ ucfirst($salesReturn->return_type) }}</td>
                                </tr>
                                <tr class="detail-row">
                                    <td class="detail-label">{{ __('messages.original_invoice') ?? 'Original Invoice' }}
                                    </td>
                                    <td class="detail-value">{{ $salesReturn->salesInvoice?->document_number ?? '-' }}
                                    </td>
                                </tr>
                                <tr class="detail-row">
                                    <td class="detail-label">{{ __('messages.status') }}</td>
                                    <td class="detail-value">{{ ucfirst($salesReturn->status) }}</td>
                                </tr>
                                <tr class="detail-row-last">
                                    <td class="detail-label">{{ __('messages.branch') }}</td>
                                    <td class="detail-value">{{ $salesReturn->branch?->name_en ?? '-' }}</td>
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
                        <th width="45%">{{ __('messages.product') }}</th>
                        <th class="text-right" width="15%">{{ __('messages.quantity') }}</th>
                        <th class="text-right" width="20%">{{ __('messages.unit_price') }}</th>
                        <th class="text-right" width="20%">{{ __('messages.total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salesReturn->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->product_name_ar_reshaped ?? ($item->product?->name_en ?? $item->product?->name ?? '-') }}</strong>
                                @if($item->description_ar_reshaped || $item->notes)
                                    <div class="product-desc">{{ $item->description_ar_reshaped ?? $item->notes }}</div>
                                @endif
                            </td>
                            <td class="text-right">{{ number_format($item->quantity, 3) }}</td>
                            <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
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
                        <td class="text-right" style="font-weight: bold;">{{ number_format($salesReturn->subtotal, 2) }}
                        </td>
                    </tr>
                    <tr class="totals-row">
                        <td class="detail-label">{{ __('messages.tax') }}</td>
                        <td class="text-right" style="font-weight: bold;">
                            {{ number_format($salesReturn->tax_amount, 2) }}
                        </td>
                    </tr>
                    <tr class="totals-row-grand">
                        <td>{{ __('messages.total_refund') ?? 'Total Refund' }}</td>
                        <td class="text-right">{{ number_format($salesReturn->total_amount, 2) }}</td>
                    </tr>
                </table>
                <div style="clear: both;"></div>
            </div>

            {{-- Return Reason --}}
            @if($salesReturn->return_reason || $salesReturn->reason_description)
                <div class="notes-section">
                    <div class="notes-title">{{ __('messages.return_reason') ?? 'Return Reason' }}</div>
                    <p style="color: #475569; margin: 0;">
                        <strong>{{ $salesReturn->return_reason }}</strong>
                        @if($salesReturn->reason_description)<br>{{ $salesReturn->reason_description }}@endif
                    </p>
                </div>
            @endif

            {{-- Notes --}}
            @if($salesReturn->notes_ar_reshaped || $salesReturn->notes)
                <div class="notes-section">
                    <div class="notes-title">{{ __('messages.notes') }}</div>
                    <p style="color: #475569; margin: 0;">{{ $salesReturn->notes_ar_reshaped ?? $salesReturn->notes }}</p>
                </div>
            @endif
        </div>

        {{-- ===== FOOTER ===== --}}
        <div class="doc-footer">
            <div class="footer-company">{{ $salesReturn->company?->name_en ?? config('app.name') }}</div>
            @if($salesReturn->company?->address)
                {{ $salesReturn->company->address }}
                @if($salesReturn->company?->contact_phone) &nbsp;|&nbsp; {{ $salesReturn->company->contact_phone }} @endif
            @endif
        </div>
    </div>
</body>

</html>
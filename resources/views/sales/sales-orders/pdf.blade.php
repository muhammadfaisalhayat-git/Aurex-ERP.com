<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('messages.sales_order') ?? 'Sales Order' }} #{{ $salesOrder->document_number }}</title>
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
                            @elseif($salesOrder->company?->logo)
                                <img src="{{ public_path('storage/' . $salesOrder->company->logo) }}" alt="Logo"
                                    class="company-logo">
                            @endif
                            <div class="company-name-en">
                                {{ strtoupper($salesOrder->company?->name_en ?? $salesOrder->company?->name ?? config('app.name')) }}
                            </div>
                            @if($salesOrder->company_name_ar_reshaped)
                                <div class="company-name-ar">{{ $salesOrder->company_name_ar_reshaped }}</div>
                            @endif
                        </div>
                    </td>
                    <td style="vertical-align: top;">
                        <div class="invoice-label">{{ __('messages.sales_order') ?? 'Sales Order' }}</div>
                        <div class="invoice-num">
                            # {{ $salesOrder->document_number }}<br>
                            {{ $salesOrder->order_date->format('d M Y') }}
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
                            {{ $salesOrder->customer_name_ar_reshaped ?? ($salesOrder->customer?->name ?? __('messages.cash_customer')) }}
                        </div>
                        @if($salesOrder->customer?->address)
                            <div class="info-box-sub"><strong>{{ __('messages.address') }}:</strong>
                                {{ $salesOrder->customer->address }}</div>
                        @endif
                        <div class="info-box-sub"><strong>{{ __('messages.city') }}:</strong>
                            {{ $salesOrder->customer?->city ?? '-' }}</div>
                        <div class="info-box-sub"><strong>{{ __('messages.country') }}:</strong>
                            {{ $salesOrder->customer?->country ?? '-' }}</div>
                        @if($salesOrder->customer?->phone)
                            <div class="info-box-sub"><strong>{{ __('messages.phone') }}:</strong>
                                {{ $salesOrder->customer->phone }}</div>
                        @endif
                        <div class="info-box-sub"><strong>{{ __('messages.email') }}:</strong>
                            {{ $salesOrder->customer?->email ?? '-' }}</div>
                    </td>

                    <td class="spacer"></td>

                    {{-- Order Details --}}
                    <td class="info-box">
                        <div class="details-container">
                            <table width="100%" style="border-collapse: collapse;">
                                <tr class="detail-row">
                                    <td class="detail-label">{{ __('messages.date') }}</td>
                                    <td class="detail-value">{{ $salesOrder->order_date->format('d M Y') }}</td>
                                </tr>
                                <tr class="detail-row">
                                    <td class="detail-label">
                                        {{ __('messages.expected_delivery_date') ?? 'Expected Delivery' }}
                                    </td>
                                    <td class="detail-value">
                                        {{ $salesOrder->expected_delivery_date ? $salesOrder->expected_delivery_date->format('d M Y') : '-' }}
                                    </td>
                                </tr>
                                <tr class="detail-row">
                                    <td class="detail-label">{{ __('messages.status') }}</td>
                                    <td class="detail-value">{{ ucfirst($salesOrder->status) }}</td>
                                </tr>
                                <tr class="detail-row-last">
                                    <td class="detail-label">{{ __('messages.branch') }}</td>
                                    <td class="detail-value">{{ $salesOrder->branch?->name_en ?? '-' }}</td>
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
                        <th class="text-right" width="15%">{{ __('messages.quantity') }} /
                            {{ __('messages.unit') ?? 'Unit' }}
                        </th>
                        <th class="text-right" width="15%">{{ __('messages.unit_price') }}</th>
                        <th class="text-right" width="10%">{{ __('messages.tax') }} (%)</th>
                        <th class="text-right" width="25%">{{ __('messages.total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salesOrder->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->product_name_ar_reshaped ?? ($item->product?->name_en ?? $item->product?->name ?? '-') }}</strong>
                                @if($item->description_ar_reshaped || $item->description)
                                    <div class="product-desc">{{ $item->description_ar_reshaped ?? $item->description }}</div>
                                @endif
                            </td>
                            <td class="text-right">
                                {{ number_format($item->quantity, 2) }}
                                @if($item->measurementUnit)
                                    <div style="font-size: 8px; color: #64748b;">{{ $item->measurementUnit->name }}</div>
                                @endif
                            </td>
                            <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-right">{{ number_format($item->tax_rate, 2) }}</td>
                            <td class="text-right">{{ number_format($item->gross_amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Totals --}}
            <div class="totals-container">
                <table class="totals-table">
                    <tr class="totals-row">
                        <td class="detail-label">{{ __('messages.subtotal') }}</td>
                        <td class="text-right" style="font-weight: bold;">{{ number_format($salesOrder->subtotal, 2) }}
                        </td>
                    </tr>
                    <tr class="totals-row">
                        <td class="detail-label">{{ __('messages.tax') }}</td>
                        <td class="text-right" style="font-weight: bold;">
                            {{ number_format($salesOrder->tax_amount, 2) }}
                        </td>
                    </tr>
                    @if($salesOrder->shipping_amount > 0)
                        <tr class="totals-row">
                            <td class="detail-label">{{ __('messages.shipping') }}</td>
                            <td class="text-right" style="font-weight: bold;">
                                {{ number_format($salesOrder->shipping_amount, 2) }}
                            </td>
                        </tr>
                    @endif
                    @if($salesOrder->discount_amount > 0)
                        <tr class="totals-row">
                            <td class="detail-label">{{ __('messages.discount') }}</td>
                            <td class="text-right" style="font-weight: bold;">
                                -{{ number_format($salesOrder->discount_amount, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="totals-row-grand">
                        <td>{{ __('messages.grand_total') }}</td>
                        <td class="text-right">{{ number_format($salesOrder->total_amount, 2) }}</td>
                    </tr>
                </table>
                <div style="clear: both;"></div>
            </div>

            {{-- Delivery Address --}}
            @if($salesOrder->delivery_address)
                <div class="notes-section" style="margin-top: 20px;">
                    <div class="notes-title">{{ __('messages.delivery_address') ?? 'Delivery Address' }}</div>
                    <p style="color: #475569; margin: 0;">{{ $salesOrder->delivery_address }}</p>
                </div>
            @endif

            {{-- Terms & Conditions --}}
            @if($salesOrder->terms_conditions)
                <div class="notes-section">
                    <div class="notes-title">{{ __('messages.terms_conditions') }}</div>
                    <p style="color: #475569; margin: 0;">{{ $salesOrder->terms_conditions }}</p>
                </div>
            @endif

            {{-- Notes --}}
            @if($salesOrder->notes_ar_reshaped || $salesOrder->notes)
                <div class="notes-section">
                    <div class="notes-title">{{ __('messages.notes') }}</div>
                    <p style="color: #475569; margin: 0;">{{ $salesOrder->notes_ar_reshaped ?? $salesOrder->notes }}</p>
                </div>
            @endif
        </div>

        {{-- ===== FOOTER ===== --}}
        <div class="doc-footer">
            <div class="footer-company">{{ $salesOrder->company?->name_en ?? config('app.name') }}</div>
            @if($salesOrder->company?->address)
                {{ $salesOrder->company->address }}
                @if($salesOrder->company?->contact_phone) &nbsp;|&nbsp; {{ $salesOrder->company->contact_phone }} @endif
            @endif
        </div>
    </div>
</body>

</html>
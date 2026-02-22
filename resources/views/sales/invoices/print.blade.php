<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('sales.invoice') }} #{{ $invoice->document_number }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 13px;
            line-height: 1.6;
            color: #1e293b;
            background: #fff;
            padding: 32px;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none !important;
            }
        }

        /* ---- Top toolbar ---- */
        .toolbar {
            display: flex;
            gap: 10px;
            margin-bottom: 24px;
        }

        .btn {
            padding: 8px 20px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
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

        .btn:hover {
            opacity: 0.88;
        }

        /* ---- Invoice Document ---- */
        .document {
            max-width: 860px;
            margin: 0 auto;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }

        /* Header bar */
        .doc-header {
            background: #1e293b;
            color: #fff;
            padding: 28px 36px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .company-name-en {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .company-name-ar {
            font-size: 18px;
            margin-top: 4px;
            opacity: 0.85;
            direction: rtl;
        }

        .company-logo {
            max-height: 64px;
            max-width: 160px;
            object-fit: contain;
            border-radius: 6px;
        }

        .invoice-meta {
            text-align:
                {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}
            ;
        }

        .invoice-label {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .invoice-num {
            font-size: 13px;
            opacity: 0.75;
            margin-top: 4px;
        }

        /* Body */
        .doc-body {
            padding: 28px 36px;
        }

        /* Info grid */
        .info-row {
            display: flex;
            gap: 32px;
            margin-bottom: 28px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-box {
            flex: 1;
        }

        .info-box-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #64748b;
            margin-bottom: 10px;
        }

        .info-box-value {
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
        }

        .info-box-sub {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        /* Details table */
        .details-table {
            display: flex;
            gap: 0;
            margin-bottom: 28px;
            border: 1px solid #f1f5f9;
            border-radius: 8px;
            overflow: hidden;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 9px 14px;
            border-bottom: 1px solid #f8fafc;
            font-size: 12.5px;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #64748b;
        }

        .detail-value {
            font-weight: 600;
            color: #0f172a;
        }

        /* Items table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .items-table thead th {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px 14px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
        }

        [dir="rtl"] .items-table thead th {
            text-align: right;
        }

        .items-table tbody td {
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
            vertical-align: top;
        }

        .text-end {
            text-align: right;
        }

        [dir="rtl"] .text-end {
            text-align: left;
        }

        .product-desc {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 2px;
        }

        /* Totals */
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 24px;
        }

        [dir="rtl"] .totals-section {
            justify-content: flex-start;
        }

        .totals-box {
            width: 280px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 9px 16px;
            font-size: 13px;
            border-bottom: 1px solid #f1f5f9;
        }

        .totals-row:last-child {
            background: #2563eb;
            color: #fff;
            font-weight: 700;
            font-size: 15px;
            border-bottom: none;
        }

        /* Notes */
        .notes-section {
            background: #f8fafc;
            border-left: 4px solid #2563eb;
            padding: 14px 18px;
            border-radius: 0 8px 8px 0;
            margin-bottom: 24px;
            font-size: 12.5px;
        }

        [dir="rtl"] .notes-section {
            border-left: none;
            border-right: 4px solid #2563eb;
            border-radius: 8px 0 0 8px;
        }

        .notes-title {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 1px;
            color: #64748b;
            margin-bottom: 6px;
        }

        /* Footer */
        .doc-footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 18px 36px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
        }

        .doc-footer strong {
            display: block;
            font-size: 12px;
            color: #475569;
            margin-bottom: 4px;
        }
    </style>
</head>

<body>
    <div class="toolbar no-print">
        <button class="btn btn-primary" onclick="window.print()">{{ __('common.print') }}</button>
        <button class="btn btn-secondary" onclick="window.close()">{{ __('common.close') }}</button>
    </div>

    <div class="document">
        {{-- ===== HEADER ===== --}}
        <div class="doc-header">
            <div class="company-branding">
                @if($invoice->company?->logo)
                    <img src="{{ asset('storage/' . $invoice->company->logo) }}" alt="{{ $invoice->company->name_en }}"
                        class="company-logo" style="margin-bottom: 10px; display: block;">
                @endif
                <div class="company-name-en">
                    {{ strtoupper($invoice->company?->name_en ?? $invoice->company?->name ?? config('app.name')) }}
                </div>
                @if($invoice->company?->name_ar)
                    <div class="company-name-ar">{{ $invoice->company->name_ar }}</div>
                @endif
            </div>

            <div class="invoice-meta">
                <div class="invoice-label">{{ __('sales.invoice') }}</div>
                <div class="invoice-num">
                    <div># {{ $invoice->document_number }}</div>
                    <div>{{ $invoice->invoice_date->format('d M Y') }}</div>
                </div>
            </div>
        </div>

        {{-- ===== BODY ===== --}}
        <div class="doc-body">

            {{-- Customer + Invoice Details --}}
            <div
                style="display: flex; gap: 32px; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid #f1f5f9;">
                {{-- Customer --}}
                <div style="flex: 1;">
                    <div class="info-box-title">{{ __('sales.customer_info') }}</div>
                    <div class="info-box-value">
                        {{ $invoice->customer?->company_name ?? $invoice->customer?->name ?? __('sales.cash_customer') }}
                    </div>
                    @if($invoice->customer?->address)
                        <div class="info-box-sub">{{ $invoice->customer->address }}</div>
                    @endif
                    @if($invoice->customer?->city || $invoice->customer?->country)
                        <div class="info-box-sub">
                            {{ implode(', ', array_filter([$invoice->customer?->city, $invoice->customer?->country])) }}
                        </div>
                    @endif
                    @if($invoice->customer?->phone)
                        <div class="info-box-sub">{{ __('sales.phone') }}: {{ $invoice->customer->phone }}</div>
                    @endif
                </div>

                {{-- Invoice Details --}}
                <div style="flex: 1; border: 1px solid #f1f5f9; border-radius: 8px; overflow: hidden;">
                    <div class="detail-row">
                        <span class="detail-label">{{ __('sales.payment_terms') }}</span>
                        <span class="detail-value">{{ ucfirst($invoice->payment_terms ?? '-') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">{{ __('sales.due_date') }}</span>
                        <span
                            class="detail-value">{{ $invoice->due_date ? $invoice->due_date->format('d M Y') : '-' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">{{ __('sales.reference_number') }}</span>
                        <span class="detail-value">{{ $invoice->reference_number ?? '-' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">{{ __('sales.branch') }}</span>
                        <span class="detail-value">{{ $invoice->branch?->name_en ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Items Table --}}
            <table class="items-table">
                <thead>
                    <tr>
                        <th width="45%">{{ __('sales.product') }}</th>
                        <th class="text-end">{{ __('sales.quantity') }}</th>
                        <th class="text-end">{{ __('sales.unit_price') }}</th>
                        <th class="text-end">{{ __('sales.total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->product?->name_en ?? $item->product?->name ?? '-' }}</strong>
                                @if($item->product?->name_ar)
                                    <div class="product-desc" style="direction: rtl;">{{ $item->product->name_ar }}</div>
                                @endif
                                @if($item->description && $item->description !== $item->product?->name)
                                    <div class="product-desc">{{ $item->description }}</div>
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($item->quantity, 3) }}</td>
                            <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-end">{{ number_format($item->gross_amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Totals --}}
            <div class="totals-section">
                <div class="totals-box">
                    <div class="totals-row">
                        <span>{{ __('sales.subtotal') }}</span>
                        <span>{{ number_format($invoice->subtotal, 2) }}</span>
                    </div>
                    <div class="totals-row">
                        <span>{{ __('sales.tax') }}</span>
                        <span>{{ number_format($invoice->tax_amount, 2) }}</span>
                    </div>
                    @if($invoice->discount_amount > 0)
                        <div class="totals-row">
                            <span>{{ __('sales.discount') }}</span>
                            <span>-{{ number_format($invoice->discount_amount, 2) }}</span>
                        </div>
                    @endif
                    <div class="totals-row">
                        <span>{{ __('sales.grand_total') }}</span>
                        <span>{{ number_format($invoice->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            @if($invoice->notes)
                <div class="notes-section">
                    <div class="notes-title">{{ __('sales.notes') }}</div>
                    <p style="white-space: pre-wrap; color: #475569;">{{ $invoice->notes }}</p>
                </div>
            @endif
        </div>

        {{-- ===== FOOTER ===== --}}
        <div class="doc-footer">
            <strong>{{ $invoice->company?->name_en ?? config('app.name') }}</strong>
            @if($invoice->company?->address)
                {{ $invoice->company->address }}
                @if($invoice->company?->contact_phone) &nbsp;|&nbsp; {{ $invoice->company->contact_phone }} @endif
            @endif
        </div>
    </div>
</body>

</html>
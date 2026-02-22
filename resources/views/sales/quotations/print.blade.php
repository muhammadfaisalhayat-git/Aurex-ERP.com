<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.quotation') }} #{{ $quotation->document_number }}</title>
    <style>
        body {
            font-family: 'Inter', 'Cairo', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }

        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }

        .company-logo {
            font-size: 28px;
            font-weight: bold;
            color: #2563eb;
        }

        .document-title {
            text-align: right;
        }

        .document-title h1 {
            margin: 0;
            color: #1e293b;
            font-size: 32px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .info-section h3 {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
            font-size: 16px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th {
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }

        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        .text-end {
            text-align: right !important;
        }

        .totals-section {
            margin-left: auto;
            width: 300px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }

        .total-row.grand-total {
            border-top: 2px solid #2563eb;
            margin-top: 10px;
            padding-top: 15px;
            font-weight: bold;
            font-size: 18px;
            color: #2563eb;
        }

        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        [dir="rtl"] .items-table th,
        [dir="rtl"] .items-table td {
            text-align: right;
        }

        [dir="rtl"] .text-end {
            text-align: left !important;
        }

        [dir="rtl"] .totals-section {
            margin-left: 0;
            margin-right: auto;
        }
    </style>
</head>

<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()"
            style="padding: 10px 20px; cursor: pointer;">{{ __('messages.print') }}</button>
        <button onclick="window.close()"
            style="padding: 10px 20px; cursor: pointer;">{{ __('messages.close') }}</button>
    </div>

    <div class="header">
        <div class="company-branding">
            @if($quotation->company?->logo)
                <img src="{{ asset('storage/' . $quotation->company->logo) }}" alt="Logo"
                    style="max-height: 80px; margin-bottom: 10px;"><br>
            @endif
            <div class="company-logo">
                {{ $quotation->company_name_ar ?? $quotation->company?->name ?? config('app.name', 'Aurex ERP') }}
            </div>
        </div>
        <div class="document-title">
            <h1>{{ __('messages.quotation') }}</h1>
            <div># {{ $quotation->document_number }}</div>
            <div>{{ __('messages.date') }}: {{ $quotation->quotation_date->format('Y-m-d') }}</div>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-section">
            <h3>{{ __('messages.customer_information') }}</h3>
            <strong>{{ $quotation->customer_name_ar ?? $quotation->customer?->name_ar ?? $quotation->customer?->name }}</strong><br>
            @if($quotation->customer?->address) {{ $quotation->customer->address }}<br> @endif
            @if($quotation->customer?->phone) {{ $quotation->customer->phone }} @endif
        </div>
        <div class="info-section">
            <h3>{{ __('messages.quotation_details') }}</h3>
            <strong>{{ __('messages.status') }}:</strong> {{ ucfirst($quotation->status) }}<br>
            <strong>{{ __('messages.expiry_date') }}:</strong>
            {{ $quotation->expiry_date ? $quotation->expiry_date->format('Y-m-d') : '' }}<br>
            <strong>{{ __('messages.created_by') }}:</strong> {{ $quotation->creator->name ?? '' }}
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th width="50%">{{ __('messages.product') }}</th>
                <th width="10%" class="text-end">{{ __('messages.quantity') }}</th>
                <th width="20%" class="text-end">{{ __('messages.unit_price') }}</th>
                <th width="20%" class="text-end">{{ __('messages.total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->product_name_ar ?? $item->product->name_en }}</strong>
                    </td>
                    <td class="text-end">{{ number_format($item->quantity, 2) }}</td>
                    <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end">{{ number_format($item->net_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-section">
        <div class="total-row">
            <span>{{ __('messages.subtotal') }}</span>
            <span>{{ number_format($quotation->subtotal, 2) }}</span>
        </div>
        @if($quotation->tax_amount > 0)
            <div class="total-row">
                <span>{{ __('messages.tax') }} ({{ number_format($quotation->tax_rate, 0) }}%)</span>
                <span>{{ number_format($quotation->tax_amount, 2) }}</span>
            </div>
        @endif
        @if($quotation->discount_amount > 0)
            <div class="total-row">
                <span>{{ __('messages.discount') }}</span>
                <span>-{{ number_format($quotation->discount_amount, 2) }}</span>
            </div>
        @endif
        <div class="total-row grand-total">
            <span>{{ __('messages.grand_total') }}</span>
            <span>{{ number_format($quotation->total_amount, 2) }}</span>
        </div>
    </div>

    @if($quotation->terms_conditions)
        <div style="margin-top: 40px;">
            <h3 style="font-size: 16px; color: #64748b; margin-bottom: 10px;">{{ __('messages.terms_conditions') }}</h3>
            <p style="white-space: pre-wrap;">{{ $quotation->terms_conditions_ar ?? $quotation->terms_conditions }}</p>
        </div>
    @endif

    <div class="footer">
        <p>{{ config('app.url') }}</p>
    </div>
</body>

</html>
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.purchase_invoice') }} #{{ $invoice->invoice_number }}</title>
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
            @if($invoice->company?->logo)
                <img src="{{ asset('storage/' . $invoice->company->logo) }}" alt="Logo"
                    style="max-height: 80px; margin-bottom: 10px;"><br>
            @endif
            <div class="company-logo">
                {{ $invoice->company_name_ar_reshaped ?? $invoice->company?->name ?? config('app.name', 'Aurex ERP') }}
            </div>
        </div>
        <div class="document-title">
            <h1>{{ __('messages.purchase_invoice') }}</h1>
            <div># {{ $invoice->invoice_number }}</div>
            <div>{{ __('messages.date') }}: {{ $invoice->invoice_date->format('Y-m-d') }}</div>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-section">
            <h3>{{ __('messages.vendor_information') }}</h3>
            <strong>{{ $invoice->vendor_name_ar_reshaped ?? ($invoice->vendor?->name ?? '') }}</strong><br>
            @if($invoice->vendor?->address) {{ $invoice->vendor->address }}<br> @endif
            @if($invoice->vendor?->phone) {{ $invoice->vendor->phone }}<br> @endif
            @if($invoice->vendor?->tax_number) {{ __('messages.tax_number') }}: {{ $invoice->vendor->tax_number }}
            @endif
        </div>
        <div class="info-section">
            <h3>{{ __('messages.invoice_details') }}</h3>
            <strong>{{ __('messages.status') }}:</strong> {{ ucfirst($invoice->status) }}<br>
            <strong>{{ __('messages.due_date') }}:</strong>
            {{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '' }}<br>
            <strong>{{ __('messages.created_by') }}:</strong> {{ $invoice->creator->name ?? '' }}<br>
            <strong>{{ __('messages.branch') }}:</strong> {{ $invoice->branch->name ?? '' }}
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
            @foreach($invoice->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->product_name_ar_reshaped ?? $item->product->name }}</strong>
                    </td>
                    <td class="text-end">{{ number_format($item->quantity, 2) }}</td>
                    <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end">{{ number_format($item->total_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-section">
        <div class="total-row">
            <span>{{ __('messages.subtotal') }}</span>
            <span>{{ number_format($invoice->subtotal, 2) }}</span>
        </div>
        @if($invoice->tax_amount > 0)
            <div class="total-row">
                <span>{{ __('messages.tax') }}</span>
                <span>{{ number_format($invoice->tax_amount, 2) }}</span>
            </div>
        @endif
        @if($invoice->discount_amount > 0)
            <div class="total-row">
                <span>{{ __('messages.discount') }}</span>
                <span>-{{ number_format($invoice->discount_amount, 2) }}</span>
            </div>
        @endif
        @if($invoice->shipping_amount > 0)
            <div class="total-row">
                <span>{{ __('messages.shipping') }}</span>
                <span>{{ number_format($invoice->shipping_amount, 2) }}</span>
            </div>
        @endif
        <div class="total-row grand-total">
            <span>{{ __('messages.grand_total') }}</span>
            <span>{{ number_format($invoice->total_amount, 2) }}</span>
        </div>
    </div>

    @if($invoice->notes)
        <div style="margin-top: 40px;">
            <h3 style="font-size: 16px; color: #64748b; margin-bottom: 10px;">{{ __('messages.notes') }}</h3>
            <p style="white-space: pre-wrap;">{{ $invoice->notes_ar_reshaped ?? $invoice->notes }}</p>
        </div>
    @endif

    <div class="footer">
        <p>{{ config('app.url') }}</p>
    </div>
</body>

</html>
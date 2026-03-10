<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.supply_order') }} #{{ $supplyOrder->document_number }}</title>
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

        .company-branding {
            width: 50%;
        }

        .company-logo {
            font-size: 28px;
            font-weight: bold;
            color: #2563eb;
        }

        .document-title {
            text-align: right;
            width: 50%;
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

        [dir="rtl"] .document-title {
            text-align: left;
        }
    </style>
</head>

<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()"
            style="padding: 10px 20px; cursor: pointer; background: #2563eb; color: white; border: none; border-radius: 4px;">{{ __('messages.print') }}</button>
        <button onclick="window.close()"
            style="padding: 10px 20px; cursor: pointer; background: #f1f5f9; color: #333; border: 1px solid #ccc; border-radius: 4px; margin-left: 10px;">{{ __('messages.close') }}</button>
    </div>

    <div class="header">
        <div class="company-branding">
            @if($supplyOrder->branch?->company->logo ?? false)
                <img src="{{ asset('storage/' . $supplyOrder->branch->company->logo) }}" alt="Logo"
                    style="max-height: 80px; margin-bottom: 10px;"><br>
            @endif
            <div class="company-logo">
                {{ $supplyOrder->branch?->company->name ?? config('app.name', 'Aurex ERP') }}
            </div>
        </div>
        <div class="document-title">
            <h1>{{ __('messages.supply_order') }}</h1>
            <div># {{ $supplyOrder->document_number }}</div>
            <div>{{ __('messages.date') }}: {{ $supplyOrder->order_date->format('Y-m-d') }}</div>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-section">
            <h3>{{ __('messages.vendor_information') }}</h3>
            <strong>{{ app()->getLocale() == 'ar' ? ($supplyOrder->vendor->name_ar ?? $supplyOrder->vendor->name_en ?? '-') : ($supplyOrder->vendor->name_en ?? '-') }}</strong><br>
            @if($supplyOrder->vendor?->address) {{ $supplyOrder->vendor->address }}<br> @endif
            @if($supplyOrder->vendor?->phone) {{ $supplyOrder->vendor->phone }}<br> @endif
        </div>
        <div class="info-section">
            <h3>{{ __('messages.order_details') }}</h3>
            <strong>{{ __('messages.status') }}:</strong> {{ __('messages.' . $supplyOrder->status) }}<br>
            <strong>{{ __('messages.reference_number') }}:</strong> {{ $supplyOrder->order_number }}<br>
            <strong>{{ __('messages.branch') }}:</strong> {{ $supplyOrder->branch->name ?? '-' }}<br>
            <strong>{{ __('messages.warehouse') }}:</strong> {{ $supplyOrder->warehouse->name ?? '-' }}
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th width="40%">{{ __('messages.product') }}</th>
                <th width="15%" class="text-end">{{ __('messages.quantity') }} / {{ __('messages.unit') ?? 'Unit' }}
                </th>
                <th width="15%" class="text-end">{{ __('messages.unit_price') }}</th>
                <th width="15%" class="text-end">{{ __('messages.discount') }}</th>
                <th width="15%" class="text-end">{{ __('messages.total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($supplyOrder->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->product->name ?? '-' }}</strong><br>
                        <small>{{ $item->product->code ?? '-' }}</small>
                    </td>
                    <td class="text-end">
                        {{ number_format($item->quantity, 2) }}
                        <small style="color: #64748b; font-size: 0.8em;">{{ $item->measurementUnit->name ?? '' }}</small>
                    </td>
                    <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end">{{ number_format($item->discount_amount, 2) }}
                        ({{ number_format($item->discount_percentage, 2) }}%)</td>
                    <td class="text-end">{{ number_format($item->total_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-section">
        <div class="total-row">
            <span>{{ __('messages.subtotal') }}</span>
            <span>{{ number_format($supplyOrder->subtotal, 2) }}</span>
        </div>
        @if($supplyOrder->discount_amount > 0)
            <div class="total-row">
                <span>{{ __('messages.discount') }}</span>
                <span>-{{ number_format($supplyOrder->discount_amount, 2) }}</span>
            </div>
        @endif
        @if($supplyOrder->tax_amount > 0)
            <div class="total-row">
                <span>{{ __('messages.tax') }}</span>
                <span>{{ number_format($supplyOrder->tax_amount, 2) }}</span>
            </div>
        @endif
        @if($supplyOrder->shipping_amount > 0)
            <div class="total-row">
                <span>{{ __('messages.shipping') }}</span>
                <span>{{ number_format($supplyOrder->shipping_amount, 2) }}</span>
            </div>
        @endif
        <div class="total-row grand-total">
            <span>{{ __('messages.total_amount') }}</span>
            <span>{{ number_format($supplyOrder->total_amount, 2) }}</span>
        </div>
    </div>

    @if($supplyOrder->notes)
        <div style="margin-top: 40px;">
            <h3 style="font-size: 16px; color: #64748b; margin-bottom: 10px;">{{ __('messages.notes') }}</h3>
            <p style="white-space: pre-wrap;">{{ $supplyOrder->notes }}</p>
        </div>
    @endif

    @if($supplyOrder->terms_conditions)
        <div style="margin-top: 20px;">
            <h3 style="font-size: 16px; color: #64748b; margin-bottom: 10px;">{{ __('messages.terms_conditions') }}</h3>
            <p style="white-space: pre-wrap;">{{ $supplyOrder->terms_conditions }}</p>
        </div>
    @endif

    <div class="footer">
        <p>{{ config('app.url') }}</p>
    </div>
</body>

</html>
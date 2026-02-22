<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('sales.invoice') }} #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #eee;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
        }

        .invoice-title {
            text-align: right;
            font-size: 28px;
            font-weight: bold;
            color: #1e293b;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            vertical-align: top;
            width: 50%;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #64748b;
            border-bottom: 1px solid #eee;
            margin-bottom: 10px;
            padding-bottom: 5px;
            text-transform: uppercase;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th {
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }

        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .text-right {
            text-align: right;
        }

        .totals-table {
            width: 250px;
            margin-left: auto;
        }

        .totals-table td {
            padding: 5px 0;
        }

        .grand-total {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
            border-top: 2px solid #2563eb;
            padding-top: 10px !important;
        }

        [dir="rtl"] .company-name {
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }
        
        .company-name {
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
            font-family: 'DejaVu Sans', sans-serif;
        }

        .company-branding {
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }

        [dir="rtl"] .invoice-title {
            text-align: left;
        }

        [dir="rtl"] .items-table th,
        [dir="rtl"] .items-table td {
            text-align: right;
        }

        [dir="rtl"] .text-right {
            text-align: left;
        }

        [dir="rtl"] .totals-table {
            margin-left: 0;
            margin-right: auto;
        }
    </style>
</head>

<body>
    <table class="header-table">
        <tr>
            <td class="company-branding">
                @if(isset($logoBase64) && $logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo" style="max-height: 80px; margin-bottom: 10px;"><br>
                @elseif($invoice->company?->logo)
                    <img src="{{ public_path('storage/' . $invoice->company->logo) }}" alt="Logo"
                        style="max-height: 80px; margin-bottom: 10px;"><br>
                @endif
                <div class="company-name" style="font-family: 'DejaVu Sans', sans-serif;">
                    {{ $invoice->company_name_ar ?? $invoice->company?->name ?? config('app.name', 'Aurex ERP') }}
                </div>
            </td>
            <td class="invoice-title">
                {{ __('sales.invoice') }}
                <div style="font-size: 14px; font-weight: normal; color: #666; margin-top: 5px;">
                    #{{ $invoice->document_number }}<br>
                    {{ __('sales.date') }}: {{ $invoice->invoice_date->format('Y-m-d') }}
                </div>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td>
                <div class="section-title">{{ __('sales.customer_info') }}</div>
                <div style="font-family: 'DejaVu Sans', sans-serif;">
                    <strong>{{ $invoice->customer_name_ar ?? $invoice->customer?->name_ar ?? $invoice->customer?->company_name ?? __('sales.cash_customer') }}</strong>
                </div>
                @if($invoice->customer?->address) {{ $invoice->customer->address }}<br> @endif
                @if($invoice->customer?->city || $invoice->customer?->country)
                    {{ $invoice->customer->city }}{{ $invoice->customer->city && $invoice->customer->country ? ', ' : '' }}{{ $invoice->customer->country }}<br>
                @endif
                @if($invoice->customer?->phone) {{ __('sales.phone') }}: {{ $invoice->customer->phone }} @endif
            </td>
            <td>
                <div class="section-title">{{ __('sales.invoice_details') }}</div>
                <table width="100%">
                    <tr>
                        <td>{{ __('sales.payment_terms') }}:</td>
                        <td class="text-right"><strong>{{ ucfirst($invoice->payment_terms) }}</strong></td>
                    </tr>
                    <tr>
                        <td>{{ __('sales.due_date') }}:</td>
                        <td class="text-right">
                            <strong>{{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '-' }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ __('sales.reference_number') }}:</td>
                        <td class="text-right"><strong>{{ $invoice->reference_number }}</strong></td>
                    </tr>
                    <tr>
                        <td>{{ __('sales.branch') }}:</td>
                        <td class="text-right"><strong>{{ $invoice->branch->name_en }}</strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th width="45%">{{ __('sales.product') }}</th>
                <th class="text-right">{{ __('sales.quantity') }}</th>
                <th class="text-right">{{ __('sales.unit_price') }}</th>
                <th class="text-right" style="display: none;">{{ __('sales.tax') }}</th>
                <th class="text-right">{{ __('sales.total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->product_name_ar ?? $item->product->name_ar ?? $item->product->name_en }}</strong>
                        @if($item->description && $item->description !== $item->product->name)
                            <div style="font-size: 10px; color: #666;">{{ $item->description_ar ?? $item->description }}</div>
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($item->quantity, 3) }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right" style="display: none;">{{ number_format($item->tax_amount, 2) }}</td>
                    <td class="text-right">{{ number_format($item->gross_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td>{{ __('sales.subtotal') }}</td>
            <td class="text-right">{{ number_format($invoice->total_amount, 2) }}</td>
        </tr>
        <tr>
            <td>{{ __('sales.tax') }}</td>
            <td class="text-right">{{ number_format($invoice->tax_amount, 2) }}</td>
        </tr>
        @if($invoice->discount_amount > 0)
            <tr>
                <td>{{ __('sales.discount') }}</td>
                <td class="text-right">-{{ number_format($invoice->discount_amount, 2) }}</td>
            </tr>
        @endif
        <tr>
            <td class="grand-total">{{ __('sales.grand_total') }}</td>
            <td class="grand-total text-right">{{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
    </table>

    @if($invoice->notes)
        <div style="margin-top: 30px;">
            <div class="section-title">{{ __('sales.notes') }}</div>
            <p style="font-size: 11px; color: #666;">{{ $invoice->notes_ar ?? $invoice->notes }}</p>
        </div>
    @endif

    <div
        style="margin-top: 50px; text-align: center; font-size: 10px; color: #94a3b8; border-top: 1px solid #eee; padding-top: 10px;">
        <p>{{ __('messages.thank_you_for_business') }}</p>
        <p>{{ config('app.url') }}</p>
    </div>
</body>

</html>
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('messages.stock_supply') }} #{{ $supply->document_number }}</title>
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

        .document-title {
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

        .company-branding {
            text-align:
                {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}
            ;
        }

        [dir="rtl"] .document-title {
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

<body dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <table class="header-table">
        <tr>
            <td class="company-branding">
                @if(isset($logoBase64) && $logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo" style="max-height: 80px; margin-bottom: 10px;"><br>
                @elseif($supply->company?->logo)
                    <img src="{{ public_path('storage/' . $supply->company->logo) }}" alt="Logo"
                        style="max-height: 80px; margin-bottom: 10px;"><br>
                @endif
                <div class="company-name">
                    {{ $supply->company_name_ar ?? $supply->company?->name ?? config('app.name', 'Aurex ERP') }}
                </div>
            </td>
            <td class="document-title">
                {{ __('messages.stock_supply') }}
                <div style="font-size: 14px; font-weight: normal; color: #666; margin-top: 5px;">
                    #{{ $supply->document_number }}<br>
                    {{ __('messages.date') }}: {{ $supply->supply_date->format('Y-m-d') }}
                </div>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td>
                <div class="section-title">{{ __('messages.vendor_info') }}</div>
                <div>
                    <strong>{{ $supply->vendor_name_ar ?? $supply->vendor?->name_ar ?? $supply->vendor?->name_en ?? '-' }}</strong>
                </div>
                @if($supply->vendor?->address) {{ $supply->vendor->address }}<br> @endif
                @if($supply->vendor?->city || $supply->vendor?->country)
                    {{ $supply->vendor->city }}{{ $supply->vendor->city && $supply->vendor->country ? ', ' : '' }}{{ $supply->vendor->country }}<br>
                @endif
                @if($supply->vendor?->phone) {{ __('messages.phone') }}: {{ $supply->vendor->phone }} @endif
            </td>
            <td>
                <div class="section-title">{{ __('messages.details') }}</div>
                <table width="100%">
                    <tr>
                        <td>{{ __('messages.warehouse') }}:</td>
                        <td class="text-right"><strong>{{ $supply->warehouse->name ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td>{{ __('messages.reference_number') }}:</td>
                        <td class="text-right"><strong>{{ $supply->reference_number ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td>{{ __('messages.status') }}:</td>
                        <td class="text-right"><strong>{{ ucfirst($supply->status) }}</strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th width="45%">{{ __('messages.product') }}</th>
                <th class="text-right">{{ __('messages.quantity') }}</th>
                <th class="text-right">{{ __('messages.unit_cost') }}</th>
                <th class="text-right">{{ __('messages.total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($supply->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->product_name_ar ?? $item->product?->name_ar ?? $item->product?->name_en }}</strong>
                    </td>
                    <td class="text-right">{{ number_format($item->quantity, 3) }}</td>
                    <td class="text-right">{{ number_format($item->unit_cost, 2) }}</td>
                    <td class="text-right">{{ number_format($item->total_cost, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td class="grand-total">{{ __('messages.grand_total') }}</td>
            <td class="grand-total text-right">{{ number_format($supply->total_amount, 2) }}</td>
        </tr>
    </table>

    @if($supply->notes)
        <div style="margin-top: 30px;">
            <div class="section-title">{{ __('messages.notes') }}</div>
            <p style="font-size: 11px; color: #666;">{{ $supply->notes_ar ?? $supply->notes }}</p>
        </div>
    @endif

    <div
        style="margin-top: 50px; text-align: center; font-size: 10px; color: #94a3b8; border-top: 1px solid #eee; padding-top: 10px;">
        <p>{{ config('app.url') }}</p>
    </div>
</body>

</html>
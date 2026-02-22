<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <title>Quotation {{ $quotation->document_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .details-table td {
            padding: 5px;
            vertical-align: top;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .items-table th {
            background-color: #f8f9fa;
        }

        .text-end {
            text-align: right;
        }

        .fw-bold {
            font-weight: bold;
        }

        .mb-4 {
            margin-bottom: 1.5rem;
        }

        [dir="rtl"] .details-table td:nth-child(2) {
            text-align: left !important;
        }

        [dir="rtl"] .text-end {
            text-align: left !important;
        }

        [dir="rtl"] .items-table th,
        [dir="rtl"] .items-table td {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header" style="position: relative; min-height: 100px;">
        <div
            style="float: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};">
            @if(isset($logoBase64) && $logoBase64)
                <img src="{{ $logoBase64 }}" alt="Logo" style="max-height: 80px;"><br>
            @elseif($quotation->company?->logo)
                <img src="{{ public_path('storage/' . $quotation->company->logo) }}" alt="Logo"
                    style="max-height: 80px;"><br>
            @endif
            <div style="font-size: 20px; font-weight: bold; color: #333; font-family: 'DejaVu Sans', sans-serif;">
                {{ $quotation->company_name_ar ?? $quotation->company?->name ?? config('app.name', 'Aurex ERP') }}
            </div>
        </div>
        <div
            style="float: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};">
            <h2 style="margin: 0; color: #2563eb;">{{ __('messages.quotation') }}</h2>
            <p style="margin: 5px 0;">#{{ $quotation->document_number }}</p>
        </div>
        <div style="clear: both;"></div>
    </div>

    <table class="details-table">
        <tr>
            <td style="width: 50%; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};">
                <h3 class="fw-bold">{{ __('messages.customer_details') }}</h3>
                <div style="font-family: 'DejaVu Sans', sans-serif;">
                    <p>{{ $quotation->customer_name_ar ?? $quotation->customer?->name_ar ?? $quotation->customer?->name_en }}<br>
                        @if($quotation->customer?->address) {{ $quotation->customer->address }}<br> @endif
                        @if($quotation->customer?->phone) {{ $quotation->customer->phone }}<br> @endif
                        @if($quotation->customer?->email) {{ $quotation->customer->email }} @endif
                    </p>
                </div>
            </td>
            <td style="width: 50%; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};">
                <h3 class="fw-bold">{{ __('messages.quotation_details') }}</h3>
                <p><strong>{{ __('messages.date') }}:</strong> {{ $quotation->quotation_date->format('Y-m-d') }}<br>
                    <strong>{{ __('messages.expiry_date') }}:</strong>
                    {{ $quotation->expiry_date->format('Y-m-d') }}<br>
                    <strong>{{ __('messages.status') }}:</strong> {{ ucfirst($quotation->status) }}
                </p>
            </td>
        </tr>
    </table>

    <table class="items-table mb-4">
        <thead>
            <tr>
                <th>{{ __('messages.product') }}</th>
                <th class="text-end">{{ __('messages.quantity') }}</th>
                <th class="text-end">{{ __('messages.unit_price') }}</th>
                <th class="text-end">{{ __('messages.tax') }} (%)</th>
                <th class="text-end">{{ __('messages.tax_amount') }}</th>
                <th class="text-end">{{ __('messages.net_amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->items as $item)
                <tr>
                    <td>{{ $item->product_name_ar ?? ($item->product ? $item->product->name_en : '') }}</td>
                    <td class="text-end">{{ number_format($item->quantity, 2) }}</td>
                    <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end">{{ number_format($item->tax_rate, 2) }}</td>
                    <td class="text-end">{{ number_format($item->tax_amount, 2) }}</td>
                    <td class="text-end">{{ number_format($item->net_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-end fw-bold">{{ __('messages.subtotal') }}</td>
                <td class="text-end fw-bold">{{ number_format($quotation->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td colspan="5" class="text-end fw-bold">{{ __('messages.tax_amount') }}</td>
                <td class="text-end fw-bold">{{ number_format($quotation->tax_amount, 2) }}</td>
            </tr>
            <tr>
                <td colspan="5" class="text-end fw-bold">{{ __('messages.grand_total') }}</td>
                <td class="text-end fw-bold">{{ number_format($quotation->total_amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    @if($quotation->terms_conditions)
        <div class="mb-4">
            <h4 class="fw-bold">{{ __('messages.terms_conditions') }}</h4>
            <p>{{ $quotation->terms_conditions_ar ?? $quotation->terms_conditions }}</p>
        </div>
    @endif

    @if($quotation->notes)
        <div class="mb-4">
            <h4 class="fw-bold">{{ __('messages.notes') }}</h4>
            <p>{{ $quotation->notes_ar ?? $quotation->notes }}</p>
        </div>
    @endif

    <div class="footer">
        <p>Generated by Aurex ERP on {{ date('Y-m-d H:i:s') }}</p>
    </div>
</body>

</html>
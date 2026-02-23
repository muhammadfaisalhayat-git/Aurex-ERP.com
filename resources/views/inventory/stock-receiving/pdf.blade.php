<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        @page {
            margin: 100px 25px;
        }

        body {
            font-family: 'Cairo', 'DejaVu Sans', sans-serif;
            font-size: 12px;
        }

        .header {
            position: fixed;
            top: -80px;
            left: 0;
            right: 0;
            height: 80px;
            border-bottom: 2px solid #333;
        }

        .footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .logo {
            height: 60px;
        }

        .company-info {
            float: left;
            text-align: left;
        }

        .document-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
            color: #1e293b;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 5px;
            vertical-align: top;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .items-table th {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 10px;
            text-align: center;
        }

        .items-table td {
            border: 1px solid #e2e8f0;
            padding: 10px;
        }

        .text-end {
            text-align: left;
        }

        /* For RTL, text-end is left */
        .total-section {
            margin-top: 30px;
            float: left;
            width: 300px;
        }

        .signature-section {
            margin-top: 50px;
            width: 100%;
        }

        .signature-box {
            width: 45%;
            display: inline-block;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        @if($logoBase64)
            <img src="{{ $logoBase64 }}" class="logo">
        @else
            <h2 style="margin:0;">{{ $receiving->company_name_ar ?? $receiving->company->name ?? 'Aurex ERP' }}</h2>
        @endif
        <div class="company-info" style="float: right;">
            <p style="margin:0;">
                <strong>{{ $receiving->company_name_ar ?? $receiving->company->name ?? 'Aurex ERP' }}</strong></p>
            <p style="margin:0;">{{ $receiving->company->address ?? '' }}</p>
            <p style="margin:0;">{{ $receiving->company->phone ?? '' }}</p>
        </div>
    </div>

    <div class="footer">
        <p>{{ __('messages.page') }} <span class="pagenum"></span> | {{ $receiving->document_number }}</p>
    </div>

    <div class="document-title">
        {{ __('messages.stock_receiving') }}
    </div>

    <table class="info-table">
        <tr>
            <td style="width: 50%;">
                <strong>{{ __('messages.vendor') }}:</strong>
                {{ $receiving->vendor_name_ar ?? $receiving->vendor->name }}<br>
                <strong>{{ __('messages.warehouse') }}:</strong> {{ $receiving->warehouse->name }}
            </td>
            <td style="width: 50%; text-align: left;">
                <strong>{{ __('messages.document_number') }}:</strong> {{ $receiving->document_number }}<br>
                <strong>{{ __('messages.date') }}:</strong> {{ $receiving->receiving_date->format('Y-m-d') }}<br>
                <strong>{{ __('messages.reference_number') }}:</strong> {{ $receiving->purchase_order_number ?? '-' }}
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 50%; text-align: right;">{{ __('messages.product') }}</th>
                <th style="width: 15%;">{{ __('messages.ordered') }}</th>
                <th style="width: 15%;">{{ __('messages.received') }}</th>
                <th style="width: 15%;">{{ __('messages.notes') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($receiving->items as $index => $item)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $item->product_name_ar ?? $item->product->name }}</td>
                    <td style="text-align: center;">{{ number_format($item->ordered_quantity, 3) }}</td>
                    <td style="text-align: center;">{{ number_format($item->received_quantity, 3) }}</td>
                    <td>{{ $item->notes ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($receiving->notes)
        <div style="margin-top: 20px;">
            <strong>{{ __('messages.notes') }}:</strong>
            <p>{{ $receiving->notes }}</p>
        </div>
    @endif

    <div class="signature-section">
        <div class="signature-box" style="float: right;">
            <p>____________________</p>
            <p>{{ __('messages.prepared_by') }}</p>
            <p>{{ $receiving->creator->name ?? '' }}</p>
        </div>
        <div class="signature-box" style="float: left;">
            <p>____________________</p>
            <p>{{ __('messages.received_by') }}</p>
            <p>{{ $receiving->receiver->name ?? '................' }}</p>
        </div>
    </div>
</body>

</html>
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
            <h2 style="margin:0;">{{ $issueOrder->company_name_ar ?? $issueOrder->company->name ?? 'Aurex ERP' }}</h2>
        @endif
        <div class="company-info" style="float: right;">
            <p style="margin:0;">
                <strong>{{ $issueOrder->company_name_ar ?? $issueOrder->company->name ?? 'Aurex ERP' }}</strong>
            </p>
            <p style="margin:0;">{{ $issueOrder->company->address ?? '' }}</p>
            <p style="margin:0;">{{ $issueOrder->company->phone ?? '' }}</p>
        </div>
    </div>

    <div class="footer">
        <p>{{ __('messages.page') }} <span class="pagenum"></span> | {{ $issueOrder->document_number }}</p>
    </div>

    <div class="document-title">
        {{ __('messages.stock_issue_order') }}
    </div>

    <table class="info-table">
        <tr>
            <td style="width: 50%;">
                <strong>{{ __('messages.warehouse') }}:</strong> {{ $issueOrder->warehouse->name }}<br>
                <strong>{{ __('messages.issue_type') }}:</strong> {{ __('messages.' . $issueOrder->issue_type) }}<br>
                @if($issueOrder->customer)
                    <strong>{{ __('messages.customer') }}:</strong> {{ $issueOrder->customer->name }}
                @elseif($issueOrder->vendor)
                    <strong>{{ __('messages.vendor') }}:</strong> {{ $issueOrder->vendor->name }}
                @endif
            </td>
            <td style="width: 50%; text-align: left;">
                <strong>{{ __('messages.document_number') }}:</strong> {{ $issueOrder->document_number }}<br>
                <strong>{{ __('messages.date') }}:</strong> {{ $issueOrder->issue_date->format('Y-m-d') }}<br>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 60%; text-align: right;">{{ __('messages.product') }}</th>
                <th style="width: 15%;">{{ __('messages.quantity') }}</th>
                <th style="width: 20%;">{{ __('messages.notes') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($issueOrder->items as $index => $item)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $item->product_name_ar ?? $item->product->name }}</td>
                    <td style="text-align: center;">
                        {{ number_format($item->quantity, 3) }}
                        <span
                            style="font-size: 10px;">{{ $item->measurementUnit->name ?? $item->product->unit_of_measure ?? '' }}</span>
                    </td>
                    <td>{{ $item->notes ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($issueOrder->notes)
        <div style="margin-top: 20px;">
            <strong>{{ __('messages.notes') }}:</strong>
            <p>{{ $issueOrder->notes }}</p>
        </div>
    @endif

    <div class="signature-section">
        <div class="signature-box" style="float: right;">
            <p>____________________</p>
            <p>{{ __('messages.prepared_by') }}</p>
            <p>{{ $issueOrder->creator->name ?? '' }}</p>
        </div>
        <div class="signature-box" style="float: left;">
            <p>____________________</p>
            <p>{{ __('messages.approved_by') }}</p>
            <p>{{ $issueOrder->poster->name ?? '................' }}</p>
        </div>
    </div>
</body>

</html>
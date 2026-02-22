<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Customer Request - {{ $customerRequest->document_number }}</title>
    <style>
        @if(app()->getLocale() == 'ar')
            body {
                font-family: 'DejaVu Sans', sans-serif;
                direction: rtl;
            }

        @else body {
                font-family: 'Helvetica', sans-serif;
                direction: ltr;
            }

        @endif .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #1a5632;
        }

        .info-section {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-box {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
            color: #555;
            text-transform: uppercase;
            font-size: 13px;
            margin-bottom: 5px;
            display: block;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        {{ app()->getLocale() == 'ar' ? 'th, td { text-align: right; }' : '' }}
        th {
            background-color: #f8f9fa;
            color: #333;
            text-transform: uppercase;
            font-size: 12px;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 11px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="company-name">AUREX ERP</div>
        <div style="font-size: 18px; margin-top: 5px; color: #555;">Customer Request</div>
    </div>

    <div class="info-section">
        <div class="info-box">
            <div class="label">Request Details</div>
            <div style="margin-top: 5px; line-height: 1.6;">
                <strong>Document Number:</strong> {{ $customerRequest->document_number }}<br>
                <strong>Date:</strong> {{ $customerRequest->request_date->format('Y-m-d') }}<br>
                <strong>Needed By:</strong>
                {{ $customerRequest->needed_date ? $customerRequest->needed_date->format('Y-m-d') : '' }}<br>
                <strong>Status:</strong> {{ ucfirst($customerRequest->status) }}
            </div>
        </div>
        <div class="info-box" style="{{ app()->getLocale() == 'ar' ? 'text-align: left;' : 'text-align: right;' }}">
            <div class="label">Customer Information</div>
            <div style="margin-top: 5px; line-height: 1.6;">
                <strong
                    style="font-size: 16px;">{{ $customerRequest?->customer_name_ar ?? $customerRequest->customer?->name_ar ?? $customerRequest?->customer?->name ?? '' }}</strong><br>
                {{ $customerRequest?->branch?->name ?? '' }}
            </div>
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th width="35%">Product</th>
                <th width="10%">Quantity</th>
                <th width="15%">Unit Price</th>
                <th width="10%">Tax %</th>
                <th width="15%">Tax Amount</th>
                <th width="15%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customerRequest->items as $item)
                <tr>
                    <td>{{ $item->product_name_ar ?? (app()->getLocale() == 'ar' ? ($item->product->name_ar ?? $item->product->name_en) : ($item->product->name_en ?? 'Unknown Product')) }}
                    </td>
                    <td style="text-align: center;">{{ number_format($item->quantity, 2) }}</td>
                    <td style="text-align: right;">{{ number_format($item->unit_price, 2) }}</td>
                    <td style="text-align: center;">{{ number_format($item->tax_rate, 2) }}%</td>
                    <td style="text-align: right;">{{ number_format($item->tax_amount, 2) }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format($item->total_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px; width: 100%;">
        <div style="width: 40%; {{ app()->getLocale() == 'ar' ? 'float: left;' : 'float: right;' }}">
            <table class="summary-table" style="border: none;">
                <tr style="border: none;">
                    <td style="border: none; padding: 5px; font-weight: bold;">Subtotal:</td>
                    <td style="border: none; padding: 5px; text-align: right;">
                        {{ number_format($customerRequest->subtotal, 2) }}
                    </td>
                </tr>
                <tr style="border: none;">
                    <td style="border: none; padding: 5px; font-weight: bold;">Tax Amount:</td>
                    <td style="border: none; padding: 5px; text-align: right;">
                        {{ number_format($customerRequest->tax_amount, 2) }}
                    </td>
                </tr>
                <tr style="border: none; background-color: #f8f9fa;">
                    <td style="border: none; padding: 5px; font-weight: bold; font-size: 14px;">Total Amount:</td>
                    <td
                        style="border: none; padding: 5px; text-align: right; font-weight: bold; font-size: 14px; border-top: 1px solid #333;">
                        {{ number_format($customerRequest->total_amount, 2) }}
                    </td>
                </tr>
            </table>
        </div>
        <div style="clear: both;"></div>
    </div>

    @if($customerRequest->notes)
        <div style="margin-top: 30px;">
            <div class="label">Notes:</div>
            <div style="margin-top: 5px; padding: 15px; border: 1px solid #eee; background-color: #fafafa; color: #444;">
                {{ $customerRequest->notes_ar ?? $customerRequest->notes }}
            </div>
        </div>
    @endif

    <div class="footer">
        Generated by: {{ $customerRequest->creator->name ?? 'System' }} |
        Generated on: {{ date('Y-m-d H:i') }}
    </div>
</body>

</html>
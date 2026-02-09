<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ __('sales.invoice') }} #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
        }

        .company-info {
            float: left;
            width: 50%;
        }

        .invoice-info {
            float: right;
            width: 40%;
            text-align: right;
        }

        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }

        .customer-info {
            margin-bottom: 30px;
            clear: both;
        }

        .billing-address {
            float: left;
            width: 45%;
        }

        .shipping-address {
            float: right;
            width: 45%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }

        .text-right {
            text-align: right;
        }

        .totals {
            width: 40%;
            float: right;
        }

        .totals table tr td:first-child {
            font-weight: bold;
        }

        .footer {
            margin-top: 50px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
            font-size: 10px;
            text-align: center;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="company-info">
            {{-- <img src="{{ public_path('images/logo.png') }}" class="logo"> --}}
            <h2>Aurex ERP</h2>
            <p>
                123 Business Street<br>
                Riyadh, Saudi Arabia<br>
                Phone: +966 12 345 6789<br>
                Email: info@aurex-erp.com
            </p>
        </div>
        <div class="invoice-info">
            <h1>{{ __('sales.invoice') }}</h1>
            <p>
                <strong>{{ __('sales.invoice_number') }}:</strong> {{ $invoice->invoice_number }}<br>
                <strong>{{ __('sales.date') }}:</strong> {{ $invoice->invoice_date->format('Y-m-d') }}<br>
                <strong>{{ __('sales.due_date') }}:</strong>
                {{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '-' }}<br>
                <strong>{{ __('sales.status') }}:</strong> {{ ucfirst($invoice->status) }}
            </p>
        </div>
    </div>

    <div class="customer-info">
        <div class="billing-address">
            <h3>{{ __('sales.bill_to') }}:</h3>
            <p>
                <strong>{{ $invoice->customer->company_name }}</strong><br>
                {{ $invoice->customer->address }}<br>
                {{ $invoice->customer->city }}, {{ $invoice->customer->country }}<br>
                {{ __('sales.tax_number') }}: {{ $invoice->customer->tax_number }}
            </p>
        </div>
    </div>

    <div style="clear: both;"></div>

    <table class="items-table">
        <thead>
            <tr>
                <th>{{ __('sales.item') }}</th>
                <th class="text-right">{{ __('sales.quantity') }}</th>
                <th class="text-right">{{ __('sales.unit_price') }}</th>
                <th class="text-right">{{ __('sales.tax') }}</th>
                <th class="text-right">{{ __('sales.total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->product->name }}</strong>
                        @if($item->description)
                            <br><small>{{ $item->description }}</small>
                        @endif
                    </td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">{{ number_format($item->tax_amount, 2) }}</td>
                    <td class="text-right">{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td>{{ __('sales.subtotal') }}:</td>
                <td class="text-right">{{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td>{{ __('sales.tax') }}:</td>
                <td class="text-right">{{ number_format($invoice->tax_amount, 2) }}</td>
            </tr>
            @if($invoice->discount_amount > 0)
                <tr>
                    <td>{{ __('sales.discount') }}:</td>
                    <td class="text-right">-{{ number_format($invoice->discount_amount, 2) }}</td>
                </tr>
            @endif
            <tr style="font-size: 14px; background-color: #f8f9fa;">
                <td>{{ __('sales.grand_total') }}:</td>
                <td class="text-right"><strong>{{ number_format($invoice->grand_total, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    @if($invoice->notes)
        <div class="notes">
            <h3>{{ __('sales.notes') }}:</h3>
            <p>{{ $invoice->notes }}</p>
        </div>
    @endif

    @if($invoice->terms)
        <div class="terms">
            <h3>{{ __('sales.terms_conditions') }}:</h3>
            <p>{{ $invoice->terms }}</p>
        </div>
    @endif

    <div class="footer">
        <p>{{ __('sales.invoice_generated_on') }} {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>

</html>
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.journal_voucher') }} #{{ $jv->voucher_number }}</title>
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

        .signature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 40px;
            margin-top: 60px;
        }

        .signature-box {
            text-align: center;
            border-top: 1px solid #333;
            padding-top: 10px;
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

        .badge {
            display: inline-block;
            padding: 0.25em 0.6em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }

        .bg-light {
            background-color: #f8fafc !important;
        }

        .text-dark {
            color: #1e293b !important;
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
        <div class="company-info" style="display: flex; align-items: center; gap: 20px;">
            @if(isset($jv->company->logo) && $jv->company->logo)
                <img src="{{ asset('storage/' . $jv->company->logo) }}" alt="Logo"
                    style="max-height: 80px; max-width: 200px;">
            @endif
            <div class="company-logo">
                {{ $jv->company->name ?? config('app.name', 'Aurex ERP') }}
                @if(isset($jv->branch))
                    <div style="font-size: 14px; color: #64748b; font-weight: normal; margin-top: 5px;">
                        {{ $jv->branch->name }}
                    </div>
                @endif
            </div>
        </div>
        <div class="document-title">
            <h1>{{ __('messages.journal_voucher') }}</h1>
            <div># {{ $jv->voucher_number }}</div>
            <div>{{ __('messages.date') }}: {{ $jv->voucher_date->format('Y-m-d') }}</div>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-section">
            <h3>{{ __('messages.general_information') }}</h3>
            <strong>{{ __('messages.status') }}:</strong> {{ ucfirst($jv->status) }}<br>
            <strong>{{ __('messages.created_by') }}:</strong> {{ $jv->creator->name ?? '-' }}<br>
            <strong>{{ __('messages.description') }}:</strong> {{ $jv->description ?? '-' }}
        </div>
        <div class="info-section">
            <h3>{{ __('messages.audit_details') }}</h3>
            <strong>{{ __('messages.reference_no') }}:</strong> {{ $jv->reference_no ?? '-' }}<br>
            <strong>{{ __('messages.created_at') }}:</strong> {{ $jv->created_at->format('Y-m-d H:i') }}<br>
            @if($jv->approved_by)
                <strong>{{ __('messages.approved_by') }}:</strong> {{ $jv->approver->name ?? '-' }}
            @endif
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th width="12%">{{ __('messages.account_code') }}</th>
                <th width="33%">{{ __('messages.account_name') }}</th>
                <th width="25%">{{ __('messages.details') }}</th>
                <th width="15%" class="text-end">{{ __('messages.debit') }}</th>
                <th width="15%" class="text-end">{{ __('messages.credit') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalDebit = 0;
                $totalCredit = 0;
            @endphp
            @foreach($jv->items as $item)
                <tr>
                    <td>{{ $item->account->code }}</td>
                    <td>{{ $item->account->name }}</td>
                    <td>
                        @if($item->customer_id)
                            <span class="badge bg-light text-dark">{{ $item->customer->name ?? '-' }}</span>
                        @elseif($item->vendor_id)
                            <span class="badge bg-light text-dark">{{ $item->vendor->name ?? '-' }}</span>
                        @elseif($item->employee_id)
                            <span class="badge bg-light text-dark">{{ $item->employee->name ?? '-' }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-end">{{ $item->debit > 0 ? number_format($item->debit, 2) : '-' }}</td>
                    <td class="text-end">{{ $item->credit > 0 ? number_format($item->credit, 2) : '-' }}</td>
                </tr>
                @php
                    $totalDebit += $item->debit;
                    $totalCredit += $item->credit;
                @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #f8fafc;">
                <td colspan="3" class="text-end">{{ __('messages.total') }}</td>
                <td class="text-end border-top: 2px solid #333;">{{ number_format($totalDebit, 2) }}</td>
                <td class="text-end border-top: 2px solid #333;">{{ number_format($totalCredit, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="signature-grid">
        <div class="signature-box">
            {{ __('messages.prepared_by') }}
        </div>
        <div class="signature-box">
            {{ __('messages.reviewed_by') }}
        </div>
        <div class="signature-box">
            {{ __('messages.approved_by') }}
        </div>
    </div>

    <div class="footer">
        <p>{{ config('app.url') }}</p>
    </div>
</body>

</html>
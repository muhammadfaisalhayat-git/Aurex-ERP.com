<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Ledger Report</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .summary { margin-top: 20px; margin-left: auto; width: 300px; }
    </style>
</head>
<body>
    <div class="header">
        <table style="border: none; margin-bottom: 0;">
            <tr style="border: none;">
                <td style="border: none; width: 30%;">
                    @if($company && $company->logo)
                        <img src="{{ public_path('storage/' . $company->logo) }}" style="max-height: 80px;">
                    @else
                        <div style="width: 80px; height: 80px; background: #eee; line-height: 80px; text-align: center;">LOGO</div>
                    @endif
                </td>
                <td style="border: none; width: 40%; text-align: center;">
                    <h2 style="margin: 0;">{{ $company ? $company->name : 'Daily Ledger Report' }}</h2>
                    @if($branch)
                        <h4 style="margin: 5px 0; color: #666;">{{ $branch->name }}</h4>
                    @endif
                    <p style="margin: 5px 0;">Daily Ledger Report</p>
                </td>
                <td style="border: none; width: 30%; text-align: right;">
                    <p style="margin: 0;">Period: {{ $start_date }} to {{ $endDate ?? $end_date }}</p>
                    <p style="margin: 5px 0; font-size: 8px;">Generated on: {{ now()->format('Y-m-d H:i') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Reference</th>
                <th>Account</th>
                <th>Sub-Account</th>
                <th>Description</th>
                <th class="text-end">Debit</th>
                <th class="text-end">Credit</th>
                <th class="text-end">Balance</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="7" class="text-end fw-bold">Opening Balance</td>
                <td class="text-end fw-bold">{{ number_format($opening_balance, 2) }}</td>
            </tr>
            @foreach($entries as $entry)
                @php 
                    $subAccount = $entry->customer ? $entry->customer->name : ($entry->vendor ? $entry->vendor->name : ($entry->employee ? $entry->employee->name : '-'));
                @endphp
                <tr>
                    <td>{{ $entry->transaction_date->format('Y-m-d') }}</td>
                    <td>{{ $entry->reference_number }}</td>
                    <td>{{ $entry->chartOfAccount->name }}</td>
                    <td>{{ $subAccount }}</td>
                    <td>{{ $entry->description }}</td>
                    <td class="text-end">{{ $entry->debit > 0 ? number_format($entry->debit, 2) : '-' }}</td>
                    <td class="text-end">{{ $entry->credit > 0 ? number_format($entry->credit, 2) : '-' }}</td>
                    <td class="text-end fw-bold">{{ number_format($entry->running_balance, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="fw-bold">
                <td colspan="5" class="text-end">TOTALS</td>
                <td class="text-end">{{ number_format($total_debit, 2) }}</td>
                <td class="text-end">{{ number_format($total_credit, 2) }}</td>
                <td class="text-end" style="background-color: #f2f2f2;">{{ number_format($opening_balance + ($total_debit - $total_credit), 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>

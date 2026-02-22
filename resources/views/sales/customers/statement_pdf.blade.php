<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customer Statement - {{ $customer->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #000; }
        .info-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .info-table td { padding: 5px; vertical-align: top; }
        .statement-table { width: 100%; border-collapse: collapse; }
        .statement-table th { background: #f2f2f2; border: 1px solid #ccc; padding: 8px; text-align: left; }
        .statement-table td { border: 1px solid #ccc; padding: 8px; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .footer { margin-top: 30px; font-size: 10px; color: #777; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>CUSTOMER STATEMENT</h1>
        <p>{{ now()->format('Y-m-d H:i') }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="50%">
                <p class="fw-bold">Customer:</p>
                <p>{{ $customer->name }}</p>
                <p>{{ $customer->code }}</p>
                <p>{{ $customer->address }}</p>
            </td>
            <td width="50%" class="text-end">
                <p class="fw-bold">Company:</p>
                <p>{{ auth()->user()->company->name ?? 'Aurex ERP' }}</p>
                <p>{{ auth()->user()->branch->name ?? '' }}</p>
            </td>
        </tr>
    </table>

    <table class="statement-table">
        <thead>
            <tr>
                <th width="15%">Date</th>
                <th width="15%">Reference</th>
                <th>Description</th>
                <th width="12%" class="text-end">Debit</th>
                <th width="12%" class="text-end">Credit</th>
                <th width="15%" class="text-end">Balance</th>
            </tr>
        </thead>
        <tbody>
            @php
                $runningBalance = $openingBalance ?? 0;
                $totalDebit = 0;
                $totalCredit = 0;
            @endphp
            <tr>
                <td colspan="2"></td>
                <td class="fw-bold">Opening Balance</td>
                <td colspan="2"></td>
                <td class="text-end fw-bold">{{ number_format($runningBalance, 2) }}</td>
            </tr>
            @foreach($entries as $entry)
                @php
                    $runningBalance += ($entry->debit - $entry->credit);
                    $totalDebit += $entry->debit;
                    $totalCredit += $entry->credit;
                @endphp
                <tr>
                    <td>{{ $entry->transaction_date->format('Y-m-d') }}</td>
                    <td>{{ $entry->reference_number }}</td>
                    <td>{{ $entry->description }}</td>
                    <td class="text-end">{{ $entry->debit > 0 ? number_format($entry->debit, 2) : '-' }}</td>
                    <td class="text-end">{{ $entry->credit > 0 ? number_format($entry->credit, 2) : '-' }}</td>
                    <td class="text-end fw-bold">{{ number_format($runningBalance, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="fw-bold">
                <td colspan="3" class="text-end">Total</td>
                <td class="text-end">{{ number_format($totalDebit, 2) }}</td>
                <td class="text-end">{{ number_format($totalCredit, 2) }}</td>
                <td class="text-end bg-light">{{ number_format($runningBalance, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>This is a computer-generated document. No signature required.</p>
    </div>
</body>
</html>

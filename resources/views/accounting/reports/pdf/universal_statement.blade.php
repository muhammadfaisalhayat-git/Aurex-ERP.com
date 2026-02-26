<!DOCTYPE html>
<html dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('messages.universal_statement_report') }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; margin: 0; padding: 0; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 15px; }
        .company-name { font-size: 18px; font-weight: bold; margin-bottom: 5px; color: #000; }
        .report-title { font-size: 14px; color: #666; text-transform: uppercase; letter-spacing: 1px; }
        
        .info-section { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .info-section td { padding: 5px 0; }
        .info-label { font-weight: bold; color: #555; width: 120px; }
        .info-value { border-bottom: 1px dotted #ccc; }
        
        .balance-box { float: right; background: #f8f9fa; border: 1px solid #dee2e6; padding: 10px 15px; text-align: right; margin-bottom: 20px; border-radius: 4px; }
        .balance-label { font-size: 10px; color: #6c757d; display: block; }
        .balance-amount { font-size: 16px; font-weight: bold; color: #333; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f1f1f1; border: 1px solid #dee2e6; color: #333; font-weight: bold; padding: 8px 5px; text-align: center; }
        td { border: 1px solid #dee2e6; padding: 8px 5px; vertical-align: middle; }
        
        .text-centered { text-align: center; }
        .text-end { text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; }
        .text-start { text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}; }
        
        .stock-in { color: #198754; }
        .stock-out { color: #dc3545; }
        .fw-bold { font-weight: bold; }
        
        .footer { position: fixed; bottom: 0; width: 100%; font-size: 9px; color: #999; text-align: center; border-top: 1px solid #eee; padding-top: 10px; }
        .page-number:after { content: counter(page); }

        .ar-font { font-family: 'Amiri', 'DejaVu Sans', serif; }
        [dir="rtl"] .text-end { text-align: left; }
        [dir="rtl"] .text-start { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%; border: none; margin-bottom: 0;">
            <tr style="border: none;">
                <td style="border: none; width: 25%; text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};">
                    @if($company && $company->logo)
                        <img src="{{ public_path('storage/' . $company->logo) }}" style="max-height: 70px;">
                    @else
                        <div style="width: 70px; height: 70px; background: #eee; line-height: 70px; text-align: center; border-radius: 4px;">LOGO</div>
                    @endif
                </td>
                <td style="border: none; width: 50%; text-align: center;">
                    <div class="company-name">{{ $company->name }}</div>
                    @if(isset($branch))
                        <div style="font-size: 13px; color: #666; margin-bottom: 5px;">{{ $branch->name }}</div>
                    @endif
                    <div class="report-title">{{ __('messages.entity_statement_report', ['type' => $typeLabel]) }}</div>
                </td>
                <td style="border: none; width: 25%; text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; vertical-align: top;">
                    <div style="font-size: 9px; color: #777;">
                        {{ __('messages.statement_report') }}: {{ now()->format('Y-m-d') }}<br>
                        {{ __('messages.page') }}: <span class="page-number"></span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <table class="info-section">
        <tr>
            <td class="info-label">{{ __('messages.entity') }}:</td>
            <td class="info-value"><strong>{{ $entityName }}</strong></td>
            <td rowspan="2" style="vertical-align: top; width: 40%;">
                <div class="balance-box">
                    <span class="balance-label">{{ __('messages.opening_balance') }}</span>
                    <span class="balance-amount">{{ number_format($openingBalance, 2) }}</span>
                </div>
            </td>
        </tr>
        <tr>
            <td class="info-label">{{ __('messages.date_range') }}:</td>
            <td class="info-value">{{ \Carbon\Carbon::parse($startDate)->format('Y-m-d') }} - {{ \Carbon\Carbon::parse($endDate)->format('Y-m-d') }}</td>
        </tr>
    </table>

    <table>
        @php 
            $isStock = in_array($type, ['product', 'warehouse', 'category', 'stock_supply', 'stock_receiving', 'stock_transfer', 'transfer_request', 'issue_order', 'composite_assembly']);
            $hasOpeningBalance = !in_array($type, ['stock_supply', 'stock_receiving', 'stock_transfer', 'transfer_request', 'issue_order', 'composite_assembly', 'production_order']);
            $currentBalance = $openingBalance;
        @endphp
        <thead>
            <tr>
                <th width="15%">{{ __('messages.date') }}</th>
                <th width="20%">{{ __('messages.reference') }}</th>
                <th>{{ __('messages.description') }}</th>
                @if($isStock)
                    <th width="12%" class="text-end">{{ __('messages.stock_in') }}</th>
                    <th width="12%" class="text-end">{{ __('messages.stock_out') }}</th>
                @else
                    <th width="12%" class="text-end">{{ __('messages.debit') }}</th>
                    <th width="12%" class="text-end">{{ __('messages.credit') }}</th>
                @endif
                <th width="15%" class="text-end">{{ __('messages.balance') }}</th>
            </tr>
        </thead>
        <tbody>
            @if($hasOpeningBalance)
            <tr>
                <td colspan="{{ $isStock ? 5 : 5 }}" class="text-end fw-bold">{{ __('messages.opening_balance') }}</td>
                <td class="text-end fw-bold">{{ number_format($openingBalance, $isStock ? 3 : 2) }}</td>
            </tr>
            @endif
            @foreach($results as $item)
                @if($isStock)
                    @php 
                        $in = $item->movement_type === 'in' ? $item->quantity : 0;
                        $out = $item->movement_type === 'out' ? $item->quantity : 0;
                        $currentBalance += ($in - $out);
                    @endphp
                    <tr>
                        <td class="text-centered">{{ $item->transaction_date->format('Y-m-d') }}</td>
                        <td class="text-centered">{{ $item->reference_number }}</td>
                        <td>{{ $item->notes }}</td>
                        <td class="text-end stock-in">{{ $in > 0 ? number_format($in, 3) : '-' }}</td>
                        <td class="text-end stock-out">{{ $out > 0 ? number_format($out, 3) : '-' }}</td>
                        <td class="text-end fw-bold">{{ number_format($currentBalance, 3) }}</td>
                    </tr>
                @else
                    @php 
                        $debit = $item->debit;
                        $credit = $item->credit;
                        $balanceFactor = ($type === 'vendor') ? ($credit - $debit) : ($debit - $credit);
                        $currentBalance += $balanceFactor;
                    @endphp
                    <tr>
                        <td class="text-centered">{{ $item->transaction_date->format('Y-m-d') }}</td>
                        <td class="text-centered">{{ $item->reference_number }}</td>
                        <td>{{ $item->description }}</td>
                        <td class="text-end">{{ $debit > 0 ? number_format($debit, 2) : '-' }}</td>
                        <td class="text-end">{{ $credit > 0 ? number_format($credit, 2) : '-' }}</td>
                        <td class="text-end fw-bold">{{ number_format($currentBalance, 2) }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #eee;">
                <td colspan="{{ $isStock ? 5 : 5 }}" class="text-end fw-bold">{{ __('messages.current_balance') }}</td>
                <td class="text-end fw-bold">{{ number_format($currentBalance, $isStock ? 3 : 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        {{ __('messages.generated_on') }}: {{ now()->format('Y-m-d H:i') }} | {{ __('messages.page') }} <span class="page-number"></span>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <title>Salary Slip - {{ $employee->employee_code }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 13px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 5px;
        }

        .company-info {
            font-size: 11px;
            color: #666;
        }

        .slip-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 15px;
            text-transform: uppercase;
            color: #2c3e50;
        }

        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            border: 1px solid #eee;
            background-color: #fcfcfc;
        }

        .info-row {
            display: table-row;
        }

        .info-cell {
            display: table-cell;
            padding: 8px 12px;
            border-bottom: 1px solid #eee;
        }

        .label {
            font-weight: bold;
            color: #555;
            width: 25%;
        }

        .value {
            width: 25%;
        }

        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .salary-table th {
            background-color: #2c3e50;
            color: white;
            padding: 10px;
            text-align: left;
            border: 1px solid #2c3e50;
        }

        .salary-table td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .text-right {
            text-align: right;
        }

        [dir="rtl"] .salary-table th {
            text-align: right;
        }

        [dir="rtl"] .text-right {
            text-align: left;
        }

        .total-row {
            font-weight: bold;
            background-color: #ecf0f1;
        }

        .signature-section {
            margin-top: 60px;
            width: 100%;
        }

        .signature-box {
            width: 40%;
            border-top: 1px solid #888;
            text-align: center;
            padding-top: 8px;
            font-style: italic;
        }

        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 11px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        @page {
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="company-name">{{ $employee->company?->name ?? config('app.name') }}</div>
            <div class="company-info">
                {{ $employee->branch?->name }}<br>
                {{ $employee->branch?->address }}
            </div>
            <div class="slip-title">{{ __('messages.salary_slip') }}</div>
        </div>

        <div class="info-section">
            <div class="info-row">
                <div class="info-cell label">{{ __('messages.name') }}</div>
                <div class="info-cell value">{{ $employee->name }}</div>
                <div class="info-cell label">{{ __('messages.employee_code') }}</div>
                <div class="info-cell value">{{ $employee->employee_code }}</div>
            </div>
            <div class="info-row">
                <div class="info-cell label">{{ __('messages.department') }}</div>
                <div class="info-cell value">{{ $employee->department?->name }}</div>
                <div class="info-cell label">{{ __('messages.designation') }}</div>
                <div class="info-cell value">{{ $employee->designation?->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-cell label">{{ __('messages.joining_date') }}</div>
                <div class="info-cell value">
                    {{ $employee->joining_date ? $employee->joining_date->format('Y-m-d') : '-' }}</div>
                <div class="info-cell label">{{ __('messages.phone') }}</div>
                <div class="info-cell value">{{ $employee->phone }}</div>
            </div>
        </div>

        <table class="salary-table">
            <thead>
                <tr>
                    <th>{{ __('messages.description') }}</th>
                    <th class="text-right" style="width: 150px;">{{ __('messages.amount') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ __('messages.basic_salary') }}</td>
                    <td class="text-right">{{ number_format($employee->basic_salary, 2) }}</td>
                </tr>
                @if($employee->house_rent_allowance > 0)
                    <tr>
                        <td>{{ __('messages.house_rent_allowance') }}</td>
                        <td class="text-right">{{ number_format($employee->house_rent_allowance, 2) }}</td>
                    </tr>
                @endif
                @if($employee->conveyance_allowance > 0)
                    <tr>
                        <td>{{ __('messages.conveyance_allowance') }}</td>
                        <td class="text-right">{{ number_format($employee->conveyance_allowance, 2) }}</td>
                    </tr>
                @endif
                @if($employee->dearness_allowance > 0)
                    <tr>
                        <td>{{ __('messages.dearness_allowance') }}</td>
                        <td class="text-right">{{ number_format($employee->dearness_allowance, 2) }}</td>
                    </tr>
                @endif
                @if($employee->overtime_allowance > 0)
                    <tr>
                        <td>{{ __('messages.overtime_allowance') }}</td>
                        <td class="text-right">{{ number_format($employee->overtime_allowance, 2) }}</td>
                    </tr>
                @endif
                @if($employee->other_allowance > 0)
                    <tr>
                        <td>{{ __('messages.other_allowance') }}</td>
                        <td class="text-right">{{ number_format($employee->other_allowance, 2) }}</td>
                    </tr>
                @endif
            </tbody>
            <tfoot>
                @php
                    $total = (float) $employee->basic_salary +
                        (float) $employee->house_rent_allowance +
                        (float) $employee->conveyance_allowance +
                        (float) $employee->dearness_allowance +
                        (float) $employee->overtime_allowance +
                        (float) $employee->other_allowance;
                @endphp
                <tr class="total-row">
                    <td>{{ __('messages.grand_total') }}</td>
                    <td class="text-right">{{ number_format($total, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="signature-section">
            <div class="signature-box" style="float: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};">
                {{ __('messages.employee_signature') }}
            </div>
            <div class="signature-box" style="float: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};">
                {{ __('messages.authorized_signature') }}
            </div>
            <div style="clear: both;"></div>
        </div>

        <div class="footer">
            {{ __('messages.generated_on') }}: {{ now()->format('Y-m-d H:i') }}
        </div>
    </div>
</body>

</html>
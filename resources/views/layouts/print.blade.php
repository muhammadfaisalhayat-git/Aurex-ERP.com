<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - {{ config('app.name') }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; color: #333; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #4e73df; }
        .company-info { text-align: right; font-size: 14px; }
        .report-title { text-align: center; font-size: 22px; margin-bottom: 30px; text-transform: uppercase; letter-spacing: 1px; }
        .content { margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { bg-color: #f8f9fc; color: #4e73df; font-weight: bold; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #777; border-top: 1px solid #eee; padding-top: 5px; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
        .btn-print { background: #4e73df; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: right;">
        <button class="btn-print" onclick="window.print()">{{ __('messages.print') }}</button>
    </div>

    <div class="header">
        <div class="logo">AUREX ERP</div>
        <div class="company-info">
            <strong>{{ session('active_company_name', 'Aurex Corporation') }}</strong><br>
            {{ session('active_branch_name', 'Main Branch') }}<br>
            {{ date('Y-m-d H:i') }}
        </div>
    </div>

    <div class="report-title">
        @yield('report_title')
    </div>

    <div class="content">
        @yield('content')
    </div>

    <div class="footer">
        {{ __('messages.printed_by') }}: {{ auth()->user()->name ?? 'System' }} | {{ __('messages.page') }} <span class="page-number"></span>
    </div>
</body>
</html>

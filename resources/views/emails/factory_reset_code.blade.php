<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 30px;
            text-align: center;
        }

        .content {
            padding: 30px;
            text-align: center;
        }

        .code {
            font-size: 32px;
            font-weight: bold;
            color: #764ba2;
            letter-spacing: 5px;
            background: #f0f0f0;
            padding: 15px;
            border-radius: 4px;
            display: inline-block;
            margin: 20px 0;
        }

        .footer {
            background: #f9f9f9;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>{{ __('messages.factory_reset_security_code') }}</h1>
        </div>
        <div class="content">
            <p>{{ __('messages.hello') }} {{ $user->name }},</p>
            <p>{{ __('messages.factory_reset_email_intro') }}</p>
            <div class="code">{{ $code }}</div>
            <p>{{ __('messages.factory_reset_email_warning') }}</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('messages.all_rights_reserved') }}</p>
        </div>
    </div>
</body>

</html>
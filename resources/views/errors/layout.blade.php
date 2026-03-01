<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ config('app.name') }}</title>
    <!-- Bootstrap CSS -->
    @if(app()->getLocale() === 'ar')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    @else
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    @endif
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Cairo:wght@400;600;700&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --primary-color: #2563eb;
            --danger-color: #ef4444;
        }

        body {
            background-color: #f8fafc;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family:
                {{ app()->getLocale() === 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }}
            ;
        }

        .error-card {
            background: #fff;
            padding: 2.5rem;
            border-radius: 1.5rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        .error-icon {
            font-size: 4rem;
            color: var(--danger-color);
            margin-bottom: 1.5rem;
        }

        .error-code {
            font-size: 3rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .error-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 1.5rem;
        }

        .btn-home {
            background-color: var(--primary-color);
            color: #fff;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            transition: opacity 0.2s;
        }

        .btn-home:hover {
            opacity: 0.9;
            color: #fff;
        }
    </style>
</head>

<body>
    <turbo-frame id="main-frame">
        <div class="error-card">
            <div class="error-icon">
                @yield('icon')
            </div>
            <div class="error-code">@yield('code')</div>
            <div class="error-title">@yield('message')</div>
            <a href="{{ url('/') }}" class="btn-home" data-turbo-frame="_top">
                <i class="fas fa-home me-2"></i>{{ __('messages.go_home') ?? 'Go to Dashboard' }}
            </a>
        </div>
    </turbo-frame>
</body>

</html>
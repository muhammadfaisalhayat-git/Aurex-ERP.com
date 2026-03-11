<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.activation_required') ?? 'Activation Required' }} - {{ config('app.name', 'Aurex ERP') }}
    </title>

    <!-- Bootstrap CSS -->
    @if(app()->getLocale() == 'ar')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background-color: #f8f9fc;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .activation-card {
            max-width: 500px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            background: #fff;
            border: 1px solid #edf2f9;
        }

        .activation-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .activation-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.9;
        }
    </style>
</head>

<body>

    <div class="activation-card">
        <div class="activation-header">
            <div class="activation-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h3 class="fw-bold mb-1">{{ __('messages.system_activation') ?? 'System Activation' }}</h3>
            <p class="mb-0 text-white-50">
                {{ __('messages.activation_desc') ?? 'Please configure the core network layers to unlock this deployment instance.' }}
            </p>
        </div>

        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger small">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('activation.store') }}">
                @csrf

                <div class="mb-4">
                    <label
                        class="form-label fw-semibold text-dark">{{ __('messages.master_server_address') ?? 'Master Server Address' }}
                        <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-globe text-muted"></i></span>
                        <input type="url" name="master_server_address" class="form-control"
                            placeholder="https://master.yourdomain.com" required
                            value="{{ old('master_server_address') }}">
                    </div>
                    <div class="form-text small text-muted">
                        {{ __('messages.master_server_hint') ?? 'The primary URL where the central database and main application are hosted.' }}
                    </div>
                </div>

                <div class="mb-4">
                    <label
                        class="form-label fw-semibold text-dark">{{ __('messages.deployment_url') ?? 'Current Deployment URL' }}
                        <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-link text-muted"></i></span>
                        <input type="url" name="deployment_url" class="form-control"
                            placeholder="https://local.yourdomain.com" required
                            value="{{ old('deployment_url', config('app.url')) }}">
                    </div>
                    <div class="form-text small text-muted">
                        {{ __('messages.deployment_url_hint') ?? 'The specific URL accessing this local/instance deployment right now.' }}
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                    <i class="fas fa-unlock me-2"></i> {{ __('messages.activate_system') ?? 'Activate System' }}
                </button>
            </form>
        </div>

        <div class="card-footer bg-light border-0 text-center py-3">
            <small class="text-muted"><i class="fas fa-lock me-1"></i>
                {{ __('messages.secure_connection') ?? 'Secure Setup Connection' }} &bull; {{ date('Y') }}</small>
        </div>
    </div>

</body>

</html>
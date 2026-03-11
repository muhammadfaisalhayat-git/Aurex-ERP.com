@extends('layouts.app')

@section('title', __('messages.settings'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold">{{ __('messages.settings') }}</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm glassy mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm glassy mb-4" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm glassy">
        <div class="card-header bg-transparent border-bottom-0 pt-4">
            <ul class="nav nav-pills" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">{{ __('messages.general') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tax-tab" data-bs-toggle="tab" data-bs-target="#tax" type="button" role="tab">{{ __('messages.tax') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="visibility-tab" data-bs-toggle="tab" data-bs-target="#visibility" type="button" role="tab">{{ __('messages.module_visibility_control') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="maintenance-tab" data-bs-toggle="tab" data-bs-target="#maintenance" type="button" role="tab">{{ __('messages.maintenance') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="deployments-tab" data-bs-toggle="tab" data-bs-target="#deployments" type="button" role="tab">{{ __('messages.deployments') }}</button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <form action="{{ route('acp.system.settings.update') }}" method="POST">
                @csrf
                <div class="tab-content" id="settingsTabsContent">
                    <!-- General Settings -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel">
                        <div class="row">
                            @php
                                $generalSettings = $systemSettings->reject(fn($val, $key) => $key === 'module_visibility');
                            @endphp

                            @forelse($generalSettings as $group => $settings)
                                <div class="col-12 mb-4">
                                    <h5 class="fw-bold border-bottom pb-2">{{ ucfirst($group) }}</h5>
                                    <div class="row">
                                        @foreach($settings as $setting)
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">
                                                    {{ app()->getLocale() == 'ar' ? $setting->display_name_ar : $setting->display_name_en }}
                                                </label>
                                                @if($setting->type == 'boolean')
                                                    <div class="form-check form-switch">
                                                        <input type="hidden" name="settings[{{ $setting->id }}]" value="0">
                                                        <input class="form-check-input" type="checkbox" name="settings[{{ $setting->id }}]" value="1" {{ $setting->value ? 'checked' : '' }} {{ !$setting->is_editable ? 'disabled' : '' }}>
                                                    </div>
                                                @elseif($setting->type == 'text' || $setting->type == 'string')
                                                    <input type="text" class="form-control" name="settings[{{ $setting->id }}]" value="{{ $setting->value }}" {{ !$setting->is_editable ? 'readonly' : '' }}>
                                                @elseif($setting->type == 'integer' || $setting->type == 'float')
                                                    <input type="number" step="any" class="form-control" name="settings[{{ $setting->id }}]" value="{{ $setting->value }}" {{ !$setting->is_editable ? 'readonly' : '' }}>
                                                @endif
                                                @if($setting->description)
                                                    <div class="form-text mt-1 small text-muted">{{ $setting->description }}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-5">
                                    <div class="text-muted">No general system settings found.</div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Tax Settings -->
                    <div class="tab-pane fade" id="tax" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="tax[tax_enabled]" value="0">
                                    <input class="form-check-input" type="checkbox" id="tax_enabled" name="tax[tax_enabled]" value="1" {{ $taxSettings->tax_enabled ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="tax_enabled">{{ __('messages.tax_enabled') }}</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="default_tax_rate" class="form-label fw-semibold">{{ __('messages.default_tax_rate') }} (%)</label>
                                <input type="number" step="0.01" class="form-control" id="default_tax_rate" name="tax[default_tax_rate]" value="{{ $taxSettings->default_tax_rate }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tax_name_en" class="form-label fw-semibold">{{ __('messages.tax_name') }} (EN)</label>
                                <input type="text" class="form-control" id="tax_name_en" name="tax[tax_name_en]" value="{{ $taxSettings->tax_name_en }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tax_name_ar" class="form-label fw-semibold">{{ __('messages.tax_name') }} (AR)</label>
                                <input type="text" class="form-control text-end" id="tax_name_ar" name="tax[tax_name_ar]" value="{{ $taxSettings->tax_name_ar }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tax_number" class="form-label fw-semibold">{{ __('messages.tax_number') }}</label>
                                <input type="text" class="form-control" id="tax_number" name="tax[tax_number]" value="{{ $taxSettings->tax_number }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">{{ __('messages.rounding_mode') }}</label>
                                <select class="form-select" name="tax[rounding_mode]" required>
                                    <option value="per_line" {{ $taxSettings->rounding_mode == 'per_line' ? 'selected' : '' }}>Per Line</option>
                                    <option value="per_total" {{ $taxSettings->rounding_mode == 'per_total' ? 'selected' : '' }}>Per Total</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Module Visibility Settings -->
                    <div class="tab-pane fade" id="visibility" role="tabpanel">
                        <div class="row">
                            @if(isset($systemSettings['module_visibility']))
                                <div class="col-12 mb-4">
                                    <div class="alert alert-info border-0 shadow-sm glassy">
                                        <i class="fas fa-info-circle me-2"></i>
                                        {{ __('messages.module_visibility_description') }}
                                    </div>
                                </div>
                                
                                @php
                                    $visibilitySettings = $systemSettings['module_visibility'];
                                    $sections = $visibilitySettings->filter(fn($s) => str_starts_with($s->key, 'module_'));
                                    $options = $visibilitySettings->filter(fn($s) => str_starts_with($s->key, 'sidebar_'));
                                @endphp

                                <div class="col-md-6 mb-4">
                                    <h5 class="fw-bold border-bottom pb-2">{{ __('messages.sidebar_sections_visibility') }}</h5>
                                    @foreach($sections as $setting)
                                        <div class="mb-3 d-flex justify-content-between align-items-center bg-light p-2 rounded">
                                            <label class="form-label fw-semibold mb-0">
                                                {{ app()->getLocale() == 'ar' ? $setting->display_name_ar : $setting->display_name_en }}
                                            </label>
                                            <div class="form-check form-switch">
                                                <input type="hidden" name="settings[{{ $setting->id }}]" value="0">
                                                <input class="form-check-input" type="checkbox" name="settings[{{ $setting->id }}]" value="1" {{ $setting->value ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="col-md-6 mb-4">
                                    <h5 class="fw-bold border-bottom pb-2">{{ __('messages.sidebar_items_visibility') }}</h5>
                                    <div class="visibility-options-grid" style="max-height: 500px; overflow-y: auto; padding-right: 10px;">
                                        @foreach($options as $setting)
                                            <div class="mb-2 d-flex justify-content-between align-items-center border-bottom pb-2">
                                                <span class="small">
                                                    {{ app()->getLocale() == 'ar' ? $setting->display_name_ar : $setting->display_name_en }}
                                                </span>
                                                <div class="form-check form-switch">
                                                    <input type="hidden" name="settings[{{ $setting->id }}]" value="0">
                                                    <input class="form-check-input" type="checkbox" name="settings[{{ $setting->id }}]" value="1" {{ $setting->value ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="col-12 text-center py-5">
                                    <div class="text-muted">No visibility settings found.</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Maintenance Settings -->
                    <div class="tab-pane fade" id="maintenance" role="tabpanel">
                        <div class="row">
                            @if(isset($systemSettings['maintenance']))
                                <div class="col-12 mb-4">
                                    <h5 class="fw-bold border-bottom pb-2">{{ __('messages.security_configuration') }}</h5>
                                    <div class="row">
                                        @foreach($systemSettings['maintenance'] as $setting)
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">
                                                    {{ app()->getLocale() == 'ar' ? $setting->display_name_ar : $setting->display_name_en }}
                                                </label>
                                                @if($setting->type == 'text' || $setting->type == 'string')
                                                    <input type="text" class="form-control" name="settings[{{ $setting->id }}]" value="{{ $setting->value }}" {{ !$setting->is_editable ? 'readonly' : '' }}>
                                                @endif
                                                @if($setting->description)
                                                    <div class="form-text mt-1 small text-muted">{{ $setting->description }}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="alert alert-info border-0 shadow-sm glassy small mb-4">
                                        <i class="fas fa-info-circle me-2"></i> {{ __('messages.admin_email_hint') }}
                                    </div>
                                </div>
                            @endif

                            <div class="col-12 mb-4">
                                <div class="alert alert-warning border-0 shadow-sm glassy">
                                    <div class="d-flex">
                                        <div class="me-3">
                                            <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                                        </div>
                                        <div>
                                            <h5 class="fw-bold mb-1">{{ __('messages.danger_zone') }}</h5>
                                            <p class="mb-0 text-muted small">{{ __('messages.factory_reset_description') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card border-danger border-opacity-25 shadow-sm h-100">
                                    <div class="card-body">
                                        <h6 class="fw-bold text-danger mb-3">
                                            <i class="fas fa-trash-alt me-2"></i> {{ __('messages.factory_reset') }}
                                        </h6>
                                        <p class="small text-muted mb-4">
                                            {{ __('messages.factory_reset_warning') }}
                                        </p>
                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#factoryResetModal">
                                            {{ __('messages.perform_factory_reset') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Deployment Management (The Frame) -->
                    <div class="tab-pane fade" id="deployments" role="tabpanel">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-header bg-primary text-white py-3">
                                        <h6 class="mb-0 fw-bold"><i class="fas fa-server me-2"></i> {{ __('messages.server_info') }}</h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-sm mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="ps-3 border-0 py-2 muted small">Environment</td>
                                                    <td class="text-end pe-3 border-0 py-2 fw-bold small text-uppercase">{{ app()->environment() }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-3 py-2 muted small">PHP Version</td>
                                                    <td class="text-end pe-3 py-2 fw-bold small">{{ PHP_VERSION }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-3 py-2 muted small">Database</td>
                                                    <td class="text-end pe-3 py-2 fw-bold small text-uppercase text-primary">{{ config('database.default') }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-3 py-2 muted small">App URL</td>
                                                    <td class="text-end pe-3 py-2 small fw-semibold text-truncate" style="max-width: 150px;">{{ config('app.url') }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-dark text-white py-3">
                                        <h6 class="mb-0 fw-bold"><i class="fas fa-plus me-2"></i> {{ __('messages.add_deployment') }}</h6>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('acp.system.settings.deployments.store') }}" method="POST">
                                            @csrf
                                            <div class="mb-2">
                                                <label class="form-label small mb-1">{{ __('messages.instance_name') }}</label>
                                                <input type="text" name="name" class="form-control form-control-sm" required placeholder="e.g. Cloud Server">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small mb-1">{{ __('messages.deployment_url') }}</label>
                                                <input type="url" name="url" class="form-control form-control-sm" placeholder="https://example.com">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small mb-1">{{ __('messages.deployment_type') }}</label>
                                                <select name="type" class="form-select form-select-sm">
                                                    <option value="online">{{ __('messages.online') }}</option>
                                                    <option value="offline">{{ __('messages.offline') }}</option>
                                                </select>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small mb-1">{{ __('messages.location') ?? 'Location' }}</label>
                                                <input type="text" name="location" class="form-control form-control-sm" placeholder="e.g. AWS us-east-1, Local Server">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small mb-1">{{ __('messages.notes') ?? 'Notes' }}</label>
                                                <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Environment details..."></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-sm w-100">{{ __('messages.save') }}</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="card border-0 shadow-sm min-vh-50">
                                    <div class="card-header bg-light py-3 border-bottom-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 fw-bold"><i class="fas fa-network-wired me-2"></i> {{ __('messages.instance_tracker') }}</h6>
                                            <span class="badge bg-secondary rounded-pill small">{{ $deployments->count() }}</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted small mb-4">{{ __('messages.deployment_frame_hint') }}</p>

                                        @if($deployments->isEmpty())
                                            <div class="text-center py-5">
                                                <i class="fas fa-server fa-4x text-muted mb-3 opacity-25"></i>
                                                <p class="text-muted fw-semibold">{{ __('messages.no_data_found') }}</p>
                                            </div>
                                        @else
                                            <div class="table-responsive">
                                                <table class="table table-hover align-middle">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>{{ __('messages.instance_name') }}</th>
                                                            <th>{{ __('messages.type') }}</th>
                                                            <th>{{ __('messages.url') }}</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($deployments as $deployment)
                                                            <tr>
                                                                <td>
                                                                    <div class="fw-bold">{{ $deployment->name }}</div>
                                                                    <div class="small text-muted">{{ $deployment->location }}</div>
                                                                </td>
                                                                <td>
                                                                    <span class="badge {{ $deployment->type == 'online' ? 'bg-success' : 'bg-warning' }} rounded-pill small">
                                                                        {{ __('messages.' . $deployment->type) }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    @if($deployment->url)
                                                                        <a href="{{ $deployment->url }}" target="_blank" class="small text-decoration-none">
                                                                            {{ $deployment->url }} <i class="fas fa-external-link-alt ms-1 small"></i>
                                                                        </a>
                                                                    @else
                                                                        <span class="text-muted small">-</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-end">
                                                                    <form action="{{ route('acp.system.settings.deployments.delete', $deployment) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-link text-danger p-0" title="{{ __('messages.delete') }}">
                                                                            <i class="fas fa-trash-alt"></i>
                                                                        </button>
                                                                    </form>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm">
                        <i class="fas fa-save me-2"></i> {{ __('messages.save_changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Factory Reset Confirmation Modal -->
<div class="modal fade" id="factoryResetModal" tabindex="-1" aria-labelledby="factoryResetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title fw-bold" id="factoryResetModalLabel">
                    <i class="fas fa-radiation me-2"></i> {{ __('messages.critical_confirmation') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('acp.system.settings.factory-reset') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="mb-3 text-dark fw-semibold">{{ __('messages.reset_confirmation_text') }}</p>
                    <div class="alert alert-secondary border-0 small py-2 mb-3">
                        <i class="fas fa-info-circle me-2"></i> {{ __('messages.reset_type_hint') }}
                    </div>
                    <div class="mb-3">
                        <label for="confirm_reset" class="form-label small text-muted text-uppercase fw-bold">{{ __('messages.type_reset_to_confirm') }}</label>
                        <input type="text" class="form-control form-control-lg border-danger" id="confirm_reset" name="confirm_reset" placeholder="RESET" required autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label small text-muted text-uppercase fw-bold">{{ __('messages.confirm_with_password') }}</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="security_code" class="form-label small text-muted text-uppercase fw-bold">{{ __('messages.security_code') }}</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="security_code" name="security_code" required maxlength="6" placeholder="000000">
                            <button class="btn btn-outline-primary" type="button" id="sendCodeBtn" onclick="sendFactoryResetCode()">
                                {{ __('messages.send_code') }}
                            </button>
                        </div>
                        <div id="codeFeedback" class="form-text small mt-1 d-none"></div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-link text-muted text-decoration-none px-4" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-danger px-4 shadow-sm">
                        {{ __('messages.erase_all_data') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .glassy {
        background: rgba(255, 255, 255, 0.8) !important;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .nav-pills .nav-link {
        color: #6c757d;
        font-weight: 500;
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
    }
    .nav-pills .nav-link.active {
        background-color: var(--bs-primary);
        color: white;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
</style>

<script>
    function sendFactoryResetCode() {
        const btn = document.getElementById('sendCodeBtn');
        const feedback = document.getElementById('codeFeedback');
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
        
        fetch("{{ route('acp.system.settings.send-reset-code') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            feedback.classList.remove('d-none', 'text-danger');
            feedback.classList.add('text-success');
            feedback.innerText = data.message;
            
            // Re-enable button after 60 seconds (rate limiting)
            setTimeout(() => {
                btn.disabled = false;
                btn.innerText = "{{ __('messages.send_code') }}";
            }, 60000);
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerText = "{{ __('messages.send_code') }}";
            feedback.classList.remove('d-none', 'text-success');
            feedback.classList.add('text-danger');
            feedback.innerText = 'Error sending code. Please try again.';
        });
    }
</script>
@endpush

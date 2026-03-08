@extends('layouts.app')

@section('title', __('messages.sm_create_alert_rule'))

@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a
                            href="{{ route('acp.system.alert-system.index') }}">{{ __('messages.sm_alert_system') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ __('messages.create') }}</li>
                </ol>
            </nav>
            <h1 class="h3">{{ __('messages.sm_create_alert_rule') }}</h1>
        </div>

        <form action="{{ route('acp.system.alert-system.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold">{{ __('messages.alert_rule_details') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">{{ __('messages.rule_name') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name') }}" placeholder="e.g. Low Inventory Alert" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('messages.module') }} <span
                                            class="text-danger">*</span></label>
                                    <select name="module" class="form-select @error('module') is-invalid @enderror"
                                        required>
                                        @foreach($modules as $module)
                                            <option value="{{ $module }}">{{ $module }}</option>
                                        @endforeach
                                    </select>
                                    @error('module') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('messages.condition_type') }} <span
                                            class="text-danger">*</span></label>
                                    <select name="condition_type"
                                        class="form-select @error('condition_type') is-invalid @enderror" required>
                                        @foreach($conditionTypes as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('condition_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('messages.threshold_limit') }} <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="threshold" step="0.01"
                                            class="form-control @error('threshold') is-invalid @enderror"
                                            value="{{ old('threshold', 0) }}" required>
                                        <span class="input-group-text bg-light"><i class="fas fa-tachometer-alt"></i></span>
                                    </div>
                                    <div class="form-text small">{{ __('messages.threshold_hint') }}</div>
                                    @error('threshold') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">{{ __('messages.notification_recipients') }}</label>
                                    <textarea name="recipients" class="form-control" rows="2"
                                        placeholder="{{ __('messages.emails_comma_separated') }}">{{ old('recipients') }}</textarea>
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                            id="is_active" checked>
                                        <label class="form-check-label"
                                            for="is_active">{{ __('messages.enable_this_rule') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 sticky-top" style="top: 100px;">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100 mb-2 py-2 fw-bold">
                                <i class="fas fa-save me-1"></i> {{ __('messages.save_rule') }}
                            </button>
                            <a href="{{ route('acp.system.alert-system.index') }}"
                                class="btn btn-outline-secondary w-100">
                                {{ __('messages.cancel') }}
                            </a>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mt-3 bg-light">
                        <div class="card-body">
                            <h6 class="fw-bold small text-uppercase mb-2"><i class="fas fa-info-circle me-1"></i>
                                {{ __('messages.how_it_works') }}</h6>
                            <p class="small text-muted mb-0">{{ __('messages.alert_explanation') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
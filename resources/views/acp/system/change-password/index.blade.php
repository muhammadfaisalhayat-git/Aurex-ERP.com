@extends('layouts.app')

@section('title', __('messages.sm_change_password'))

@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h1 class="h3 mb-0">{{ __('messages.sm_change_password') }}</h1>
            <p class="text-muted">{{ __('messages.sm_update_security_desc') }}</p>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-key me-2 text-primary"></i>
                            {{ __('messages.password_security') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('acp.system.change-password.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('messages.current_password') }}</label>
                                <input type="password" name="current_password"
                                    class="form-control @error('current_password') is-invalid @enderror" required>
                                @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <hr class="my-4">

                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('messages.new_password') }}</label>
                                <input type="password" name="new_password"
                                    class="form-control @error('new_password') is-invalid @enderror" required>
                                <div class="form-text small">{{ __('messages.password_complexity_hint') }}</div>
                                @error('new_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">{{ __('messages.confirm_new_password') }}</label>
                                <input type="password" name="new_password_confirmation" class="form-control" required>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary py-2 shadow-sm">
                                    <i class="fas fa-save me-1"></i> {{ __('messages.update_password') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="mt-4 p-4 rounded bg-light-soft border"
                    style="background: rgba(var(--bs-warning-rgb), 0.05); border-color: rgba(var(--bs-warning-rgb), 0.2) !important;">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="fas fa-shield-alt text-warning fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">{{ __('messages.security_advice') }}</h6>
                            <ul class="small mb-0 text-muted ps-3">
                                <li>{{ __('messages.advice_length') }}</li>
                                <li>{{ __('messages.advice_chars') }}</li>
                                <li>{{ __('messages.advice_frequency') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center p-5">
                        <div class="bg-primary-soft p-4 rounded-circle mb-4"
                            style="background: rgba(var(--bs-primary-rgb), 0.1);">
                            <i class="fas fa-lock text-primary fa-4x"></i>
                        </div>
                        <h4 class="fw-bold mb-3">{{ __('messages.account_protection') }}</h4>
                        <p class="text-muted ps-lg-5 pe-lg-5">{{ __('messages.account_protection_desc') }}</p>
                        <div class="small fw-bold text-uppercase letter-spacing-1 text-primary mt-3">
                            <i class="fas fa-user-secret me-1"></i> {{ __('messages.security_layer_active') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
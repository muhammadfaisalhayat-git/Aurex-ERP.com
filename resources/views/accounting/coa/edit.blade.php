@extends('layouts.app')

@section('title', __('messages.edit_account'))

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('messages.edit_account') }}</h1>
            <a href="{{ route('accounting.gl.coa.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('accounting.gl.coa.update', $coa->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('messages.code') }}</label>
                            <input type="text" class="form-control" value="{{ $coa->code }}" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('messages.account_type') }}</label>
                            <input type="text" class="form-control"
                                value="{{ $coa->accountType ? (app()->getLocale() == 'ar' ? $coa->accountType->name_ar : $coa->accountType->name_en) : $coa->type }}"
                                disabled>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('messages.name_en') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror"
                                value="{{ old('name_en', $coa->name_en) }}" required>
                            @error('name_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('messages.name_ar') }}</label>
                            <input type="text" name="name_ar" class="form-control @error('name_ar') is-invalid @enderror"
                                value="{{ old('name_ar', $coa->name_ar) }}">
                            @error('name_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" {{ old('is_active', $coa->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="isActive">{{ __('messages.active') }}</label>
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="is_posting_allowed"
                                    id="is_posting_allowed" value="1" {{ old('is_posting_allowed', $coa->is_posting_allowed) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_posting_allowed">
                                    {{ __('messages.is_posting_allowed') }}
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('messages.sub_ledger_type') }}</label>
                            <select name="sub_ledger_type"
                                class="form-select @error('sub_ledger_type') is-invalid @enderror">
                                <option value="">{{ __('messages.none') }}</option>
                                <option value="customer" {{ old('sub_ledger_type', $coa->sub_ledger_type) == 'customer' ? 'selected' : '' }}>{{ __('messages.customer') }}</option>
                                <option value="vendor" {{ old('sub_ledger_type', $coa->sub_ledger_type) == 'vendor' ? 'selected' : '' }}>{{ __('messages.vendor') }}</option>
                                <option value="employee" {{ old('sub_ledger_type', $coa->sub_ledger_type) == 'employee' ? 'selected' : '' }}>{{ __('messages.employee') }}</option>
                            </select>
                            <small class="text-muted">{{ __('messages.sub_ledger_type_help') }}</small>
                            @error('sub_ledger_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="fas fa-save me-1"></i> {{ __('messages.update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
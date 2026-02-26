@extends('layouts.app')

@section('title', __('messages.edit') . ' - ' . __('messages.bank_cash_accounts'))

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.edit') }}: {{ $bankAccount->name }}</h1>
        <a href="{{ route('finance.bank-accounts.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> {{ __('messages.back') }}
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('finance.bank-accounts.update', $bankAccount->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('messages.account_code') ?? __('messages.code') }}</label>
                        <input type="text" class="form-control" value="{{ $bankAccount->code }}" disabled>
                        <small class="text-muted">Account code cannot be changed.</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('messages.account_type') }}</label>
                        <input type="text" class="form-control" value="{{ __('messages.' . $bankAccount->account_type) }}" disabled>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('messages.name_en') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" value="{{ old('name_en', $bankAccount->name_en) }}" required>
                        @error('name_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('messages.name_ar') }}</label>
                        <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $bankAccount->name_ar) }}">
                    </div>
                </div>

                <div class="row bank-fields" style="{{ $bankAccount->account_type == 'cash' ? 'display: none;' : '' }}">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('messages.bank_name') }}</label>
                        <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $bankAccount->bank_name) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('messages.account_number') }}</label>
                        <input type="text" name="account_number" class="form-control" value="{{ old('account_number', $bankAccount->account_number) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('messages.iban') }}</label>
                        <input type="text" name="iban" class="form-control" value="{{ old('iban', $bankAccount->iban) }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('messages.status') }}</label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ old('is_active', $bankAccount->is_active) ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                            <option value="0" {{ !old('is_active', $bankAccount->is_active) ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
                    <a href="{{ route('finance.bank-accounts.index') }}" class="btn btn-link text-secondary">{{ __('messages.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

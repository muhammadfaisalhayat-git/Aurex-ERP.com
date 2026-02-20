@extends('layouts.app')

@section('title', __('messages.add_account') . ' - ' . __('messages.finance_banking'))

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.add_account') }}</h1>
        <a href="{{ route('finance.bank-accounts.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> {{ __('messages.back') }}
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('finance.bank-accounts.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('messages.account_code') ?? __('messages.code') }} <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" required placeholder="e.g. BANK-01">
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('messages.account_type') }} <span class="text-danger">*</span></label>
                        <select name="account_type" class="form-select" required>
                            <option value="bank" {{ old('account_type') == 'bank' ? 'selected' : '' }}>{{ __('messages.bank_account') ?? __('messages.bank') }}</option>
                            <option value="cash" {{ old('account_type') == 'cash' ? 'selected' : '' }}>{{ __('messages.cash_account') ?? __('messages.cash') }}</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('messages.name_en') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" value="{{ old('name_en') }}" required>
                        @error('name_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('messages.name_ar') }}</label>
                        <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar') }}">
                    </div>
                </div>

                <div class="row bank-fields">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('messages.bank_name') }}</label>
                        <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('messages.account_number') }}</label>
                        <input type="text" name="account_number" class="form-control" value="{{ old('account_number') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('messages.iban') }}</label>
                        <input type="text" name="iban" class="form-control" value="{{ old('iban') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('messages.gl_account') ?? __('messages.account') }} <span class="text-danger">*</span></label>
                        <select name="chart_of_account_id" class="form-select @error('chart_of_account_id') is-invalid @enderror" required>
                            <option value="">{{ __('messages.select_account') }}</option>
                            @foreach($coaAccounts as $coa)
                                <option value="{{ $coa->id }}" {{ old('chart_of_account_id') == $coa->id ? 'selected' : '' }}>
                                    {{ $coa->code }} - {{ App::getLocale() == 'ar' ? ($coa->name_ar ?? $coa->name_en) : $coa->name_en }}
                                </option>
                            @endforeach
                        </select>
                        @error('chart_of_account_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('messages.currency') }} <span class="text-danger">*</span></label>
                        <input type="text" name="currency_code" class="form-control" value="{{ old('currency_code', 'SAR') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('messages.opening_balance') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="opening_balance" class="form-control @error('opening_balance') is-invalid @enderror" value="{{ old('opening_balance', 0) }}" required>
                        @error('opening_balance') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
                    <a href="{{ route('finance.bank-accounts.index') }}" class="btn btn-link text-secondary">{{ __('messages.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.querySelector('select[name="account_type"]').addEventListener('change', function() {
        const bankFields = document.querySelector('.bank-fields');
        if (this.value === 'cash') {
            bankFields.style.display = 'none';
        } else {
            bankFields.style.display = 'flex';
        }
    });
</script>
@endsection

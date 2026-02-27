@extends('layouts.app')

@section('title', __('messages.add_asset') . ' - ' . __('messages.finance'))

@section('content')
<div class="container-fluid px-4">
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.finance') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('finance.fixed_assets.index') }}">{{ __('messages.fixed_assets') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('messages.add_asset') }}</li>
            </ol>
        </nav>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.add_asset') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('finance.fixed_assets.store') }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.name_en') }}</label>
                        <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" value="{{ old('name_en') }}" required>
                        @error('name_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.name_ar') }}</label>
                        <input type="text" name="name_ar" class="form-control @error('name_ar') is-invalid @enderror" value="{{ old('name_ar') }}">
                        @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.category') }}</label>
                        <select name="asset_category_id" class="form-select @error('asset_category_id') is-invalid @enderror" required>
                            <option value="">{{ __('messages.select_category') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('asset_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name_en }}
                                </option>
                            @endforeach
                        </select>
                        @error('asset_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.purchase_date') }}</label>
                        <input type="date" name="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror" value="{{ old('purchase_date') }}" required>
                        @error('purchase_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.purchase_cost') }}</label>
                        <input type="number" step="0.01" name="purchase_cost" class="form-control @error('purchase_cost') is-invalid @enderror" value="{{ old('purchase_cost') }}" required>
                        @error('purchase_cost') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.salvage_value') }}</label>
                        <input type="number" step="0.01" name="salvage_value" class="form-control @error('salvage_value') is-invalid @enderror" value="{{ old('salvage_value', 0) }}">
                        @error('salvage_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.useful_life_years') }}</label>
                        <input type="number" name="useful_life_years" class="form-control @error('useful_life_years') is-invalid @enderror" value="{{ old('useful_life_years') }}" required>
                        @error('useful_life_years') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.depreciation_method') }}</label>
                        <select name="depreciation_method" class="form-select @error('depreciation_method') is-invalid @enderror" required>
                            <option value="straight_line" {{ old('depreciation_method') == 'straight_line' ? 'selected' : '' }}>{{ __('messages.straight_line') }}</option>
                            <option value="declining_balance" {{ old('depreciation_method') == 'declining_balance' ? 'selected' : '' }}>{{ __('messages.declining_balance') }}</option>
                        </select>
                        @error('depreciation_method') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.asset_account') }}</label>
                        <select name="asset_account_id" class="form-select @error('asset_account_id') is-invalid @enderror" required>
                            <option value="">{{ __('messages.select_account') }}</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ old('asset_account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->code }} - {{ $account->name_en }}
                                </option>
                            @endforeach
                        </select>
                        @error('asset_account_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.accumulated_depreciation_account') }}</label>
                        <select name="accumulated_depreciation_account_id" class="form-select @error('accumulated_depreciation_account_id') is-invalid @enderror" required>
                            <option value="">{{ __('messages.select_account') }}</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ old('accumulated_depreciation_account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->code }} - {{ $account->name_en }}
                                </option>
                            @endforeach
                        </select>
                        @error('accumulated_depreciation_account_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.depreciation_expense_account') }}</label>
                        <select name="depreciation_expense_account_id" class="form-select @error('depreciation_expense_account_id') is-invalid @enderror" required>
                            <option value="">{{ __('messages.select_account') }}</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ old('depreciation_expense_account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->code }} - {{ $account->name_en }}
                                </option>
                            @endforeach
                        </select>
                        @error('depreciation_expense_account_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('finance.fixed_assets.index') }}" class="btn btn-secondary me-2">{{ __('messages.cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

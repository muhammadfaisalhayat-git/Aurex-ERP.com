@extends('layouts.app')

@section('title', __('messages.edit_asset') . ' - ' . __('messages.finance'))

@section('content')
<div class="container-fluid px-4">
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.finance') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('finance.fixed-assets.index') }}">{{ __('messages.fixed_assets') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('messages.edit_asset') }} ({{ $asset->code }})</li>
            </ol>
        </nav>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.edit_asset') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('finance.fixed-assets.update', $asset->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.name_en') }}</label>
                        <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" value="{{ old('name_en', $asset->name_en) }}" required>
                        @error('name_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.name_ar') }}</label>
                        <input type="text" name="name_ar" class="form-control @error('name_ar') is-invalid @enderror" value="{{ old('name_ar', $asset->name_ar) }}">
                        @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.category') }}</label>
                        <select name="asset_category_id" class="form-select @error('asset_category_id') is-invalid @enderror" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('asset_category_id', $asset->asset_category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name_en }}
                                </option>
                            @endforeach
                        </select>
                        @error('asset_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.purchase_date') }}</label>
                        <input type="date" name="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror" value="{{ old('purchase_date', $asset->purchase_date) }}" required>
                        @error('purchase_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.purchase_cost') }}</label>
                        <input type="number" step="0.01" name="purchase_cost" class="form-control @error('purchase_cost') is-invalid @enderror" value="{{ old('purchase_cost', $asset->purchase_cost) }}" required>
                        @error('purchase_cost') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.salvage_value') }}</label>
                        <input type="number" step="0.01" name="salvage_value" class="form-control @error('salvage_value') is-invalid @enderror" value="{{ old('salvage_value', $asset->salvage_value) }}">
                        @error('salvage_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.useful_life_years') }}</label>
                        <input type="number" name="useful_life_years" class="form-control @error('useful_life_years') is-invalid @enderror" value="{{ old('useful_life_years', $asset->useful_life_years) }}" required>
                        @error('useful_life_years') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.status') }}</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="active" {{ old('status', $asset->status) == 'active' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                            <option value="disposed" {{ old('status', $asset->status) == 'disposed' ? 'selected' : '' }}>{{ __('messages.disposed') }}</option>
                            <option value="written_off" {{ old('status', $asset->status) == 'written_off' ? 'selected' : '' }}>{{ __('messages.written_off') }}</option>
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.asset_account') }}</label>
                        <select name="asset_account_id" class="form-select @error('asset_account_id') is-invalid @enderror" required>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ old('asset_account_id', $asset->asset_account_id) == $account->id ? 'selected' : '' }}>
                                    {{ $account->code }} - {{ $account->name_en }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.accumulated_depreciation_account') }}</label>
                        <select name="accumulated_depreciation_account_id" class="form-select @error('accumulated_depreciation_account_id') is-invalid @enderror" required>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ old('accumulated_depreciation_account_id', $asset->accumulated_depreciation_account_id) == $account->id ? 'selected' : '' }}>
                                    {{ $account->code }} - {{ $account->name_en }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-danger" onclick="if(confirm('{{ __('messages.are_you_sure') }}')) { document.getElementById('delete-form').submit(); }">
                        <i class="fas fa-trash me-1"></i> {{ __('messages.delete') }}
                    </button>
                    <div class="d-flex">
                        <a href="{{ route('finance.fixed-assets.index') }}" class="btn btn-secondary me-2">{{ __('messages.cancel') }}</a>
                        <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
                    </div>
                </div>
            </form>
            <form id="delete-form" action="{{ route('finance.fixed-assets.destroy', $asset->id) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</div>
@endsection

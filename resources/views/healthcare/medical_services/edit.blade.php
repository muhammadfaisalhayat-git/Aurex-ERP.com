@extends('layouts.app')

@section('title', __('messages.edit_medical_service') . ' - ' . __('messages.healthcare_management'))

@section('content')
<div class="container-fluid px-4">
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.healthcare_management') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('healthcare.medical-services.index') }}">{{ __('messages.medical_services') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('messages.edit_medical_service') }}</li>
            </ol>
        </nav>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.edit_medical_service') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('healthcare.medical-services.update', $service->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.name_en') }}</label>
                        <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" value="{{ old('name_en', $service->name_en) }}" required>
                        @error('name_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.name_ar') }}</label>
                        <input type="text" name="name_ar" class="form-control @error('name_ar') is-invalid @enderror" value="{{ old('name_ar', $service->name_ar) }}">
                        @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.cost') }}</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="cost" class="form-control @error('cost') is-invalid @enderror" value="{{ old('cost', $service->cost) }}" required>
                        </div>
                        @error('cost') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.revenue_account') }}</label>
                        <select name="revenue_account_id" class="form-select @error('revenue_account_id') is-invalid @enderror" required>
                            @foreach($revenueAccounts as $account)
                                <option value="{{ $account->id }}" {{ old('revenue_account_id', $service->revenue_account_id) == $account->id ? 'selected' : '' }}>
                                    {{ $account->code }} - {{ $account->name_en }}
                                </option>
                            @endforeach
                        </select>
                        @error('revenue_account_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.is_active') }}</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ $service->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">{{ __('messages.active') }}</label>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">{{ __('messages.description') }}</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $service->description) }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-danger" onclick="if(confirm('{{ __('messages.are_you_sure') }}')) { document.getElementById('delete-form').submit(); }">
                        <i class="fas fa-trash me-1"></i> {{ __('messages.delete') }}
                    </button>
                    <div class="d-flex">
                        <a href="{{ route('healthcare.medical-services.index') }}" class="btn btn-secondary me-2">{{ __('messages.cancel') }}</a>
                        <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
                    </div>
                </div>
            </form>
            <form id="delete-form" action="{{ route('healthcare.medical-services.destroy', $service->id) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</div>
@endsection

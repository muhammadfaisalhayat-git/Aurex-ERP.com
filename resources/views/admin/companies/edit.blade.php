@extends('layouts.app')

@section('title', isset($company) ? __('Edit Company') : __('Add Company'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ isset($company) ? __('Edit Company') : __('Add Company') }}</h1>
        <div class="page-actions">
            <a href="{{ route('admin.companies.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> {{ __('Back to List') }}
            </a>
        </div>
    </div>

    <div class="card">
        <form action="{{ isset($company) ? route('admin.companies.update', $company) : route('admin.companies.store') }}"
            method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($company))
                @method('PUT')
            @endif

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Name (English)') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror"
                            value="{{ old('name_en', $company->name_en ?? '') }}" required>
                        @error('name_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Name (Arabic)') }}</label>
                        <input type="text" name="name_ar" class="form-control @error('name_ar') is-invalid @enderror"
                            value="{{ old('name_ar', $company->name_ar ?? '') }}">
                        @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Registration Number') }}</label>
                        <input type="text" name="registration_number"
                            class="form-control @error('registration_number') is-invalid @enderror"
                            value="{{ old('registration_number', $company->registration_number ?? '') }}">
                        @error('registration_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('Tax Number') }}</label>
                        <input type="text" name="tax_number" class="form-control @error('tax_number') is-invalid @enderror"
                            value="{{ old('tax_number', $company->tax_number ?? '') }}">
                        @error('tax_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('Currency') }} <span class="text-danger">*</span></label>
                        <select name="currency" class="form-select @error('currency') is-invalid @enderror" required>
                            <option value="USD" {{ old('currency', $company->currency ?? '') == 'USD' ? 'selected' : '' }}>USD
                            </option>
                            <option value="SAR" {{ old('currency', $company->currency ?? '') == 'SAR' ? 'selected' : '' }}>SAR
                            </option>
                            <option value="AED" {{ old('currency', $company->currency ?? '') == 'AED' ? 'selected' : '' }}>AED
                            </option>
                            <option value="EUR" {{ old('currency', $company->currency ?? '') == 'EUR' ? 'selected' : '' }}>EUR
                            </option>
                        </select>
                        @error('currency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Logo') }}</label>
                        <input type="file" name="logo" id="logo-input"
                            class="form-control @error('logo') is-invalid @enderror" accept="image/*"
                            onchange="previewLogo(this)">
                        @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror

                        {{-- Logo Preview --}}
                        <div id="logo-preview-wrapper"
                            style="margin-top: 12px; display: {{ isset($company) && $company->logo ? 'block' : 'none' }};">
                            <div
                                style="display: inline-block; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; background: #f8fafc;">
                                <img id="logo-preview-img"
                                    src="{{ isset($company) && $company->logo ? asset('storage/' . $company->logo) : '' }}"
                                    alt="Logo Preview"
                                    style="max-height: 80px; max-width: 220px; object-fit: contain; display: block;">
                                <div style="font-size: 11px; color: #94a3b8; margin-top: 6px; text-align: center;">
                                    {{ isset($company) && $company->logo ? __('Current Logo') : __('New Logo Preview') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        function previewLogo(input) {
                            if (input.files && input.files[0]) {
                                var reader = new FileReader();
                                reader.onload = function (e) {
                                    document.getElementById('logo-preview-img').src = e.target.result;
                                    document.getElementById('logo-preview-wrapper').style.display = 'block';
                                };
                                reader.readAsDataURL(input.files[0]);
                            }
                        }
                    </script>
                    <div class="col-md-6">
                        <div class="form-check mt-4">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', $company->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">
                    {{ __('Save Changes') }}
                </button>
            </div>
        </form>
    </div>
@endsection
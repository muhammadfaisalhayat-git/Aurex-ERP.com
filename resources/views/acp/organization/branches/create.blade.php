@extends('layouts.app')

@section('title', isset($branch) ? __('Edit Branch') : __('Add Branch'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ isset($branch) ? __('Edit Branch') : __('Add Branch') }}</h1>
        <div class="page-actions">
            <a href="{{ route('acp.organization.branches.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> {{ __('Back to List') }}
            </a>
        </div>
    </div>

    <div class="card">
        <form action="{{ isset($branch) ? route('acp.organization.branches.update', $branch) : route('acp.organization.branches.store') }}"
            method="POST">
            @csrf
            @if(isset($branch))
                @method('PUT')
            @endif

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">{{ __('Company') }} <span class="text-danger">*</span></label>
                        <select name="company_id" class="form-select @error('company_id') is-invalid @enderror" required>
                            <option value="">{{ __('Select Company') }}</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id', $branch->company_id ?? '') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                        @error('company_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ __('Code') }} <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                            value="{{ old('code', $branch->code ?? '') }}" required>
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ __('Name (English)') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror"
                            value="{{ old('name_en', $branch->name_en ?? '') }}" required>
                        @error('name_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ __('Name (Arabic)') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_ar" class="form-control @error('name_ar') is-invalid @enderror"
                            value="{{ old('name_ar', $branch->name_ar ?? '') }}" required>
                        @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('Email') }}</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $branch->email ?? '') }}">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('Phone') }}</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                            value="{{ old('phone', $branch->phone ?? '') }}">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">{{ __('Manager Name') }}</label>
                        <input type="text" name="manager_name"
                            class="form-control @error('manager_name') is-invalid @enderror"
                            value="{{ old('manager_name', $branch->manager_name ?? '') }}">
                        @error('manager_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">{{ __('Address') }}</label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                            rows="2">{{ old('address', $branch->address ?? '') }}</textarea>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <div class="form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', $branch->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> {{ __('Save Branch') }}
                </button>
            </div>
        </form>
    </div>
@endsection
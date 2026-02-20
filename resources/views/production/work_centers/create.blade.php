@extends('layouts.app')

@section('title', __('messages.create') . ' ' . __('messages.work_center') . ' - ' . __('messages.production'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('messages.new_work_center') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('production.work-centers.index') }}">{{ __('messages.work_centers') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('messages.create') }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">{{ __('messages.work_center_details') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('production.work-centers.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.code') }} <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="WC-XXXX" required>
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Main Assembly Line" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.capacity_units_hr_label') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-chart-line"></i></span>
                                <input type="number" step="0.01" name="capacity" class="form-control @error('capacity') is-invalid @enderror" value="{{ old('capacity', 100) }}" required>
                            </div>
                            @error('capacity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label d-block">{{ __('messages.status') }}</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" checked>
                                <label class="form-check-label" for="is_active">{{ __('messages.active') }}</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ __('messages.description') }}</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="{{ __('messages.briefly_describe_work_center') }}">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                        <a href="{{ route('production.work-centers.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>{{ __('messages.cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ __('messages.save_work_center') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', __('messages.register_machine') . ' - ' . __('messages.production'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('messages.register_machine') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('production.machines.index') }}">{{ __('messages.machines') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('messages.register') }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-xl-9">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">{{ __('messages.machine_information') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('production.machines.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.work_center') }} <span class="text-danger">*</span></label>
                            <select name="work_center_id" class="form-select @error('work_center_id') is-invalid @enderror" required>
                                <option value="">{{ __('messages.select_work_center') }}</option>
                                @foreach($workCenters as $wc)
                                    <option value="{{ $wc->id }}" {{ old('work_center_id') == $wc->id ? 'selected' : '' }}>
                                        {{ $wc->name }} ({{ $wc->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('work_center_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.machine_code') }} <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="MCH-XXXX" required>
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">{{ __('messages.machine_name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="CNC Laser Cutter" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.brand') }}</label>
                            <input type="text" name="brand" class="form-control" value="{{ old('brand') }}" placeholder="Trumpf">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.model') }}</label>
                            <input type="text" name="model" class="form-control" value="{{ old('model') }}" placeholder="TruLaser 3030">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.hourly_operating_cost') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">{{ __('messages.sar') ?? 'SAR' }}</span>
                                <input type="number" step="0.01" name="hourly_cost" class="form-control @error('hourly_cost') is-invalid @enderror" value="{{ old('hourly_cost', 0) }}" required>
                            </div>
                            @error('hourly_cost') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.initial_status') }}</label>
                            <select name="status" class="form-select">
                                <option value="available" selected>{{ __('messages.available') }}</option>
                                <option value="maintenance">{{ __('messages.under_maintenance') }}</option>
                                <option value="busy">{{ __('messages.busy') }}</option>
                                <option value="offline">{{ __('messages.offline') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                        <a href="{{ route('production.machines.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>{{ __('messages.cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check-circle me-2"></i>{{ __('messages.register_machine') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

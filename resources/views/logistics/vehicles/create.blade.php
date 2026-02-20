@extends('layouts.app')

@section('title', __('messages.register_vehicle') . ' - ' . __('messages.logistics'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('messages.register_vehicle') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('logistics.vehicles.index') }}">{{ __('messages.vehicles') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('messages.register') }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-xl-9">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">{{ __('messages.vehicle_specifications') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('logistics.vehicles.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.plate_number') }} <span class="text-danger">*</span></label>
                            <input type="text" name="plate_number" class="form-control @error('plate_number') is-invalid @enderror" value="{{ old('plate_number') }}" placeholder="ABC-1234" required>
                            @error('plate_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">{{ __('messages.brand') }} <span class="text-danger">*</span></label>
                            <input type="text" name="brand" class="form-control @error('brand') is-invalid @enderror" value="{{ old('brand') }}" placeholder="Toyota" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">{{ __('messages.model') }} <span class="text-danger">*</span></label>
                            <input type="text" name="model" class="form-control @error('model') is-invalid @enderror" value="{{ old('model') }}" placeholder="Hilux" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.vehicle_type') ?? __('messages.type') }} <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="van">{{ __('messages.van') }}</option>
                                <option value="truck" selected>{{ __('messages.truck') }}</option>
                                <option value="pickup">{{ __('messages.pickup') }}</option>
                                <option value="motorcycle">{{ __('messages.motorcycle') }}</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.fuel_type') }} <span class="text-danger">*</span></label>
                            <select name="fuel_type" class="form-select" required>
                                <option value="diesel" selected>{{ __('messages.diesel') }}</option>
                                <option value="petrol">{{ __('messages.petrol') }}</option>
                                <option value="electric">{{ __('messages.electric') }}</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.max_payload') }}</label>
                            <input type="number" name="max_payload" class="form-control" value="{{ old('max_payload') }}" placeholder="2000">
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                        <a href="{{ route('logistics.vehicles.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>{{ __('messages.cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ __('messages.register_vehicle') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

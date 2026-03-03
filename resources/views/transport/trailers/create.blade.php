@extends('layouts.app')

@section('title', __('messages.transport') . ' - ' . __('messages.add_trailer'))

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('messages.add_trailer') }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('transport.trailers.index') }}">{{ __('messages.trailers') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('messages.add_new') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('transport.trailers.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('messages.code') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                                    value="{{ old('code') }}" required>
                                @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('messages.plate_number') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="plate_number"
                                    class="form-control @error('plate_number') is-invalid @enderror"
                                    value="{{ old('plate_number') }}" required>
                                @error('plate_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('messages.trailer_type') }} <span
                                        class="text-danger">*</span></label>
                                <select name="trailer_type" class="form-select @error('trailer_type') is-invalid @enderror"
                                    required>
                                    <option value="">{{ __('messages.select_type') }}</option>
                                    <option value="Flatbed" {{ old('trailer_type') == 'Flatbed' ? 'selected' : '' }}>Flatbed
                                    </option>
                                    <option value="Refrigerated" {{ old('trailer_type') == 'Refrigerated' ? 'selected' : '' }}>Refrigerated</option>
                                    <option value="Tanker" {{ old('trailer_type') == 'Tanker' ? 'selected' : '' }}>Tanker
                                    </option>
                                    <option value="Lowboy" {{ old('trailer_type') == 'Lowboy' ? 'selected' : '' }}>Lowboy
                                    </option>
                                </select>
                                @error('trailer_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('messages.capacity_kg') }} <span
                                        class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="capacity_kg"
                                    class="form-control @error('capacity_kg') is-invalid @enderror"
                                    value="{{ old('capacity_kg') }}" required>
                                @error('capacity_kg') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <hr class="my-3">

                            <div class="col-md-6">
                                <label class="form-label">{{ __('messages.driver_name') }}</label>
                                <input type="text" name="driver_name"
                                    class="form-control @error('driver_name') is-invalid @enderror"
                                    value="{{ old('driver_name') }}">
                                @error('driver_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('messages.driver_phone') }}</label>
                                <input type="text" name="driver_phone"
                                    class="form-control @error('driver_phone') is-invalid @enderror"
                                    value="{{ old('driver_phone') }}">
                                @error('driver_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('messages.license_number') }}</label>
                                <input type="text" name="license_number"
                                    class="form-control @error('license_number') is-invalid @enderror"
                                    value="{{ old('license_number') }}">
                                @error('license_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('messages.license_expiry') }}</label>
                                <input type="date" name="license_expiry"
                                    class="form-control @error('license_expiry') is-invalid @enderror"
                                    value="{{ old('license_expiry') }}">
                                @error('license_expiry') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('messages.status') }} <span
                                        class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>
                                        {{ __('messages.available') }}</option>
                                    <option value="busy" {{ old('status') == 'busy' ? 'selected' : '' }}>
                                        {{ __('messages.busy') }}</option>
                                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>
                                        {{ __('messages.maintenance') }}</option>
                                    <option value="retired" {{ old('status') == 'retired' ? 'selected' : '' }}>
                                        {{ __('messages.retired') }}</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary px-5">
                                    <i class="fas fa-save me-2"></i>{{ __('messages.save') }}
                                </button>
                                <a href="{{ route('transport.trailers.index') }}"
                                    class="btn btn-outline-secondary px-4">{{ __('messages.cancel') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
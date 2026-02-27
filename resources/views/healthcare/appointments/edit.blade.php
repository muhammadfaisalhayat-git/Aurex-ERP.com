@extends('layouts.app')

@section('title', __('messages.edit_appointment') . ' - ' . __('messages.healthcare_management'))

@section('content')
<div class="container-fluid px-4">
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.healthcare_management') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('healthcare.appointments.index') }}">{{ __('messages.appointments') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('messages.edit_appointment') }}</li>
            </ol>
        </nav>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.edit_appointment') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('healthcare.appointments.update', $appointment->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.patient') }}</label>
                        <select name="patient_id" class="form-select @error('patient_id') is-invalid @enderror" required>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ old('patient_id', $appointment->patient_id) == $patient->id ? 'selected' : '' }}>
                                    {{ App::getLocale() == 'ar' ? ($patient->name_ar ?? $patient->name_en) : $patient->name_en }} ({{ $patient->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('patient_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.doctor') }}</label>
                        <select name="doctor_id" class="form-select @error('doctor_id') is-invalid @enderror" required>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ old('doctor_id', $appointment->doctor_id) == $doctor->id ? 'selected' : '' }}>
                                    {{ App::getLocale() == 'ar' ? ($doctor->name_ar ?? $doctor->name_en) : $doctor->name_en }} - {{ $doctor->specialization }}
                                </option>
                            @endforeach
                        </select>
                        @error('doctor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.service') }}</label>
                        <select name="service_id" class="form-select @error('service_id') is-invalid @enderror" required>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" {{ old('service_id', $appointment->service_id) == $service->id ? 'selected' : '' }}>
                                    {{ App::getLocale() == 'ar' ? ($service->name_ar ?? $service->name_en) : $service->name_en }} ({{ number_format($service->cost, 2) }})
                                </option>
                            @endforeach
                        </select>
                        @error('service_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.appointment_date') }}</label>
                        <input type="datetime-local" name="appointment_date" class="form-control @error('appointment_date') is-invalid @enderror" value="{{ old('appointment_date', date('Y-m-d\TH:i', strtotime($appointment->appointment_date))) }}" required>
                        @error('appointment_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.status') }}</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="scheduled" {{ old('status', $appointment->status) == 'scheduled' ? 'selected' : '' }}>{{ __('messages.scheduled') }}</option>
                            <option value="confirmed" {{ old('status', $appointment->status) == 'confirmed' ? 'selected' : '' }}>{{ __('messages.confirmed') }}</option>
                            <option value="completed" {{ old('status', $appointment->status) == 'completed' ? 'selected' : '' }}>{{ __('messages.completed') }}</option>
                            <option value="cancelled" {{ old('status', $appointment->status) == 'cancelled' ? 'selected' : '' }}>{{ __('messages.cancelled') }}</option>
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">{{ __('messages.notes') }}</label>
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $appointment->notes) }}</textarea>
                    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-danger" onclick="if(confirm('{{ __('messages.are_you_sure') }}')) { document.getElementById('delete-form').submit(); }">
                        <i class="fas fa-trash me-1"></i> {{ __('messages.delete') }}
                    </button>
                    <div class="d-flex">
                        <a href="{{ route('healthcare.appointments.index') }}" class="btn btn-secondary me-2">{{ __('messages.cancel') }}</a>
                        <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
                    </div>
                </div>
            </form>
            <form id="delete-form" action="{{ route('healthcare.appointments.destroy', $appointment->id) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</div>
@endsection

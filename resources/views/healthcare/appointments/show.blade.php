@extends('layouts.app')

@section('title', __('messages.appointment_details') . ' - ' . __('messages.healthcare_management'))

@section('content')
<div class="container-fluid px-4">
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.healthcare_management') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('healthcare.appointments.index') }}">{{ __('messages.appointments') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('messages.appointment_details') }}</li>
            </ol>
        </nav>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.appointment_details') }}</h6>
            <div>
                <a href="{{ route('healthcare.appointments.print', $appointment->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary me-1">
                    <i class="fas fa-print me-1"></i> {{ __('messages.print') }}
                </a>
                @if($appointment->billing_status !== 'invoiced')
                    <form action="{{ route('healthcare.appointments.invoice', $appointment->id) }}" method="POST" class="d-inline me-1">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('{{ __('messages.are_you_sure_generate_bill') }}')">
                            <i class="fas fa-file-invoice-dollar me-1"></i> {{ __('messages.generate_bill') }}
                        </button>
                    </form>
                @endif
                <a href="{{ route('healthcare.appointments.edit', $appointment->id) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-edit me-1"></i> {{ __('messages.edit') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="small text-muted mb-1">{{ __('messages.patient') }}</label>
                    <div class="h5">
                        <a href="{{ route('healthcare.patients.show', $appointment->patient_id) }}">
                            {{ $appointment->patient->name_en }} ({{ $appointment->patient->code }})
                        </a>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="small text-muted mb-1">{{ __('messages.doctor') }}</label>
                    <div class="h5">
                        <a href="{{ route('healthcare.doctors.show', $appointment->doctor_id) }}">
                            {{ $appointment->doctor->name_en }} - {{ $appointment->doctor->specialization }}
                        </a>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="small text-muted mb-1">{{ __('messages.service') }}</label>
                    <div class="h5">{{ $appointment->service->name_en }}</div>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="small text-muted mb-1">{{ __('messages.appointment_date') }}</label>
                    <div class="h5">{{ $appointment->appointment_date }}</div>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="small text-muted mb-1">{{ __('messages.status') }}</label>
                    <div>
                        <span class="badge bg-{{ $appointment->status == 'completed' ? 'success' : ($appointment->status == 'cancelled' ? 'danger' : 'info') }} h5">
                            {{ __('messages.' . $appointment->status) }}
                        </span>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="small text-muted mb-1">{{ __('messages.billing_status') }}</label>
                    <div>
                        <span class="badge bg-{{ $appointment->billing_status == 'invoiced' ? 'success' : 'secondary' }} h5">
                            {{ __('messages.' . ($appointment->billing_status == 'invoiced' ? 'invoiced' : 'unbilled')) }}
                        </span>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="small text-muted mb-1">{{ __('messages.total_amount') }}</label>
                    <div class="h5 text-primary font-weight-bold">{{ number_format($appointment->total_amount, 2) }}</div>
                </div>
            </div>
            <div class="mb-0">
                <label class="small text-muted mb-1">{{ __('messages.notes') }}</label>
                <div class="p-3 bg-light rounded text-gray-700">
                    {{ $appointment->notes ?? __('messages.no_notes') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

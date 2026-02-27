@extends('layouts.app')

@section('title', __('messages.medical_service_details') . ' - ' . __('messages.healthcare_management'))

@section('content')
<div class="container-fluid px-4">
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.healthcare_management') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('healthcare.medical-services.index') }}">{{ __('messages.medical_services') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('messages.medical_service_details') }}</li>
            </ol>
        </nav>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.medical_service_details') }}</h6>
            <div>
                <a href="{{ route('healthcare.medical-services.edit', $service->id) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-edit me-1"></i> {{ __('messages.edit') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="small text-muted mb-1">{{ __('messages.name_en') }}</label>
                    <div class="h5">{{ $service->name_en }}</div>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="small text-muted mb-1">{{ __('messages.name_ar') }}</label>
                    <div class="h5">{{ $service->name_ar ?? '-' }}</div>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="small text-muted mb-1">{{ __('messages.service_cost') }}</label>
                    <div class="h5 text-primary font-weight-bold">{{ number_format($service->cost, 2) }}</div>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="small text-muted mb-1">{{ __('messages.status') }}</label>
                    <div>
                        <span class="badge bg-{{ $service->is_active ? 'success' : 'danger' }}">
                            {{ $service->is_active ? __('messages.active') : __('messages.inactive') }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="mb-0">
                <label class="small text-muted mb-1">{{ __('messages.description') }}</label>
                <div class="p-3 bg-light rounded text-gray-700">
                    {{ $service->description ?? __('messages.no_description') }}
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.appointments_for_this_service') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.patient') }}</th>
                            <th>{{ __('messages.doctor') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($service->appointments as $appointment)
                            <tr>
                                <td>{{ $appointment->appointment_date }}</td>
                                <td>{{ App::getLocale() == 'ar' ? ($appointment->patient->name_ar ?? $appointment->patient->name_en) : $appointment->patient->name_en }}</td>
                                <td>{{ App::getLocale() == 'ar' ? ($appointment->doctor->name_ar ?? $appointment->doctor->name_en) : $appointment->doctor->name_en }}</td>
                                <td>
                                    <span class="badge bg-{{ $appointment->status == 'completed' ? 'success' : ($appointment->status == 'cancelled' ? 'danger' : 'info') }}">
                                        {{ __('messages.' . $appointment->status) }}
                                    </span>
                                </td>
                                <td class="text-end">{{ number_format($appointment->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">{{ __('messages.no_appointments_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

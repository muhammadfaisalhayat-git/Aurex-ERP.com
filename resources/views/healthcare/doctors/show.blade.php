@extends('layouts.app')

@section('title', __('messages.doctor_details') . ' - ' . __('messages.healthcare_management'))

@section('content')
<div class="container-fluid px-4">
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.healthcare_management') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('healthcare.doctors.index') }}">{{ __('messages.doctors') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('messages.doctor_details') }} ({{ $doctor->code }})</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-xl-4 col-md-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.basic_info') }}</h6>
                    <a href="{{ route('healthcare.doctors.edit', $doctor->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-edit me-1"></i> {{ __('messages.edit') }}
                    </a>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="bg-light d-inline-block rounded-circle p-4 mb-2">
                            <i class="fas fa-user-md fa-4x text-gray-400"></i>
                        </div>
                        <h4 class="mb-0">{{ App::getLocale() == 'ar' ? ($doctor->name_ar ?? $doctor->name_en) : $doctor->name_en }}</h4>
                        <span class="text-muted"><code>{{ $doctor->code }}</code></span>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="small text-muted mb-1">{{ __('messages.specialization') }}</label>
                        <div class="h6 mb-0">{{ $doctor->specialization }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted mb-1">{{ __('messages.phone') }}</label>
                        <div class="h6 mb-0">{{ $doctor->phone ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted mb-1">{{ __('messages.email') }}</label>
                        <div class="h6 mb-0">{{ $doctor->email ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted mb-1">{{ __('messages.bio') }}</label>
                        <div class="h6 mb-0 small text-gray-600">{{ $doctor->bio ?? '-' }}</div>
                    </div>
                    <div class="mb-0">
                        <label class="small text-muted mb-1">{{ __('messages.status') }}</label>
                        <div>
                            <span class="badge bg-{{ $doctor->is_active ? 'success' : 'danger' }}">
                                {{ $doctor->is_active ? __('messages.active') : __('messages.inactive') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-md-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.doctor_appointments') }}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.patient') }}</th>
                                    <th>{{ __('messages.service') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($doctor->appointments as $appointment)
                                    <tr>
                                        <td>{{ $appointment->appointment_date }}</td>
                                        <td>{{ App::getLocale() == 'ar' ? ($appointment->patient->name_ar ?? $appointment->patient->name_en) : $appointment->patient->name_en }}</td>
                                        <td>{{ App::getLocale() == 'ar' ? ($appointment->service->name_ar ?? $appointment->service->name_en) : $appointment->service->name_en }}</td>
                                        <td>
                                            <span class="badge bg-{{ $appointment->status == 'completed' ? 'success' : ($appointment->status == 'cancelled' ? 'danger' : 'info') }}">
                                                {{ __('messages.' . $appointment->status) }}
                                            </span>
                                        </td>
                                        <td class="text-end">{{ number_format($appointment->total_amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">{{ __('messages.no_records_found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

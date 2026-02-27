@extends('layouts.app')

@section('title', __('messages.appointment_list') . ' - ' . __('messages.healthcare_management'))

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.appointments') }}</h1>
        <a href="{{ route('healthcare.appointments.create') }}" class="btn btn-primary">
            <i class="fas fa-calendar-plus me-1"></i> {{ __('messages.book_appointment') }}
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.appointment_list') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.patient_name') }}</th>
                            <th>{{ __('messages.doctor_name') }}</th>
                            <th>{{ __('messages.service_name') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.billing_status') }}</th>
                            <th>{{ __('messages.total_amount') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointments as $appointment)
                            <tr>
                                <td>{{ $appointment->appointment_date->format('Y-m-d H:i') }}</td>
                                <td>{{ $appointment->patient->name_en }}</td>
                                <td>{{ $appointment->doctor->name_en }}</td>
                                <td>{{ $appointment->service->name_en ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $appointment->status == 'completed' ? 'success' : ($appointment->status == 'cancelled' ? 'danger' : 'info') }}">
                                        {{ __('messages.' . $appointment->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $appointment->billing_status == 'invoiced' ? 'success' : 'secondary' }}">
                                        {{ __('messages.' . ($appointment->billing_status == 'invoiced' ? 'invoiced' : 'unbilled')) }}
                                    </span>
                                </td>
                                <td class="text-end font-weight-bold">{{ number_format($appointment->total_amount, 2) }}</td>
                                <td>
                                    <div class="d-flex order-actions">
                                        <a href="{{ route('healthcare.appointments.show', $appointment->id) }}" class="btn btn-sm btn-outline-primary me-1" title="{{ __('messages.view') }}"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('healthcare.appointments.edit', $appointment->id) }}" class="btn btn-sm btn-outline-info me-1" title="{{ __('messages.edit') }}"><i class="fas fa-edit"></i></a>
                                        <a href="{{ route('healthcare.appointments.print', $appointment->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary me-1" title="{{ __('messages.print') }}"><i class="fas fa-print"></i></a>
                                        @if($appointment->billing_status !== 'invoiced')
                                            <form action="{{ route('healthcare.appointments.invoice', $appointment->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="{{ __('messages.generate_bill') }}" onclick="return confirm('{{ __('messages.are_you_sure_generate_bill') }}')">
                                                    <i class="fas fa-file-invoice-dollar"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

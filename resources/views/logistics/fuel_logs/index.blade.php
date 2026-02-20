@extends('layouts.app')

@section('title', __('messages.fuel_monitoring') . ' - ' . __('messages.logistics'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('messages.fuel_logs') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('logistics.vehicles.index') }}">{{ __('messages.logistics') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('messages.fuel_logs') }}</li>
            </ol>
        </nav>
    </div>
    <div class="page-actions">
        <a href="{{ route('logistics.fuel-logs.create') }}" class="btn btn-primary">
            <i class="fas fa-gas-pump me-2"></i>{{ __('messages.record_fuel') }}
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title">{{ __('messages.recent_transactions') }}</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>{{ __('messages.date') }}</th>
                        <th>{{ __('messages.vehicle') }}</th>
                        <th>{{ __('messages.liters') }}</th>
                        <th>{{ __('messages.cost_per_liter') }}</th>
                        <th>{{ __('messages.total_cost') }}</th>
                        <th>{{ __('messages.odometer') }}</th>
                        <th>{{ __('messages.logged_by') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($logs) > 0)
                        @foreach($logs as $log)
                            <tr>
                                <td>{{ $log->entry_date->format('Y-m-d') }}</td>
                                <td>
                                    <div class="fw-bold">{{ $log->vehicle->plate_number }}</div>
                                    <div class="text-muted small">{{ $log->vehicle->brand }}</div>
                                </td>
                                <td>{{ number_format($log->liters, 2) }} L</td>
                                <td>{{ number_format($log->cost_per_liter, 2) }}</td>
                                <td>
                                    <div class="fw-bold text-danger">{{ number_format($log->total_cost, 2) }}</div>
                                    <small class="text-muted">{{ __('messages.posted_to_gl') }}</small>
                                </td>
                                <td>{{ number_format($log->odometer_reading) }} km</td>
                                <td>{{ $log->logger->name ?? __('messages.user') }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-receipt fa-3x mb-3"></i>
                                    <p>{{ __('messages.no_fuel_logs_found') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

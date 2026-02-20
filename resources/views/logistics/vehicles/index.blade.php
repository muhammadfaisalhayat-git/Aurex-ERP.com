@extends('layouts.app')

@section('title', __('messages.fleet_management') . ' - ' . __('messages.delivery_vehicles'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('messages.delivery_vehicles') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('messages.logistics') }}</li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('messages.vehicles') }}</li>
            </ol>
        </nav>
    </div>
    <div class="page-actions">
        <a href="{{ route('logistics.vehicles.create') }}" class="btn btn-primary">
            <i class="fas fa-truck me-2"></i>{{ __('messages.register_vehicle') }}
        </a>
    </div>
</div>

    <turbo-frame id="vehicles_frame" data-turbo-action="advance">
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted small text-uppercase mb-1">{{ __('messages.total_fleet') }}</h6>
                                <h4 class="mb-0">{{ $vehicles->total() }}</h4>
                            </div>
                            <div class="ms-3 bg-primary-subtle p-3 rounded-circle text-primary">
                                <i class="fas fa-truck fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">{{ __('messages.vehicle_list') }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('messages.plate_number') }}</th>
                                <th>{{ __('messages.vehicle_info') }}</th>
                                <th>{{ __('messages.type') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.payload') }}</th>
                                <th class="text-end">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($vehicles) > 0)
                                @foreach($vehicles as $vehicle)
                                    <tr>
                                        <td><span class="fw-bold text-dark">{{ $vehicle->plate_number }}</span></td>
                                        <td>
                                            <div>{{ $vehicle->brand }} {{ $vehicle->model }}</div>
                                            <small class="text-muted">{{ __('messages.' . $vehicle->fuel_type) }}</small>
                                        </td>
                                        <td><span class="badge bg-light text-secondary border">{{ __('messages.' . $vehicle->type) }}</span></td>
                                        <td>
                                            @php
                                                $statusClasses = [
                                                    'available' => 'bg-success',
                                                    'in_transit' => 'bg-primary',
                                                    'maintenance' => 'bg-warning text-dark',
                                                    'retired' => 'bg-danger'
                                                ];
                                            @endphp
                                            <span class="badge {{ $statusClasses[$vehicle->status] ?? 'bg-secondary' }}">
                                                {{ __('messages.' . $vehicle->status) }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($vehicle->max_payload, 2) }} {{ __('messages.kg') }}</td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <a href="{{ route('logistics.vehicles.edit', $vehicle) }}" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   title="{{ __('messages.edit') }}"
                                                   data-turbo-frame="main-frame">
                                                   <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-truck-pickup fa-3x mb-3"></i>
                                            <p>{{ __('messages.no_vehicles_found') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @if($vehicles->hasPages())
                <div class="card-footer bg-white border-top-0">
                    {{ $vehicles->links() }}
                </div>
            @endif
        </div>
    </turbo-frame>
@endsection

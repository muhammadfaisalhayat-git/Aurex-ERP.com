@extends('layouts.app')

@section('title', __('messages.transport') . ' - ' . __('messages.orders'))

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('messages.transport_orders') }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('messages.transport') }}</li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('messages.orders') }}</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <a href="{{ route('transport.orders.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>{{ __('messages.add_order') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">{{ __('messages.order_list') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('messages.document_number') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.route') }}</th>
                            <th>{{ __('messages.trailer_vehicle') }}</th>
                            <th>{{ __('messages.driver') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th class="text-end">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td><span class="fw-bold">{{ $order->document_number }}</span></td>
                                <td>{{ $order->order_date->format('Y-m-d') }}</td>
                                <td>
                                    <div class="small fw-semibold">{{ $order->route_from }}</div>
                                    <i class="fas fa-arrow-down small text-muted my-1 mx-2"></i>
                                    <div class="small fw-semibold">{{ $order->route_to }}</div>
                                </td>
                                <td>
                                    @if($order->trailer)
                                        <div><i class="fas fa-trailer me-1 text-muted"></i>{{ $order->trailer->plate_number }}</div>
                                    @endif
                                    @if($order->vehicle)
                                        <div><i class="fas fa-truck me-1 text-muted"></i>{{ $order->vehicle->plate_number }}</div>
                                    @endif
                                    @if(!$order->trailer && !$order->vehicle)
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $order->driver->name ?? '-' }}</td>
                                <td>
                                    @php
                                        $statusClasses = [
                                            'draft' => 'bg-light text-dark border',
                                            'confirmed' => 'bg-info',
                                            'in_transit' => 'bg-primary',
                                            'completed' => 'bg-success',
                                            'cancelled' => 'bg-danger'
                                        ];
                                    @endphp
                                    <span class="badge {{ $statusClasses[$order->status] ?? 'bg-secondary' }}">
                                        {{ __('messages.' . $order->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('transport.orders.show', $order) }}"
                                            class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($order->status == 'draft')
                                            <a href="{{ route('transport.orders.edit', $order) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-dolly fa-3x mb-3"></i>
                                        <p>{{ __('messages.no_orders_found') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($orders->hasPages())
            <div class="card-footer bg-white">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection
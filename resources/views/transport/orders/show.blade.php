@extends('layouts.app')

@section('title', __('messages.transport') . ' - ' . __('messages.order_details'))

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('messages.order_details') }}: {{ $order->document_number }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('transport.orders.index') }}">{{ __('messages.transport_orders') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('messages.details') }}</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <div class="btn-group">
                @if($order->status == 'draft')
                    <form action="{{ route('transport.orders.confirm', $order) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success me-2">
                            <i class="fas fa-check-circle me-1"></i>{{ __('messages.confirm_order') }}
                        </button>
                    </form>
                    <a href="{{ route('transport.orders.edit', $order) }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-edit me-1"></i>{{ __('messages.edit') }}
                    </a>
                @endif
                @if($order->status == 'confirmed')
                    <form action="{{ route('transport.orders.complete', $order) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-flag-checkered me-1"></i>{{ __('messages.mark_as_completed') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Quick Info Cards -->
        <div class="col-md-3">
            <div class="card h-100 border-start border-primary border-4">
                <div class="card-body">
                    <div class="text-muted small text-uppercase mb-1">{{ __('messages.status') }}</div>
                    <h5 class="mb-0">{{ __('messages.' . $order->status) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-start border-info border-4">
                <div class="card-body">
                    <div class="text-muted small text-uppercase mb-1">{{ __('messages.scheduled_date') }}</div>
                    <h5 class="mb-0">{{ $order->scheduled_date->format('Y-m-d') }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-start border-warning border-4">
                <div class="card-body">
                    <div class="text-muted small text-uppercase mb-1">{{ __('messages.origin') }}</div>
                    <h5 class="mb-0">{{ $order->route_from }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-start border-danger border-4">
                <div class="card-body">
                    <div class="text-muted small text-uppercase mb-1">{{ __('messages.destination') }}</div>
                    <h5 class="mb-0">{{ $order->route_to }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">{{ __('messages.loading_details') }}</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('messages.product') }}</th>
                                <th class="text-center">{{ __('messages.quantity') }}</th>
                                <th>{{ __('messages.notes') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td class="text-center fw-bold">{{ number_format($item->quantity, 3) }}</td>
                                    <td>{{ $item->notes ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if($order->notes)
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">{{ __('messages.additional_notes') }}</h5>
                    </div>
                    <div class="card-body italic text-muted">
                        {{ $order->notes }}
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">{{ __('messages.assignment_details') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="small text-muted mb-1">{{ __('messages.driver') }}</label>
                        <div class="d-flex align-items-center">
                            <div class="bg-light p-2 rounded me-3 text-primary"><i class="fas fa-id-card fa-lg"></i></div>
                            <div>
                                <div class="fw-bold">{{ $order->driver->name ?? '-' }}</div>
                                <div class="small text-muted">{{ $order->driver->phone ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="small text-muted mb-1">{{ __('messages.trailer') }}</label>
                        <div class="d-flex align-items-center">
                            <div class="bg-light p-2 rounded me-3 text-secondary"><i class="fas fa-trailer fa-lg"></i></div>
                            @if($order->trailer)
                                <div>
                                    <div class="fw-bold">{{ $order->trailer->plate_number }}</div>
                                    <div class="small text-muted">{{ $order->trailer->trailer_type }}
                                        ({{ $order->trailer->code }})</div>
                                </div>
                            @else
                                <div class="text-muted">-</div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="small text-muted mb-1">{{ __('messages.vehicle') }}</label>
                        <div class="d-flex align-items-center">
                            <div class="bg-light p-2 rounded me-3 text-info"><i class="fas fa-truck fa-lg"></i></div>
                            @if($order->vehicle)
                                <div>
                                    <div class="fw-bold">{{ $order->vehicle->plate_number }}</div>
                                    <div class="small text-muted">{{ $order->vehicle->brand }} {{ $order->vehicle->model }}
                                    </div>
                                </div>
                            @else
                                <div class="text-muted">-</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">{{ __('messages.system_info') }}</h5>
                </div>
                <div class="card-body small">
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('messages.created_at') }}:</span>
                        <span class="text-muted">{{ $order->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    @if($order->closed_at)
                        <div class="d-flex justify-content-between mb-0">
                            <span>{{ __('messages.completed_at') }}:</span>
                            <span class="text-success fw-bold">{{ $order->closed_at->format('Y-m-d H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
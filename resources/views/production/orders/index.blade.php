@extends('layouts.app')

@section('title', __('messages.production_orders') . ' - ' . __('messages.production'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('messages.production_orders') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('messages.production') }}</li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('messages.orders') }}</li>
            </ol>
        </nav>
    </div>
    <div class="page-actions">
        <a href="{{ route('production.orders.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>{{ __('messages.new_production_order') }}
        </a>
    </div>
</div>

    <turbo-frame id="production_orders_frame" data-turbo-action="advance">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">{{ __('messages.active_orders') }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('messages.order_no') }}</th>
                                <th>{{ __('messages.product') }}</th>
                                <th>{{ __('messages.quantity') }}</th>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.total_cost') }}</th>
                                <th class="text-end">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($orders) > 0)
                                @foreach($orders as $order)
                                    <tr>
                                        <td><a href="{{ route('production.orders.show', $order) }}" class="fw-bold text-decoration-none" data-turbo-frame="main-frame">{{ $order->document_number }}</a></td>
                                        <td>
                                            <div class="fw-semibold">{{ $order->product->name ?? __('messages.unknown') }}</div>
                                            <div class="text-muted small">{{ $order->product->code ?? '' }}</div>
                                        </td>
                                        <td>{{ number_format($order->quantity, 3) }}</td>
                                        <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            @php
                                                $statusClasses = [
                                                    'draft' => 'badge-draft text-dark border',
                                                    'confirmed' => 'bg-info-subtle text-info border border-info-subtle',
                                                    'in_progress' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                                    'completed' => 'bg-success-subtle text-success border border-success-subtle',
                                                    'cancelled' => 'bg-danger-subtle text-danger border border-danger-subtle'
                                                ];
                                            @endphp
                                            <span class="badge {{ $statusClasses[$order->status] ?? 'bg-secondary' }}">
                                                {{ __('messages.' . $order->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-medium text-dark">{{ number_format($order->total_cost, 2) }}</span>
                                            <small class="text-muted">{{ __('messages.sar') ?? 'SAR' }}</small>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <a href="{{ route('production.orders.show', $order) }}" class="btn btn-sm btn-outline-primary" title="{{ __('messages.details') }}" data-turbo-frame="main-frame">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($order->status === 'draft')
                                                    <a href="#" class="btn btn-sm btn-outline-info" title="{{ __('messages.edit') }}" data-turbo-frame="main-frame">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                                            <p class="mb-0">{{ __('messages.no_production_orders_found') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @if($orders->hasPages())
                <div class="card-footer bg-white border-top-0">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </turbo-frame>
@endsection

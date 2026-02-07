@extends('layouts.app')

@section('title', __('messages.sales_orders'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.sales_orders') }}</h1>
            @can('create sales orders')
                <a href="{{ route('sales.sales-orders.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('messages.create') }}
                </a>
            @endcan
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('messages.order_number') }}</th>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.customer') }}</th>
                                <th>{{ __('messages.total') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($salesOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('sales.sales-orders.show', $order) }}">
                                            {{ $order->order_number }}
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ $order->document_number }}</small>
                                    </td>
                                    <td>
                                        {{ $order->order_date->format('Y-m-d') }}
                                        @if($order->expected_delivery_date)
                                            <br>
                                            <small class="text-muted">{{ __('messages.expected_delivery') }}:
                                                {{ $order->expected_delivery_date->format('Y-m-d') }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $order->customer->name ?? '-' }}</td>
                                    <td>{{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                        @php
                                            $statusClass = [
                                                'draft' => 'secondary',
                                                'confirmed' => 'info',
                                                'processing' => 'primary',
                                                'shipped' => 'warning',
                                                'delivered' => 'success',
                                                'cancelled' => 'danger',
                                                'invoiced' => 'success',
                                                'partial' => 'info',
                                            ][$order->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">
                                            {{ __('messages.' . $order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('sales.sales-orders.show', $order) }}" class="btn btn-sm btn-info"
                                                title="{{ __('messages.view') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($order->isDraft())
                                                @can('edit sales orders')
                                                    <a href="{{ route('sales.sales-orders.edit', $order) }}"
                                                        class="btn btn-sm btn-primary" title="{{ __('messages.edit') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                            @endif
                                            <a href="{{ route('sales.sales-orders.print', $order) }}"
                                                class="btn btn-sm btn-secondary" title="{{ __('messages.print') }}"
                                                target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">{{ __('messages.no_records_found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $salesOrders->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
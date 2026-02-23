@extends('layouts.app')

@section('title', __('messages.stock_issue_orders'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.stock_issue_orders') }}</h1>
            <a href="{{ route('inventory.issue-orders.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i> {{ __('messages.create_issue_order') }}
            </a>
        </div>

        <div class="card glassy">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('messages.document_number') }}</th>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.warehouse') }}</th>
                                <th>{{ __('messages.type') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($issueOrders as $order)
                                <tr>
                                    <td>{{ $order->document_number }}</td>
                                    <td>{{ $order->issue_date->format('Y-m-d') }}</td>
                                    <td>{{ $order->warehouse->name }}</td>
                                    <td>
                                        <span class="text-capitalize">{{ __('messages.' . $order->issue_type) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $order->status === 'posted' ? 'paid' : 'draft' }}">
                                            {{ __('messages.' . $order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('inventory.issue-orders.show', $order) }}"
                                            class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fas fa-file-export fa-3x text-muted mb-3"></i>
                                        <h4 class="text-muted">{{ __('messages.no_records_found') }}</h4>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $issueOrders->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
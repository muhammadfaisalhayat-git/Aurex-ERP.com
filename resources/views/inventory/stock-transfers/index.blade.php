@extends('layouts.app')

@section('title', __('messages.stock_transfers'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.stock_transfers') }}</h1>
            <a href="{{ route('inventory.stock-transfers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i> {{ __('messages.create_stock_transfer') }}
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
                                <th>{{ __('messages.from_warehouse') }}</th>
                                <th>{{ __('messages.to_warehouse') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transfers as $transfer)
                                <tr>
                                    <td>{{ $transfer->document_number }}</td>
                                    <td>{{ $transfer->transfer_date->format('Y-m-d') }}</td>
                                    <td>{{ $transfer->fromWarehouse->name ?? __('messages.unknown') }}</td>
                                    <td>{{ $transfer->toWarehouse->name ?? __('messages.unknown') }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match ($transfer->status) {
                                                'received' => 'paid',
                                                'approved' => 'posted',
                                                'pending' => 'draft',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge badge-{{ $badgeClass }}">
                                            {{ __('messages.' . $transfer->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('inventory.stock-transfers.show', $transfer) }}"
                                            class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                                        <h4 class="text-muted">{{ __('messages.no_records_found') }}</h4>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $transfers->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
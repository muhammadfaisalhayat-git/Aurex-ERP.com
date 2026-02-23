@extends('layouts.app')

@section('title', __('messages.stock_supply'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.stock_supply') }}</h1>
            <a href="{{ route('inventory.stock-supply.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i> {{ __('messages.create_stock_supply') }}
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
                                <th>{{ __('messages.vendor') }}</th>
                                <th>{{ __('messages.total_amount') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($supplies as $supply)
                                <tr>
                                    <td>{{ $supply->document_number }}</td>
                                    <td>{{ $supply->supply_date->format('Y-m-d') }}</td>
                                    <td>{{ $supply->warehouse->name ?? '-' }}</td>
                                    <td>{{ $supply->vendor->name ?? '-' }}</td>
                                    <td>{{ number_format($supply->total_amount, 2) }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $supply->status === 'posted' ? 'success' : ($supply->status === 'draft' ? 'secondary' : 'info') }}">
                                            {{ ucfirst($supply->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('inventory.stock-supply.show', $supply) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        {{ __('messages.no_records_found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $supplies->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
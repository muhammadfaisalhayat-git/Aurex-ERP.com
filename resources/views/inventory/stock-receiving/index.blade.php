@extends('layouts.app')

@section('title', __('messages.stock_receiving'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.stock_receiving') }}</h1>
            <a href="{{ route('inventory.stock-receiving.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i> {{ __('messages.create_stock_receiving') }}
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
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($receivings as $receiving)
                                <tr>
                                    <td>{{ $receiving->document_number }}</td>
                                    <td>{{ $receiving->receiving_date->format('Y-m-d') }}</td>
                                    <td>{{ $receiving->warehouse->name }}</td>
                                    <td>{{ $receiving->vendor->name }}</td>
                                    <td>
                                        <span class="badge badge-{{ $receiving->status === 'received' ? 'paid' : 'draft' }}">
                                            {{ __('messages.' . $receiving->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('inventory.stock-receiving.show', $receiving) }}"
                                            class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fas fa-download fa-3x text-muted mb-3"></i>
                                        <h4 class="text-muted">{{ __('messages.no_records_found') }}</h4>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $receivings->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
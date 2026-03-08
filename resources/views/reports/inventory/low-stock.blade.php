@extends('layouts.app')

@section('title', __('messages.low_stock_alert'))

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.low_stock_alert') }}</h1>
        <button onclick="window.print()" class="btn btn-secondary">
            <i class="fas fa-print me-1"></i> {{ __('messages.print') }}
        </button>
    </div>

    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <div>
            {{ __('messages.low_stock_alert_description') ?? 'The following products are at or below their reorder level.' }}
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('messages.product_code') }}</th>
                            <th>{{ __('messages.product_name') }}</th>
                            <th class="text-end">{{ __('messages.current_stock') }}</th>
                            <th class="text-end">{{ __('messages.reorder_level') }}</th>
                            <th class="text-end">{{ __('messages.shortage') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            @php 
                                $currentStock = $product->stockBalances->sum('available_quantity');
                                $shortage = $product->reorder_level - $currentStock;
                            @endphp
                            <tr>
                                <td><code>{{ $product->code }}</code></td>
                                <td>{{ $product->name }} ({{ __('messages.stock') }}: {{ $product->available_stock }})</td>
                                <td class="text-end text-danger fw-bold">{{ number_format($currentStock, 3) }}</td>
                                <td class="text-end">{{ number_format($product->reorder_level, 3) }}</td>
                                <td class="text-end text-warning fw-bold">{{ number_format($shortage, 3) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    {{ __('messages.no_data_found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', __('messages.inventory_valuation'))

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.inventory_valuation') }}</h1>
        <button onclick="window.print()" class="btn btn-secondary">
            <i class="fas fa-print me-1"></i> {{ __('messages.print') }}
        </button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('messages.product_code') }}</th>
                            <th>{{ __('messages.product_name') }}</th>
                            <th>{{ __('messages.category') }}</th>
                            <th class="text-end">{{ __('messages.quantity') }}</th>
                            <th class="text-end">{{ __('messages.average_cost') }}</th>
                            <th class="text-end">{{ __('messages.total_value') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $grandTotal = 0; @endphp
                        @foreach($products as $product)
                            @php 
                                $totalQty = $product->stockBalances->sum('quantity');
                                $avgCost = $product->stockBalances->avg('average_cost') ?? 0;
                                $totalValue = $totalQty * $avgCost;
                                $grandTotal += $totalValue;
                            @endphp
                            @if($totalQty > 0)
                            <tr>
                                <td><code>{{ $product->code }}</code></td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category->name ?? '-' }}</td>
                                <td class="text-end">{{ number_format($totalQty, 3) }}</td>
                                <td class="text-end">{{ number_format($avgCost, 2) }}</td>
                                <td class="text-end fw-bold">{{ number_format($totalValue, 2) }}</td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <td colspan="5" class="text-end fw-bold">{{ __('messages.total') }}</td>
                            <td class="text-end fw-bold">{{ number_format($grandTotal, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

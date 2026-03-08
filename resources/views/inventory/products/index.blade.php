@extends('layouts.app')

@section('title', __('messages.products'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.products') }}</h1>
            @can('create inventory')
                <a href="{{ route('inventory.products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-2"></i> {{ __('messages.create') }}
                </a>
            @endcan
        </div>


        <div class="card glassy">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('messages.code') }}</th>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.category') }}</th>
                                <th>{{ __('messages.available_quantity') }}</th>
                                <th>{{ __('messages.price') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td><code>{{ $product->code }}</code></td>
                                    <td>{{ $product->name }} ({{ __('messages.stock') }}: {{ $product->available_stock }})</td>
                                    <td>{{ $product->category?->name ?? '-' }}</td>
                                    <td>
                                        <span
                                            class="badge {{ ($product->stock_balances_sum_available_quantity ?? 0) > 0 ? 'bg-success' : 'bg-danger' }}">
                                            {{ number_format($product->stock_balances_sum_available_quantity ?? 0, 2) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($product->sale_price, 2) }} {{ __('messages.sar') }}</td>
                                    <td>
                                        <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $product->is_active ? __('messages.active') : __('messages.inactive') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            @can('view inventory')
                                                <a href="{{ route('inventory.products.show', $product) }}"
                                                    class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan
                                            @can('edit inventory')
                                                <a href="{{ route('inventory.products.edit', $product) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                            @can('view inventory')
                                                <a href="{{ route('inventory.products.bom', $product) }}"
                                                    class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-microchip"></i>
                                                </a>
                                            @endcan
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
                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            </div>
        </div>

    </div>
@endsection
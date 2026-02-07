@extends('layouts.app')

@section('title', __('messages.products'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.products') }}</h1>
            @can('create products')
                <a href="{{ route('inventory.products.create') }}" class="btn btn-primary">
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
                                <th>{{ __('messages.code') }}</th>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.category') }}</th>
                                <th>{{ __('messages.price') }}</th>
                                <th>{{ __('messages.type') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td>
                                        <a href="{{ route('inventory.products.show', $product) }}">
                                            {{ $product->code }}
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ $product->barcode }}</small>
                                    </td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category->name ?? '-' }}</td>
                                    <td>
                                        {{ number_format($product->sale_price, 2) }}
                                        <br>
                                        <small class="text-muted">{{ __('messages.cost_price') }}:
                                            {{ number_format($product->cost_price, 2) }}</small>
                                    </td>
                                    <td>{{ __('messages.' . $product->type) }}</td>
                                    <td>
                                        @php
                                            $statusClass = $product->is_active ? 'success' : 'danger';
                                            $statusText = $product->is_active ? 'active' : 'inactive';
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">
                                            {{ __('messages.' . $statusText) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('inventory.products.show', $product) }}"
                                                class="btn btn-sm btn-info" title="{{ __('messages.view') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @can('edit products')
                                                <a href="{{ route('inventory.products.edit', $product) }}"
                                                    class="btn btn-sm btn-primary" title="{{ __('messages.edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">{{ __('messages.no_records_found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
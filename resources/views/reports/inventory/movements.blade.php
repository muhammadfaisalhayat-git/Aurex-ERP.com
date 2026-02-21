@extends('layouts.app')

@section('title', __('messages.inventory_movements'))

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
            <h1 class="h3 mb-0 text-gray-800">{{ __('messages.inventory_movements') }}</h1>
            <button onclick="window.print()" class="btn btn-secondary">
                <i class="fas fa-print me-1"></i> {{ __('messages.print') }}
            </button>
        </div>

        <div class="card shadow mb-4 d-print-none">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.product') }}</label>
                        <select name="product_id" class="form-select select2">
                            <option value="">{{ __('messages.all') }}</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ $request->product_id == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} ({{ $product->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.warehouse') }}</label>
                        <select name="warehouse_id" class="form-select select2">
                            <option value="">{{ __('messages.all') }}</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ $request->warehouse_id == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('messages.date_from') }}</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $request->start_date }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('messages.date_to') }}</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $request->end_date }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-1"></i> {{ __('messages.filter') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.product') }}</th>
                                <th>{{ __('messages.warehouse') }}</th>
                                <th>{{ __('messages.reference') }}</th>
                                <th>{{ __('messages.type') }}</th>
                                <th class="text-end">{{ __('messages.quantity') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($movements as $movement)
                                <tr>
                                    <td>{{ $movement->transaction_date->format('Y-m-d H:i') }}</td>
                                    <td>{{ $movement->product->name }}</td>
                                    <td>{{ $movement->warehouse->name }}</td>
                                    <td><code>{{ $movement->reference_number }}</code></td>
                                    <td>
                                        <span class="badge bg-{{ $movement->movement_type == 'in' ? 'success' : 'danger' }}">
                                            {{ $movement->movement_type == 'in' ? __('messages.stock_in') : __('messages.stock_out') }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold">
                                        {{ $movement->movement_type == 'in' ? '+' : '-' }}{{ number_format($movement->quantity, 3) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        {{ __('messages.no_records_found') }}
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
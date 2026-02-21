@extends('layouts.app')

@section('title', 'Local Purchases Report')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Local Purchases Report</h1>
            <a href="{{ route('reports.suppliers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Filters</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('reports.suppliers.local-purchases') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="supplier_name" class="form-label">Supplier Name</label>
                                <input type="text" class="form-control" id="supplier_name" name="supplier_name"
                                    value="{{ $validated['supplier_name'] ?? '' }}"
                                    placeholder="{{ __('reports.click_here') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="date_from" class="form-label">Date From</label>
                                <input type="date" class="form-control" id="date_from" name="date_from"
                                    value="{{ $validated['date_from'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="date_to" class="form-label">Date To</label>
                                <input type="date" class="form-control" id="date_to" name="date_to"
                                    value="{{ $validated['date_to'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="all" {{ ($validated['status'] ?? 'all') === 'all' ? 'selected' : '' }}>
                                        {{ __('reports.click_here') }}
                                    </option>
                                    <option value="draft" {{ ($validated['status'] ?? '') === 'draft' ? 'selected' : '' }}>
                                        Draft</option>
                                    <option value="posted" {{ ($validated['status'] ?? '') === 'posted' ? 'selected' : '' }}>
                                        Posted</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="mb-3 w-100">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="{{ route('reports.suppliers.local-purchases') }}" class="btn btn-secondary">
                                    <i class="fas fa-undo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($summary)
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6>Total Count</h6>
                            <h3>{{ $summary->total_count ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6>Subtotal</h6>
                            <h3>{{ number_format($summary->total_net ?? 0, 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6>Total Tax</h6>
                            <h3>{{ number_format($summary->total_tax ?? 0, 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h6>Total Amount</h6>
                            <h3>{{ number_format($summary->total_gross ?? 0, 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Results</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Document Number</th>
                                <th>Invoice Number</th>
                                <th>Invoice Date</th>
                                <th>Supplier Name</th>
                                <th>Warehouse</th>
                                <th class="text-end">Subtotal</th>
                                <th class="text-end">Tax Amount</th>
                                <th class="text-end">Total Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchases as $purchase)
                                <tr>
                                    <td>{{ $purchase->document_number }}</td>
                                    <td>{{ $purchase->invoice_number }}</td>
                                    <td>{{ $purchase->invoice_date->format('Y-m-d') }}</td>
                                    <td>{{ $purchase->vendor->name ?? 'N/A' }}</td>
                                    <td>{{ $purchase->warehouse->name ?? 'N/A' }}</td>
                                    <td class="text-end">{{ number_format($purchase->subtotal ?? 0, 2) }}</td>
                                    <td class="text-end">{{ number_format($purchase->tax_amount ?? 0, 2) }}</td>
                                    <td class="text-end">{{ number_format($purchase->total_amount ?? 0, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $purchase->status === 'posted' ? 'success' : 'warning' }}">
                                            {{ ucfirst($purchase->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No records found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $purchases->links() }}
            </div>
        </div>
    </div>
@endsection
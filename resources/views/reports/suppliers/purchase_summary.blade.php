@extends('layouts.app')

@section('title', 'Purchase Summary Report')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Purchase Summary Report</h1>
            <a href="{{ route('reports.suppliers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Filters</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('reports.suppliers.purchase-summary') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="vendor_id" class="form-label">Vendor</label>
                                <select class="form-select" id="vendor_id" name="vendor_id">
                                    <option value="">{{ __('reports.click_here') }}</option>
                                    @foreach(\App\Models\Vendor::where('is_active', true)->orderBy('name')->get() as $vendor)
                                        <option value="{{ $vendor->id }}" {{ ($validated['vendor_id'] ?? '') == $vendor->id ? 'selected' : '' }}>
                                            {{ $vendor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="date_from" class="form-label">Date From</label>
                                <input type="date" class="form-control" id="date_from" name="date_from"
                                    value="{{ $validated['date_from'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="date_to" class="form-label">Date To</label>
                                <input type="date" class="form-control" id="date_to" name="date_to"
                                    value="{{ $validated['date_to'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="mb-3 w-100">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="{{ route('reports.suppliers.purchase-summary') }}" class="btn btn-secondary">
                                    <i class="fas fa-undo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($totals)
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6>Total Invoices</h6>
                            <h3>{{ $totals->total_invoices ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6>Total Net</h6>
                            <h3>{{ number_format($totals->total_net ?? 0, 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6>Total Tax</h6>
                            <h3>{{ number_format($totals->total_tax ?? 0, 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h6>Total Gross</h6>
                            <h3>{{ number_format($totals->total_gross ?? 0, 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Vendor Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Vendor</th>
                                        <th class="text-end">Invoices</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vendorSummary as $summary)
                                        <tr>
                                            <td>{{ $summary->vendor->name ?? 'N/A' }}</td>
                                            <td class="text-end">{{ $summary->invoice_count }}</td>
                                            <td class="text-end">{{ number_format($summary->total_gross, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Invoice Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Invoice Number</th>
                                        <th>Vendor</th>
                                        <th>Date</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invoices as $invoice)
                                        <tr>
                                            <td>{{ $invoice->document_number }}</td>
                                            <td>{{ $invoice->vendor->name ?? 'N/A' }}</td>
                                            <td>{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                                            <td class="text-end">{{ number_format($invoice->gross_amount, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No records found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $invoices->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
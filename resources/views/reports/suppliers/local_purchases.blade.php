@extends('layouts.app')

@section('title', __('reports.local_purchases_report'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ __('reports.local_purchases_report') }}</h1>
        <a href="{{ route('reports.suppliers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('general.back') }}
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ __('reports.filters') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.suppliers.local-purchases') }}" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="supplier_name" class="form-label">{{ __('reports.supplier_name') }}</label>
                            <input type="text" class="form-control" id="supplier_name" name="supplier_name" 
                                   value="{{ $validated['supplier_name'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="date_from" class="form-label">{{ __('reports.date_from') }}</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="{{ $validated['date_from'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="date_to" class="form-label">{{ __('reports.date_to') }}</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="{{ $validated['date_to'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="status" class="form-label">{{ __('reports.status') }}</label>
                            <select class="form-select" id="status" name="status">
                                <option value="all" {{ ($validated['status'] ?? 'all') === 'all' ? 'selected' : '' }}>{{ __('reports.all') }}</option>
                                <option value="draft" {{ ($validated['status'] ?? '') === 'draft' ? 'selected' : '' }}>{{ __('reports.draft') }}</option>
                                <option value="posted" {{ ($validated['status'] ?? '') === 'posted' ? 'selected' : '' }}>{{ __('reports.posted') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="mb-3 w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> {{ __('reports.filter') }}
                            </button>
                            <a href="{{ route('reports.suppliers.local-purchases') }}" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> {{ __('reports.reset') }}
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
                    <h6>{{ __('reports.total_count') }}</h6>
                    <h3>{{ $summary->total_count ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>{{ __('reports.total_net') }}</h6>
                    <h3>{{ number_format($summary->total_net ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6>{{ __('reports.total_tax') }}</h6>
                    <h3>{{ number_format($summary->total_tax ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6>{{ __('reports.total_gross') }}</h6>
                    <h3>{{ number_format($summary->total_gross ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ __('reports.results') }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('reports.document_number') }}</th>
                            <th>{{ __('reports.invoice_number') }}</th>
                            <th>{{ __('reports.invoice_date') }}</th>
                            <th>{{ __('reports.supplier_name') }}</th>
                            <th>{{ __('reports.warehouse') }}</th>
                            <th class="text-end">{{ __('reports.net_amount') }}</th>
                            <th class="text-end">{{ __('reports.tax_amount') }}</th>
                            <th class="text-end">{{ __('reports.gross_amount') }}</th>
                            <th>{{ __('reports.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $purchase)
                        <tr>
                            <td>{{ $purchase->document_number }}</td>
                            <td>{{ $purchase->invoice_number }}</td>
                            <td>{{ $purchase->invoice_date->format('Y-m-d') }}</td>
                            <td>{{ $purchase->supplier_name }}</td>
                            <td>{{ $purchase->warehouse->name }}</td>
                            <td class="text-end">{{ number_format($purchase->net_amount, 2) }}</td>
                            <td class="text-end">{{ number_format($purchase->tax_amount, 2) }}</td>
                            <td class="text-end">{{ number_format($purchase->gross_amount, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $purchase->status === 'posted' ? 'success' : 'warning' }}">
                                    {{ __('reports.status_' . $purchase->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">{{ __('reports.no_records') }}</td>
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

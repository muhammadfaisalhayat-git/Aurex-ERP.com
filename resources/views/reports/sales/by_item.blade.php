@extends('layouts.app')

@section('title', __('reports.sales_by_item'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ __('reports.sales_by_item') }}</h1>
        <a href="{{ route('reports.sales.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('general.back') }}
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ __('reports.filters') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.sales.by-item') }}" method="GET">
                <div class="row">
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="item_code" class="form-label">{{ __('reports.item_code') }}</label>
                            <input type="text" class="form-control" id="item_code" name="item_code" 
                                   value="{{ $validated['item_code'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="item_name" class="form-label">{{ __('reports.item_name') }}</label>
                            <input type="text" class="form-control" id="item_name" name="item_name" 
                                   value="{{ $validated['item_name'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="category_id" class="form-label">{{ __('reports.category') }}</label>
                            <select class="form-select" id="category_id" name="category_id">
                                <option value="">{{ __('reports.all_categories') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ ($validated['category_id'] ?? '') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
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
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="mb-3 w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> {{ __('reports.filter') }}
                            </button>
                            <a href="{{ route('reports.sales.by-item') }}" class="btn btn-secondary">
                                <i class="fas fa-undo"></i>
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
                    <h6>{{ __('reports.total_quantity') }}</h6>
                    <h3>{{ number_format($totals->total_quantity ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>{{ __('reports.total_net') }}</h6>
                    <h3>{{ number_format($totals->total_net ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6>{{ __('reports.total_tax') }}</h6>
                    <h3>{{ number_format($totals->total_tax ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6>{{ __('reports.total_gross') }}</h6>
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
                    <h5 class="mb-0">{{ __('reports.item_summary') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>{{ __('reports.item') }}</th>
                                    <th class="text-end">{{ __('reports.quantity') }}</th>
                                    <th class="text-end">{{ __('reports.total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($itemSummary as $summary)
                                <tr>
                                    <td>{{ $summary->item->name ?? 'N/A' }}</td>
                                    <td class="text-end">{{ number_format($summary->total_quantity, 2) }}</td>
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('reports.item_details') }}</h5>
                    <a href="{{ route('reports.sales.export-by-item', array_merge($validated, ['format' => 'csv'])) }}" class="btn btn-sm btn-success">
                        <i class="fas fa-file-csv"></i> {{ __('reports.export') }}
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>{{ __('reports.invoice') }}</th>
                                    <th>{{ __('reports.item') }}</th>
                                    <th class="text-end">{{ __('reports.qty') }}</th>
                                    <th class="text-end">{{ __('reports.amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                <tr>
                                    <td>{{ $item->invoice->document_number ?? 'N/A' }}</td>
                                    <td>{{ $item->item->name ?? 'N/A' }}</td>
                                    <td class="text-end">{{ number_format($item->quantity, 2) }}</td>
                                    <td class="text-end">{{ number_format($item->gross_amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">{{ __('reports.no_records') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

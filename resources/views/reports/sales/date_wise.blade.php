@extends('layouts.app')

@section('title', __('reports.sales_date_wise'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ __('reports.sales_date_wise') }}</h1>
        <a href="{{ route('reports.sales.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('general.back') }}
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ __('reports.filters') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.sales.date-wise') }}" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="date_from" class="form-label">{{ __('reports.date_from') }} *</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="{{ $validated['date_from'] ?? date('Y-m-01') }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="date_to" class="form-label">{{ __('reports.date_to') }} *</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="{{ $validated['date_to'] ?? date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="branch_id" class="form-label">{{ __('reports.branch') }}</label>
                            <select class="form-select" id="branch_id" name="branch_id">
                                <option value="">{{ __('reports.all_branches') }}</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ ($validated['branch_id'] ?? '') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="mb-3 w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> {{ __('reports.filter') }}
                            </button>
                            <a href="{{ route('reports.sales.date-wise') }}" class="btn btn-secondary">
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
                    <h6>{{ __('reports.total_invoices') }}</h6>
                    <h3>{{ $totals->total_invoices ?? 0 }}</h3>
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
                    <h5 class="mb-0">{{ __('reports.daily_summary') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>{{ __('reports.date') }}</th>
                                    <th class="text-end">{{ __('reports.invoices') }}</th>
                                    <th class="text-end">{{ __('reports.net') }}</th>
                                    <th class="text-end">{{ __('reports.gross') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dailySummary as $day)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($day->date)->format('Y-m-d') }}</td>
                                    <td class="text-end">{{ $day->invoice_count }}</td>
                                    <td class="text-end">{{ number_format($day->total_net, 2) }}</td>
                                    <td class="text-end">{{ number_format($day->total_gross, 2) }}</td>
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
                    <h5 class="mb-0">{{ __('reports.monthly_summary') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>{{ __('reports.month') }}</th>
                                    <th class="text-end">{{ __('reports.invoices') }}</th>
                                    <th class="text-end">{{ __('reports.net') }}</th>
                                    <th class="text-end">{{ __('reports.gross') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlySummary as $month)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($month->month)->format('Y-m') }}</td>
                                    <td class="text-end">{{ $month->invoice_count }}</td>
                                    <td class="text-end">{{ number_format($month->total_net, 2) }}</td>
                                    <td class="text-end">{{ number_format($month->total_gross, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

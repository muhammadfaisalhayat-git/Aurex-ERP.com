@extends('layouts.app')

@section('title', __('reports.tax_by_invoice'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('reports.sales.index') }}">{{ __('reports.tax_reports') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('reports.tax_by_invoice') }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">{{ __('reports.tax_by_invoice') }}</h1>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-primary shadow-sm" onclick="window.print()">
                <i class="fas fa-print me-2"></i> {{ __('reports.export_pdf') }}
            </button>
            <button type="button" class="btn btn-primary shadow-sm ms-2">
                <i class="fas fa-file-excel me-2"></i> {{ __('reports.export_excel') }}
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <form action="{{ route('reports.tax.by-invoice') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted text-uppercase fw-bold">{{ __('reports.date_from') }}</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from', date('Y-m-d', strtotime('-30 days'))) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted text-uppercase fw-bold">{{ __('reports.date_to') }}</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to', date('Y-m-d')) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted text-uppercase fw-bold">{{ __('reports.status') }}</label>
                    <select name="status" class="form-select">
                        <option value="">{{ __('reports.all') }}</option>
                        <option value="posted">{{ __('reports.posted') }}</option>
                        <option value="draft">{{ __('reports.draft') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted text-uppercase fw-bold">{{ __('reports.branch') }}</label>
                    <select name="branch_id" class="form-select select2">
                        <option value="">{{ __('reports.all_branches') }}</option>
                        @foreach($branches ?? [] as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex">
                    <button type="submit" class="btn btn-primary w-100 me-2">
                        <i class="fas fa-filter me-2"></i> {{ __('reports.filter') }}
                    </button>
                    <a href="{{ route('reports.tax.by-invoice') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-dark fw-bold">{{ __('reports.invoice_details') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase">
                        <tr>
                            <th class="ps-4 small fw-bold">{{ __('reports.date') }}</th>
                            <th class="small fw-bold">{{ __('reports.invoice_number') }}</th>
                            <th class="small fw-bold">{{ __('reports.customer') }}</th>
                            <th class="text-end small fw-bold">{{ __('reports.taxable_amount') }}</th>
                            <th class="text-end small fw-bold">{{ __('reports.tax_amount') }}</th>
                            <th class="text-end small fw-bold">{{ __('reports.total') }}</th>
                            <th class="text-center small fw-bold pe-4 text-nowrap">{{ __('reports.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center">
                            <td colspan="7" class="text-muted py-5">
                                <i class="fas fa-search fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">{{ __('reports.no_records') }}</p>
                                <small class="d-block mt-1">Adjust your filters to see transactions</small>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .breadcrumb-item + .breadcrumb-item::before { content: "›"; font-size: 1.5rem; line-height: 1; vertical-align: middle; }
</style>
@endsection

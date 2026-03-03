@extends('layouts.app')

@section('title', __('reports.tax_summary'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('reports.tax_summary') }}</li>
                    </ol>
                </nav>
                <h1 class="h3 mb-0">{{ __('reports.tax_summary') }}</h1>
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
                <form action="{{ route('reports.tax.summary') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small text-muted text-uppercase fw-bold">{{ __('messages.year') }}</label>
                        <select name="year" class="form-select">
                            @for($i = date('Y'); $i >= 2020; $i--)
                                <option value="{{ $i }}" {{ ($year ?? date('Y')) == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted text-uppercase fw-bold">{{ __('messages.month') }}</label>
                        <select name="month" class="form-select">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ sprintf('%02d', $m) }}" {{ ($month ?? date('m')) == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endfor
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
                        <a href="{{ route('reports.tax.summary') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm overflow-hidden h-100">
                    <div class="card-body position-relative">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="icon-shape bg-soft-primary text-primary rounded-circle">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                            <span class="badge rounded-pill bg-soft-primary text-primary small">Sales</span>
                        </div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-1">{{ __('reports.output_tax') }}</h6>
                        <h2 class="mb-0 fw-bold">SAR {{ number_format($salesTax ?? 0, 2) }}</h2>
                    </div>
                    <div class="bg-primary py-1 opacity-25"></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm overflow-hidden h-100">
                    <div class="card-body position-relative">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="icon-shape bg-soft-danger text-danger rounded-circle">
                                <i class="fas fa-arrow-down"></i>
                            </div>
                            <span class="badge rounded-pill bg-soft-danger text-danger small">Purchases</span>
                        </div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-1">{{ __('reports.input_tax') }}</h6>
                        <h2 class="mb-0 fw-bold">SAR {{ number_format($purchaseTax ?? 0, 2) }}</h2>
                    </div>
                    <div class="bg-danger py-1 opacity-25"></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm overflow-hidden h-100">
                    <div class="card-body position-relative">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="icon-shape bg-soft-success text-success rounded-circle">
                                <i class="fas fa-balance-scale"></i>
                            </div>
                            <span class="badge rounded-pill bg-soft-success text-success small">Balance</span>
                        </div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-1">{{ __('reports.net_tax_payable') }}</h6>
                        <h2 class="mb-0 fw-bold">SAR {{ number_format($netTax ?? 0, 2) }}</h2>
                        <p class="text-muted small mb-0 mt-2">
                            <i class="fas fa-info-circle me-1"></i> Net VAT liability for the period
                        </p>
                    </div>
                    <div class="bg-success py-1 opacity-25"></div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-dark fw-bold">{{ __('reports.daily_summary') }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">{{ __('reports.date') }}</th>
                                <th class="text-end">{{ __('reports.taxable_amount') }} (Sales)</th>
                                <th class="text-end">{{ __('reports.output_tax') }}</th>
                                <th class="text-end">{{ __('reports.taxable_amount') }} (Purchases)</th>
                                <th class="text-end">{{ __('reports.input_tax') }}</th>
                                <th class="text-end pe-4">{{ __('reports.net_tax_payable') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-center py-5">
                                <td colspan="6" class="text-muted py-5">
                                    <i class="fas fa-file-invoice fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0">{{ __('reports.no_records') }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        .icon-shape {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .bg-soft-primary {
            background-color: rgba(13, 110, 253, 0.1);
        }

        .bg-soft-danger {
            background-color: rgba(220, 53, 69, 0.1);
        }

        .bg-soft-success {
            background-color: rgba(25, 135, 84, 0.1);
        }

        .breadcrumb-item+.breadcrumb-item::before {
            content: "›";
            font-size: 1.5rem;
            line-height: 1;
            vertical-align: middle;
        }
    </style>
@endsection
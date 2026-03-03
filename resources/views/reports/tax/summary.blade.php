@extends('layouts.app')

@section('title', __('messages.reports') . ' - ' . __('messages.tax_summary'))

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('messages.tax_summary_report') }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('messages.reports') }}</li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('messages.tax_summary') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('reports.tax.summary') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">{{ __('messages.year') }}</label>
                    <select name="year" class="form-select">
                        @for($i = date('Y'); $i >= 2020; $i--)
                            <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('messages.month') }}</label>
                    <select name="month" class="form-select">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ sprintf('%02d', $m) }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>{{ __('messages.filter') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 border-start border-primary border-4 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase mb-2 fw-bold">{{ __('messages.output_vat') }} (Sales)</div>
                    <h2 class="text-primary mb-0 font-monospace">{{ number_format($salesTax, 2) }}</h2>
                    <small class="text-muted">{{ __('messages.currency_sar') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-start border-danger border-4 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase mb-2 fw-bold">{{ __('messages.input_vat') }} (Purchases)
                    </div>
                    <h2 class="text-danger mb-0 font-monospace">{{ number_format($purchaseTax, 2) }}</h2>
                    <small class="text-muted">{{ __('messages.currency_sar') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-start border-{{ $netTax >= 0 ? 'success' : 'warning' }} border-4 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase mb-2 fw-bold">{{ __('messages.net_vat_payable') }}</div>
                    <h2 class="text-{{ $netTax >= 0 ? 'success' : 'warning' }} mb-0 font-monospace">
                        {{ number_format($netTax, 2) }}</h2>
                    <small class="text-muted">{{ __('messages.currency_sar') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5 text-center">
        <p class="text-muted small">
            <i class="fas fa-info-circle me-1"></i>
            {{ __('messages.tax_report_disclaimer') }}
        </p>
    </div>
@endsection
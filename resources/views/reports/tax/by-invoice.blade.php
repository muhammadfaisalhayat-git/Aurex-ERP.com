@extends('layouts.app')

@section('title', __('reports.tax_by_invoice'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a>
                        </li>
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
                        <a href="{{ route('reports.tax.by-invoice') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-4">
            <!-- Sales VAT Output -->
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3 border-bottom border-light">
                        <h5 class="mb-0 text-primary fw-bold">{{ __('messages.sales_vat_output') }}</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-3">{{ __('messages.invoice') }}</th>
                                        <th>{{ __('messages.customer') }}</th>
                                        <th class="text-end pe-3">{{ __('messages.tax_amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($salesInvoices ?? [] as $invoice)
                                        <tr>
                                            <td class="ps-3">{{ $invoice->invoice_number }}</td>
                                            <td>{{ $invoice->customer->name ?? '-' }}</td>
                                            <td class="text-end pe-3 fw-bold text-primary">
                                                {{ number_format($invoice->tax_amount, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-5 text-muted">
                                                <i class="fas fa-file-invoice fa-2x mb-2 opacity-25"></i>
                                                <p class="mb-0">{{ __('messages.no_data_found') }}</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if(($salesInvoices ?? []) && count($salesInvoices) > 0)
                                    <tfoot class="bg-light fw-bold border-top-2">
                                        <tr>
                                            <td colspan="2" class="ps-3">{{ __('messages.total') }}</td>
                                            <td class="text-end pe-3 text-primary">
                                                {{ number_format($salesInvoices->sum('tax_amount'), 2) }} SAR</td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Purchase VAT Input -->
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3 border-bottom border-light">
                        <h5 class="mb-0 text-danger fw-bold">{{ __('messages.purchase_vat_input') }}</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-3">{{ __('messages.invoice') }}</th>
                                        <th>{{ __('messages.vendor') }}</th>
                                        <th class="text-end pe-3">{{ __('messages.tax_amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchaseInvoices ?? [] as $invoice)
                                        <tr>
                                            <td class="ps-3">{{ $invoice->invoice_number }}</td>
                                            <td>{{ $invoice->vendor->name ?? '-' }}</td>
                                            <td class="text-end pe-3 fw-bold text-danger">
                                                {{ number_format($invoice->tax_amount, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-5 text-muted">
                                                <i class="fas fa-file-invoice fa-2x mb-2 opacity-25"></i>
                                                <p class="mb-0">{{ __('messages.no_data_found') }}</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if(($purchaseInvoices ?? []) && count($purchaseInvoices) > 0)
                                    <tfoot class="bg-light fw-bold border-top-2">
                                        <tr>
                                            <td colspan="2" class="ps-3">{{ __('messages.total') }}</td>
                                            <td class="text-end pe-3 text-danger">
                                                {{ number_format($purchaseInvoices->sum('tax_amount'), 2) }} SAR</td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .breadcrumb-item+.breadcrumb-item::before {
            content: "›";
            font-size: 1.5rem;
            line-height: 1;
            vertical-align: middle;
        }
    </style>
@endsection
@extends('layouts.app')

@section('title', __('messages.reports') . ' - ' . __('messages.tax_by_invoice'))

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('messages.tax_by_invoice_report') }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('messages.reports') }}</li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('messages.tax_by_invoice') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('reports.tax.by-invoice') }}" method="GET" class="row g-3 align-items-end">
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

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">{{ __('messages.sales_vat_output') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.invoice') }}</th>
                                    <th>{{ __('messages.customer') }}</th>
                                    <th class="text-end">{{ __('messages.tax_amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salesInvoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->invoice_number }}</td>
                                        <td>{{ $invoice->customer->name ?? '-' }}</td>
                                        <td class="text-end fw-bold text-primary">{{ number_format($invoice->tax_amount, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">{{ __('messages.no_data_found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($salesInvoices->count() > 0)
                                <tfoot class="bg-light fw-bold">
                                    <tr>
                                        <td colspan="2">{{ __('messages.total') }}</td>
                                        <td class="text-end text-primary">
                                            {{ number_format($salesInvoices->sum('tax_amount'), 2) }}</td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">{{ __('messages.purchase_vat_input') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.invoice') }}</th>
                                    <th>{{ __('messages.vendor') }}</th>
                                    <th class="text-end">{{ __('messages.tax_amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseInvoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->invoice_number }}</td>
                                        <td>{{ $invoice->vendor->name ?? '-' }}</td>
                                        <td class="text-end fw-bold text-danger">{{ number_format($invoice->tax_amount, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">{{ __('messages.no_data_found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($purchaseInvoices->count() > 0)
                                <tfoot class="bg-light fw-bold">
                                    <tr>
                                        <td colspan="2">{{ __('messages.total') }}</td>
                                        <td class="text-end text-danger">
                                            {{ number_format($purchaseInvoices->sum('tax_amount'), 2) }}</td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
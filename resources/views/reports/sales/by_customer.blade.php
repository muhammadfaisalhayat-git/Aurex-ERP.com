@extends('layouts.app')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <style>
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px !important;
            font-size: 0.875rem !important;
            text-align: left !important;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
            text-align: left !important;
        }

        .select2-results__option {
            text-align: left !important;
        }
    </style>
@endpush

@section('title', __('reports.sales_by_customer'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('reports.sales_by_customer') }}</h1>
            <a href="{{ route('reports.sales.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('general.back') }}
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ __('reports.filters') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('reports.sales.by-customer') }}" method="GET">
                    <div class="row">
                        <input type="hidden" id="customer_id" name="customer_id"
                            value="{{ $validated['customer_id'] ?? '' }}">
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="customer_code" class="form-label">{{ __('reports.customer_code') }}</label>
                                <select class="form-select select2-ajax" id="customer_code_select"
                                    data-placeholder="{{ __('reports.click_here') }}">
                                    @if(!empty($validated['customer_id']) && !empty($validated['customer_code']))
                                        <option value="{{ $validated['customer_id'] }}" selected>
                                            {{ $validated['customer_code'] }}
                                        </option>
                                    @endif
                                </select>
                                <input type="hidden" name="customer_code" id="customer_code_hidden"
                                    value="{{ $validated['customer_code'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="customer_name" class="form-label">{{ __('reports.customer_name') }}</label>
                                <select class="form-select select2-ajax" id="customer_name_select"
                                    data-placeholder="{{ __('reports.click_here') }}">
                                    @if(!empty($validated['customer_id']) && !empty($validated['customer_name']))
                                        <option value="{{ $validated['customer_id'] }}" selected>
                                            {{ $validated['customer_name'] }}
                                        </option>
                                    @endif
                                </select>
                                <input type="hidden" name="customer_name" id="customer_name_hidden"
                                    value="{{ $validated['customer_name'] ?? '' }}">
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
                                    <option value="all" {{ ($validated['status'] ?? 'all') === 'all' ? 'selected' : '' }}>
                                        {{ __('reports.click_here') }}
                                    </option>
                                    <option value="draft" {{ ($validated['status'] ?? '') === 'draft' ? 'selected' : '' }}>
                                        {{ __('reports.draft') }}
                                    </option>
                                    <option value="posted" {{ ($validated['status'] ?? '') === 'posted' ? 'selected' : '' }}>
                                        {{ __('reports.posted') }}
                                    </option>
                                    <option value="cancelled" {{ ($validated['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>{{ __('reports.cancelled') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="mb-3 w-100 p-1 d-flex gap-1">
                                <button type="submit" class="btn btn-primary p-2">
                                    <i class="fas fa-filter"></i>
                                </button>
                                <a href="{{ route('reports.sales.by-customer') }}" class="btn btn-secondary p-2">
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
                        <h5 class="mb-0">{{ __('reports.customer_summary') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>{{ __('reports.customer') }}</th>
                                        <th class="text-end">{{ __('reports.invoices') }}</th>
                                        <th class="text-end">{{ __('reports.total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customerSummary as $summary)
                                        <tr>
                                            <td>{{ $summary->customer->name ?? 'N/A' }}</td>
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
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-success dropdown-toggle" type="button" id="exportDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-download"></i> {{ __('reports.export') }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('reports.sales.export-by-customer', array_merge($validated, ['format' => 'excel'])) }}">
                                        <i class="far fa-file-excel text-success me-2"></i> {{ __('reports.export_excel') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('reports.sales.export-by-customer', array_merge($validated, ['format' => 'pdf'])) }}"
                                        target="_blank">
                                        <i class="far fa-file-pdf text-danger me-2"></i> {{ __('reports.export_pdf') }}
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('reports.sales.export-by-customer', array_merge($validated, ['format' => 'csv'])) }}">
                                        <i class="fas fa-file-csv text-info me-2"></i> {{ __('reports.export_csv') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>{{ __('reports.invoice_number') }}</th>
                                        <th>{{ __('reports.customer') }}</th>
                                        <th>{{ __('reports.date') }}</th>
                                        <th class="text-end">{{ __('reports.amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invoices as $invoice)
                                        <tr>
                                            <td>
                                                <a href="{{ route('sales.invoices.show', $invoice->id) }}" class="text-primary text-decoration-none fw-bold">
                                                    {{ $invoice->document_number }}
                                                </a>
                                            </td>
                                            <td>{{ $invoice->customer->name ?? 'N/A' }}</td>
                                            <td>{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                                            <td class="text-end">{{ number_format($invoice->total_amount, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">{{ __('reports.no_records') }}</td>
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

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            // Function to common Select2 AJAX setup
            function initSelect2Ajax(elementId, searchType) {
                $(elementId).select2({
                    theme: 'bootstrap-5',
                    allowClear: true,
                    placeholder: $(elementId).data('placeholder'),
                    ajax: {
                        url: "{{ route('ajax.customers.search') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term,
                                type: searchType // 'code' or 'name'
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data.map(function (customer) {
                                    return {
                                        id: customer.id,
                                        text: customer.text, // "Name (Code)"
                                        customer: customer
                                    };
                                })
                            };
                        },
                        cache: true
                    }
                });
            }

            // Initialize both selects
            initSelect2Ajax('#customer_code_select', 'code');
            initSelect2Ajax('#customer_name_select', 'name');

            // Handle selection synchronization
            $('#customer_code_select').on('select2:select', function (e) {
                const data = e.params.data;
                const customer = data.customer;

                $('#customer_id').val(customer.id);
                $('#customer_code_hidden').val(customer.code);
                $('#customer_name_hidden').val(customer.name);

                // Update the other select
                var nameOption = new Option(customer.text, customer.id, true, true);
                $('#customer_name_select').append(nameOption).trigger('change');
            });

            $('#customer_name_select').on('select2:select', function (e) {
                const data = e.params.data;
                const customer = data.customer;

                $('#customer_id').val(customer.id);
                $('#customer_code_hidden').val(customer.code);
                $('#customer_name_hidden').val(customer.name);

                // Update the other select
                var codeOption = new Option(customer.text, customer.id, true, true);
                $('#customer_code_select').append(codeOption).trigger('change');
            });

            // Handle clearing
            $('.select2-ajax').on('select2:clear', function () {
                $('#customer_id').val('');
                $('#customer_code_hidden').val('');
                $('#customer_name_hidden').val('');
                $('.select2-ajax').val(null).trigger('change');
            });
        });
    </script>
@endpush
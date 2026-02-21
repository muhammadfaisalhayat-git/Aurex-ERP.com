@extends('layouts.app')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <style>
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px !important;
            font-size: 0.875rem !important;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
        }
    </style>
@endpush

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
                        <input type="hidden" id="product_id" name="product_id" value="{{ $validated['product_id'] ?? '' }}">
                        <input type="hidden" id="invoice_id" name="invoice_id" value="{{ $validated['invoice_id'] ?? '' }}">
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="item_code" class="form-label">{{ __('reports.item_code') }}</label>
                                <select class="form-select select2-ajax" id="item_code_select"
                                    data-placeholder="{{ __('reports.click_here') }}">
                                    @if(!empty($validated['product_id']) && !empty($validated['item_code']))
                                        <option value="{{ $validated['product_id'] }}" selected>{{ $validated['item_code'] }}
                                        </option>
                                    @endif
                                </select>
                                <input type="hidden" name="item_code" id="item_code_hidden"
                                    value="{{ $validated['item_code'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="item_name" class="form-label">{{ __('reports.item_name') }}</label>
                                <select class="form-select select2-ajax" id="item_name_select"
                                    data-placeholder="{{ __('reports.click_here') }}">
                                    @if(!empty($validated['product_id']) && !empty($validated['item_name']))
                                        <option value="{{ $validated['product_id'] }}" selected>{{ $validated['item_name'] }}
                                        </option>
                                    @endif
                                </select>
                                <input type="hidden" name="item_name" id="item_name_hidden"
                                    value="{{ $validated['item_name'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="invoice_number" class="form-label">{{ __('reports.invoice_number') }}</label>
                                <select class="form-select select2-ajax" id="invoice_select"
                                    data-placeholder="{{ __('reports.click_here') }}">
                                    @if(!empty($validated['invoice_id']) && !empty($validated['invoice_number']))
                                        <option value="{{ $validated['invoice_id'] }}" selected>
                                            {{ $validated['invoice_number'] }}
                                        </option>
                                    @endif
                                </select>
                                <input type="hidden" name="invoice_number" id="invoice_number_hidden"
                                    value="{{ $validated['invoice_number'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">{{ __('reports.category') }}</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">{{ __('reports.click_here') }}</option>
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
                        <div class="col-md-1">
                            <div class="mb-3">
                                <label for="date_to" class="form-label">{{ __('reports.date_to') }}</label>
                                <input type="date" class="form-control" id="date_to" name="date_to"
                                    value="{{ $validated['date_to'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <div class="mb-3 w-100 d-flex gap-1">
                                <button type="submit" class="btn btn-primary btn-sm p-2">
                                    <i class="fas fa-filter"></i>
                                </button>
                                <a href="{{ route('reports.sales.by-item') }}" class="btn btn-secondary btn-sm p-2"
                                    id="btn-reset">
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
                        <div class="dropdown">
                            <button class="btn btn-sm btn-success dropdown-toggle" type="button" id="exportDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-download"></i> {{ __('reports.export') }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('reports.sales.export-by-item', array_merge($validated, ['format' => 'excel'])) }}">
                                        <i class="far fa-file-excel text-success me-2"></i> {{ __('reports.export_excel') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('reports.sales.export-by-item', array_merge($validated, ['format' => 'pdf'])) }}"
                                        target="_blank">
                                        <i class="far fa-file-pdf text-danger me-2"></i> {{ __('reports.export_pdf') }}
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('reports.sales.export-by-item', array_merge($validated, ['format' => 'csv'])) }}">
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
                                        <th>{{ __('reports.invoice') }}</th>
                                        <th>{{ __('reports.item') }}</th>
                                        <th class="text-end">{{ __('reports.qty') }}</th>
                                        <th class="text-end">{{ __('reports.amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($items as $item)
                                        <tr>
                                            <td>
                                                @if($item->salesInvoice)
                                                    <div class="d-flex align-items-center gap-2">
                                                        <a href="{{ route('sales.invoices.show', $item->salesInvoice->id) }}"
                                                            class="text-primary text-decoration-none fw-bold">
                                                            {{ $item->salesInvoice->document_number }}
                                                        </a>
                                                        <a href="{{ route('sales.invoices.show', $item->salesInvoice->id) }}"
                                                            class="btn btn-xs btn-info p-1 py-0" title="{{ __('messages.view') }}">
                                                            <i class="fas fa-eye fa-xs"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $item->product->name ?? 'N/A' }}</td>
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
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function initSalesReportFilters() {
            const itemCodeSelect = $('#item_code_select');
            const itemNameSelect = $('#item_name_select');
            const invoiceSelect = $('#invoice_select');
            const productIdInput = $('#product_id');
            const invoiceIdInput = $('#invoice_id');
            const itemCodeHidden = $('#item_code_hidden');
            const itemNameHidden = $('#item_name_hidden');
            const invoiceNumberHidden = $('#invoice_number_hidden');

            if (!itemCodeSelect.length && !invoiceSelect.length) return;

            function initProductSelect(el, isCode) {
                el.select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    allowClear: true,
                    placeholder: el.data('placeholder'),
                    ajax: {
                        url: "{{ route('ajax.products.search') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data.map(i => ({
                                    id: i.id,
                                    text: isCode ? i.code : ({{ app()->getLocale() === 'ar' ? 'true' : 'false' }} ? (i.name_ar || i.name_en) : (i.name_en || i.name_ar)),
                                    code: i.code,
                                    name: {{ app()->getLocale() === 'ar' ? 'true' : 'false' }} ? (i.name_ar || i.name_en) : (i.name_en || i.name_ar)
                                }))
                            };
                        },
                        cache: true
                    }
                });
            }

            function initInvoiceSelect(el) {
                el.select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    allowClear: true,
                    placeholder: el.data('placeholder'),
                    ajax: {
                        url: "{{ route('ajax.invoices.search') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data.map(i => ({
                                    id: i.id,
                                    text: i.document_number,
                                    document_number: i.document_number
                                }))
                            };
                        },
                        cache: true
                    }
                });
            }

            initProductSelect(itemCodeSelect, true);
            initProductSelect(itemNameSelect, false);
            initInvoiceSelect(invoiceSelect);

            itemCodeSelect.on('select2:select', function (e) {
                const data = e.params.data;
                productIdInput.val(data.id);
                itemCodeHidden.val(data.code);
                itemNameHidden.val(data.name);

                // Sync the other select
                if (itemNameSelect.val() !== data.id) {
                    const newOption = new Option(data.name, data.id, true, true);
                    itemNameSelect.append(newOption).trigger('change');
                }
            });

            itemNameSelect.on('select2:select', function (e) {
                const data = e.params.data;
                productIdInput.val(data.id);
                itemCodeHidden.val(data.code);
                itemNameHidden.val(data.name);

                // Sync the other select
                if (itemCodeSelect.val() !== data.id) {
                    const newOption = new Option(data.code, data.id, true, true);
                    itemCodeSelect.append(newOption).trigger('change');
                }
            });

            invoiceSelect.on('select2:select', function (e) {
                const data = e.params.data;
                invoiceIdInput.val(data.id);
                invoiceNumberHidden.val(data.document_number);
            });

            itemCodeSelect.on('change', function () {
                if (!$(this).val()) {
                    productIdInput.val('');
                    itemCodeHidden.val('');
                    itemNameHidden.val('');
                    if (itemNameSelect.val()) {
                        itemNameSelect.val(null).trigger('change');
                    }
                }
            });

            itemNameSelect.on('change', function () {
                if (!$(this).val()) {
                    productIdInput.val('');
                    itemCodeHidden.val('');
                    itemNameHidden.val('');
                    if (itemCodeSelect.val()) {
                        itemCodeSelect.val(null).trigger('change');
                    }
                }
            });

            invoiceSelect.on('change', function () {
                if (!$(this).val()) {
                    invoiceIdInput.val('');
                    invoiceNumberHidden.val('');
                }
            });
        }

        $(document).ready(initSalesReportFilters);
        $(document).on('turbo:load', initSalesReportFilters);
    </script>
@endpush
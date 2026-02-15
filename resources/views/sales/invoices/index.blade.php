@extends('layouts.app')

@section('title', __('messages.sales_invoices'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.sales_invoices') }}</h1>
            <div>
                @can('create invoices')
                    <a href="{{ route('inventory.barcodes.index') }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-barcode me-2"></i> {{ __('messages.barcode_generator') }}
                    </a>
                    <a href="{{ route('sales.invoices.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i> {{ __('messages.create') }}
                    </a>
                @endcan
            </div>
        </div>

        <div class="card mb-4 glassy">
            <div class="card-body">
                <form action="{{ route('sales.invoices.index') }}" method="GET" class="row g-4">
                    {{-- Row 1: Invoice Number & Customer --}}
                    <div class="col-md-4">
                        <label for="invoice_number" class="form-label fw-bold">{{ __('messages.invoice_number') }}</label>
                        <div class="input-group">
                            <input type="text" name="invoice_number" id="invoice_number" class="form-control bg-white"
                                value="{{ request('invoice_number') }}" placeholder="Search Invoice #">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="customer_search" class="form-label fw-bold">{{ __('messages.customer') }}</label>
                        <div class="position-relative">
                            <input type="text" id="customer_search" name="customer_name"
                                class="form-control bg-white shadow-none" placeholder="{{ __('messages.all_customers') }}"
                                value="{{ request('customer_id') ? ($customers->find(request('customer_id'))->name_en ?? '') : request('customer_name') }}"
                                autocomplete="off">
                            <input type="hidden" name="customer_id" id="customer_id" value="{{ request('customer_id') }}">
                            <div id="customer-results" class="search-results-container glassy" style="display: none;">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="status" class="form-label fw-bold">{{ __('messages.status') }}</label>
                        <select name="status" id="status" class="form-select bg-white shadow-none">
                            <option value="">{{ __('messages.all_statuses') }}</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>
                                {{ __('messages.draft') }}
                            </option>
                            <option value="posted" {{ request('status') == 'posted' ? 'selected' : '' }}>
                                {{ __('messages.posted') }}
                            </option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>{{ __('messages.paid') }}
                            </option>
                            <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>
                                {{ __('messages.partial') }}
                            </option>
                        </select>
                    </div>

                    {{-- Row 2: Date Range & Total Range --}}
                    <div class="col-md-3">
                        <label for="date_from" class="form-label fw-bold">{{ __('messages.date_from') }}</label>
                        <input type="date" name="date_from" id="date_from" class="form-control bg-white shadow-none"
                            value="{{ request('date_from') }}" placeholder="mm/dd/yyyy">
                    </div>

                    <div class="col-md-3">
                        <label for="date_to" class="form-label fw-bold">{{ __('messages.date_to') }}</label>
                        <input type="date" name="date_to" id="date_to" class="form-control bg-white shadow-none"
                            value="{{ request('date_to') }}" placeholder="mm/dd/yyyy">
                    </div>

                    <div class="col-md-3">
                        <label for="total" class="form-label fw-bold">{{ __('messages.total') }}</label>
                        <input type="number" name="total" id="total" class="form-control bg-white shadow-none" step="0.01"
                            value="{{ request('total') }}" placeholder="0.00">
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> {{ __('messages.search') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('messages.invoice_number') }}</th>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.customer') }}</th>
                                <th>{{ __('messages.total') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $invoice)
                                <tr>
                                    <td>
                                        <a href="{{ route('sales.invoices.show', $invoice) }}">
                                            {{ $invoice->invoice_number }}
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ $invoice->document_number }}</small>
                                    </td>
                                    <td>{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                                    <td>{{ $invoice->customer->name ?? '-' }}</td>
                                    <td>{{ number_format($invoice->total_amount, 2) }}</td>
                                    <td>
                                        @php
                                            $statusClass = [
                                                'draft' => 'secondary',
                                                'posted' => 'info',
                                                'paid' => 'success',
                                                'partial' => 'primary',
                                                'overdue' => 'danger',
                                                'cancelled' => 'dark',
                                            ][$invoice->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">
                                            {{ __('messages.' . $invoice->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            @can('view invoices')
                                                <a href="{{ route('sales.invoices.show', $invoice) }}" class="btn btn-sm btn-info"
                                                    title="{{ __('messages.view') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan
                                            @if($invoice->isEditable())
                                                @can('edit invoices')
                                                    <a href="{{ route('sales.invoices.edit', $invoice) }}"
                                                        class="btn btn-sm btn-primary" title="{{ __('messages.edit') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                            @endif
                                            @can('view invoices')
                                                <a href="{{ route('sales.invoices.pdf', $invoice) }}"
                                                    class="btn btn-sm btn-secondary" title="{{ __('messages.download_pdf') }}">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">{{ __('messages.no_records_found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $invoices->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const customerSearch = document.getElementById('customer_search');
                const customerId = document.getElementById('customer_id');
                const customerResults = document.getElementById('customer-results');
                const customerData = @json($customers->map(function ($c) {
                    return ['id' => $c->id, 'name' => $c->name_en, 'code' => $c->code];
                }));

                const invoiceNumberSearch = document.getElementById('invoice_number');

                // F2 Shortcut
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'F2') {
                        e.preventDefault();
                        invoiceNumberSearch.focus();
                        invoiceNumberSearch.select();
                    }
                });

                function performSearch(val) {
                    const search = val.toLowerCase();
                    const filtered = customerData.filter(c =>
                        c.name.toLowerCase().includes(search) ||
                        (c.code && c.code.toLowerCase().includes(search))
                    );

                    renderResults(customerResults, filtered, (customer) => {
                        customerSearch.value = customer.name;
                        customerId.value = customer.id;
                        customerResults.style.display = 'none';
                    });
                }

                // Searchable Customer Dropdown
                customerSearch.addEventListener('focus', function () {
                    performSearch(this.value);
                });

                customerSearch.addEventListener('input', function () {
                    if (this.value.length < 1) {
                        customerId.value = '';
                    }
                    performSearch(this.value);
                });

                function renderResults(container, data, onSelect) {
                    if (data.length === 0) {
                        container.style.display = 'none';
                        return;
                    }

                    container.innerHTML = data.map(item => `
                                                                                <div class="search-result-item" data-id="${item.id}">
                                                                                    <div class="item-title">${item.name}</div>
                                                                                    ${item.code ? `<div class="item-subtitle">${item.code}</div>` : ''}
                                                                                </div>
                                                                            `).join('');

                    container.style.display = 'block';

                    container.querySelectorAll('.search-result-item').forEach((el, index) => {
                        el.addEventListener('click', () => {
                            const item = data[index];
                            onSelect(item);
                        });
                    });
                }

                // Close dropdown when clicking outside
                document.addEventListener('click', function (e) {
                    if (!customerSearch.contains(e.target) && !customerResults.contains(e.target)) {
                        customerResults.style.display = 'none';
                    }
                });

                // Keyboard navigation for dropdown
                customerSearch.addEventListener('keydown', function (e) {
                    const items = customerResults.querySelectorAll('.search-result-item');
                    let activeIndex = Array.from(items).findIndex(i => i.classList.contains('active'));

                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        if (activeIndex < items.length - 1) {
                            if (activeIndex > -1) items[activeIndex].classList.remove('active');
                            items[++activeIndex].classList.add('active');
                            items[activeIndex].scrollIntoView({ block: 'nearest' });
                        }
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        if (activeIndex > 0) {
                            items[activeIndex].classList.remove('active');
                            items[--activeIndex].classList.add('active');
                            items[activeIndex].scrollIntoView({ block: 'nearest' });
                        }
                    } else if (e.key === 'Enter' && activeIndex > -1) {
                        e.preventDefault();
                        items[activeIndex].click();
                    } else if (e.key === 'Escape') {
                        customerResults.style.display = 'none';
                    }
                });
            });
        </script>
    @endpush
@endsection
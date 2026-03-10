@extends('layouts.app')

@section('title', __('sales.create_invoice'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('sales.create_invoice') }}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('sales.invoices.store') }}" method="POST" id="invoice-form"
                            class="glassy-form">
                            @csrf
                            <div class="row mb-4 border-bottom pb-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold"><i class="fas fa-file-import me-1"></i>
                                        {{ __('sales.import_data') }}</label>
                                    <select id="import_source_type" class="form-select">
                                        <option value="">-- {{ __('sales.select_source') }} --</option>
                                        <option value="quotation">{{ __('sales.quotation') }}</option>
                                        <option value="customer_request">{{ __('sales.customer_request') }}</option>
                                        <option value="sales_return">{{ __('sales.return_invoice') }}</option>
                                        <option value="sales_order">{{ __('sales.sales_order') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6" id="import-source-container" style="display: none;">
                                    <label class="form-label fw-bold">{{ __('sales.source_document') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="import_source_display" readonly
                                            placeholder="{{ __('sales.search_document_placeholder') }}">
                                        <button class="btn btn-primary" type="button" id="btn-show-import-modal">
                                            <i class="fas fa-search me-1"></i> {{ __('common.select') }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="invoice_number" class="form-label">{{ __('sales.invoice_number') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('invoice_number') is-invalid @enderror"
                                        id="invoice_number" name="invoice_number"
                                        value="{{ old('invoice_number', $documentNumber) }}" readonly>
                                    @error('invoice_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="invoice_date" class="form-label">{{ __('sales.date') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('invoice_date') is-invalid @enderror"
                                        id="invoice_date" name="invoice_date"
                                        value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                                    @error('invoice_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="due_date" class="form-label">{{ __('sales.due_date') }}</label>
                                    <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                                        id="due_date" name="due_date" value="{{ old('due_date') }}">
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="customer_id" class="form-label">{{ __('sales.customer') }}</label>
                                    <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id">
                                        <option value="">{{ __('sales.cash_customer') }}</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name_en }}{{ $customer->customer_code ? " ($customer->customer_code)" : "" }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="branch_id" class="form-label">{{ __('sales.branch') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('branch_id') is-invalid @enderror" id="branch_id"
                                        name="branch_id" required>
                                        @php 
                                            $defaultBranchId = old('branch_id', session('active_branch_id') ?? auth()->user()->branch_id ?? ($branches->first()->id ?? null));
                                        @endphp
                                        @if(!$defaultBranchId)
                                            <option value="">{{ __('common.select') }}</option>
                                        @endif
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ $defaultBranchId == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="warehouse_id" class="form-label">{{ __('sales.warehouse') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('warehouse_id') is-invalid @enderror"
                                        id="warehouse_id" name="warehouse_id" required>
                                        <option value="">{{ __('common.select') }}</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" 
                                                data-branch-id="{{ $warehouse->branch_id }}"
                                                {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                                {{ $warehouse->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('warehouse_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="payment_terms" class="form-label">{{ __('sales.payment_terms') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('payment_terms') is-invalid @enderror"
                                        id="payment_terms" name="payment_terms" required>
                                        <option value="">{{ __('common.select') }}</option>
                                        <option value="cash" {{ old('payment_terms', 'cash') == 'cash' ? 'selected' : '' }}>
                                            {{ __('sales.cash') }}
                                        </option>
                                        <option value="credit" {{ old('payment_terms') == 'credit' ? 'selected' : '' }}>
                                            {{ __('sales.credit') }}
                                        </option>
                                        <option value="installment" {{ old('payment_terms') == 'installment' ? 'selected' : '' }}>{{ __('sales.installment') }}</option>
                                    </select>
                                    @error('payment_terms')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="salesman_id" class="form-label">{{ __('sales.salesman') }}</label>
                                    <select class="form-select @error('salesman_id') is-invalid @enderror"
                                        id="salesman_id" name="salesman_id">
                                        <option value="">{{ __('sales.select_salesman') }}</option>
                                        @foreach($salesmen as $salesman)
                                            <option value="{{ $salesman->id }}" {{ old('salesman_id') == $salesman->id ? 'selected' : '' }}>
                                                {{ $salesman->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('salesman_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="reference_number"
                                        class="form-label">{{ __('sales.reference_number') }}</label>
                                    <input type="text" class="form-control @error('reference_number') is-invalid @enderror"
                                        id="reference_number" name="reference_number" value="{{ old('reference_number') }}">
                                    @error('reference_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <h4 class="mt-4">{{ __('sales.items') }}</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="items-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 45%;">{{ __('sales.product') }}</th>
                                            <th style="width: 10%;">{{ __('sales.quantity') }}</th>
                                            <th style="width: 15%;">{{ __('sales.unit_price') }}</th>
                                            <th style="width: 10%;">{{ __('sales.discount') }} (%)</th>
                                            <th style="width: 12%;" class="d-none">{{ __('sales.vat') }}</th>
                                            <th style="width: 16%;">{{ __('sales.total') }}</th>
                                            <th style="width: 4%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Items will be added here via JS --}}
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="7">
                                                <button type="button" class="btn btn-sm btn-success" id="add-item-btn">
                                                    <i class="fas fa-plus"></i> {{ __('sales.add_item') }}
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="text-end fw-bold">{{ __('sales.subtotal') }}</td>
                                            <td class="text-end" id="subtotal">0.00</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="text-end fw-bold">{{ __('sales.vat') }}</td>
                                            <td class="text-end" id="tax_amount">0.00</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="text-end fw-bold">{{ __('sales.grand_total') }}</td>
                                            <td class="text-end fw-bold" id="grand_total">0.00</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">{{ __('sales.notes') }}</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes"
                                    rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-end">
                                <a href="{{ route('sales.invoices.index') }}"
                                    class="btn btn-secondary">{{ __('common.cancel') }}</a>
                                <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/template" id="item-row-template">
                                                                                                        <tr>
                                                                                                             <td class="position-relative product-cell">
                                                                                                                 <input type="text" class="form-control product-search-input" 
                                                                                                                        placeholder="Type to search products..." 
                                                                                                                        autocomplete="off"
                                                                                                                        data-product-id="">
                                                                                                                 <input type="hidden" class="product-id-input" name="items[INDEX][product_id]" required>
                                                                                                                 <div class="search-results-container glassy product-results"></div>
                                                                                                             </td>
                                                                                                            <td>
                                                                                                                <input type="number" class="form-control quantity-input" name="items[INDEX][quantity]" step="0.001" min="0.001" value="1" required>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                <input type="number" class="form-control price-input" name="items[INDEX][unit_price]" step="0.01" min="0" required>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                <input type="number" class="form-control discount-input" name="items[INDEX][discount_percentage]" step="0.01" min="0" max="100" value="0">
                                                                                                            </td>
                                                                                                            <td class="d-none">
                                                                                                                <input type="text" class="form-control tax-display" placeholder="{{ __('sales.vat') }}" readonly>
                                                                                                                <input type="hidden" class="tax-rate-input">
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                <input type="text" class="form-control total-display" readonly>
                                                                                                            </td>
                                                                                                            <td class="text-center">
                                                                                                        <button type="button" class="btn btn-sm btn-danger remove-item">
                                                                                                                    <i class="fas fa-trash"></i>
                                                                                                                </button>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    </script>
    
    <style>
        /* Fix search result clipping in table-responsive */
        .table-responsive {
            overflow: visible !important;
            padding-bottom: 100px; /* Space for the dropdown */
            margin-bottom: -100px;
        }
        
        #items-table tr {
            position: static; /* Let the absolute dropdown reference the relative cell */
        }
        
        .product-cell {
            position: relative;
        }
        
        .search-results-container {
            z-index: 1060 !important;
            width: 100%;
            top: 100%;
            left: 0;
            background: #fff;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            max-height: 300px;
            overflow-y: auto;
        }
    </style>

    <!-- Import Source Modal -->
    <div class="modal fade" id="importSourceModal" tabindex="-1" aria-labelledby="importSourceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importSourceModalLabel">{{ __('sales.select_source') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="modal-search-input"
                                placeholder="{{ __('common.search') }}...">
                        </div>
                    </div>
                    <div class="table-responsive" style="max-height: 400px;">
                        <table class="table table-hover" id="modal-results-table">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>{{ __('sales.document_number') }}</th>
                                    <th>{{ __('sales.customer') }}</th>
                                    <th>{{ __('sales.date') }}</th>
                                    <th>{{ __('sales.amount') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Results will be injected here --}}
                            </tbody>
                        </table>
                    </div>
                    <div id="modal-loader" class="text-center py-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('turbo:load', function () {
            // 1. Initialize Variables & DOM Elements
            let itemIndex = 0;
            const defaultTaxRate = {{ $taxSetting->default_tax_rate ?? 0 }};
            const tableBody = document.querySelector('#items-table tbody');
            const template = document.getElementById('item-row-template').innerHTML;
            const branchSelect = document.getElementById('branch_id');
            const warehouseSelect = document.getElementById('warehouse_id');
            const customerSelect = document.getElementById('customer_id');
            const salesmanSelect = document.getElementById('salesman_id');

            if (!branchSelect) return;

            // 2. Data Initialization
            // Products pre-loading removed in favor of AJAX search
            const products = [];

            const warehousesJson = [
                @foreach($warehouses as $warehouse)
                {
                    id: {{ $warehouse->id }},
                    name: "{{ addslashes($warehouse->name_en) }}",
                    branch_id: {{ $warehouse->branch_id }}
                },
                @endforeach
            ];

            // 3. Helper Functions
            function initSelect2() {
                if (window.jQuery) {
                    const selectConfigs = [
                        { id: '#customer_id', placeholder: '{{ __("sales.cash_customer") }}' },
                        { id: '#branch_id', placeholder: '{{ __("common.select") }}' },
                        { id: '#warehouse_id', placeholder: '{{ __("common.select") }}' },
                        { id: '#salesman_id', placeholder: '{{ __("sales.select_salesman") }}' },
                        { id: '#import_source_type', placeholder: '--' },
                        { id: '#payment_terms', placeholder: '--' }
                    ];

                    selectConfigs.forEach(config => {
                        const $el = $(config.id);
                        $el.select2({
                            theme: 'bootstrap-5',
                            width: '100%',
                            dir: document.documentElement.dir || 'ltr',
                            placeholder: config.placeholder,
                            allowClear: true
                        });
                        
                        // Move focus to next field after selection
                        $el.on('select2:select', function (e) {
                            const selection = $(this).next('.select2-container').find('.select2-selection')[0];
                            if (selection) {
                                // Short delay to let Select2 finish its internal closing logic
                                setTimeout(() => moveFocus(selection), 50);
                            }
                        });
                    });
                }
            }

            function filterWarehouses() {
                const selectedBranchId = branchSelect.value;
                const currentWarehouseVal = warehouseSelect.value;
                const $warehouse = $(warehouseSelect);
                
                if ($warehouse.data('select2')) $warehouse.select2('destroy');

                warehouseSelect.innerHTML = '<option value="">{{ __("common.select") }}</option>';
                
                let matches = [];
                let valueStillExists = false;
                warehousesJson.forEach(w => {
                    if (w.branch_id.toString() === selectedBranchId) {
                        const newOpt = new Option(w.name, w.id);
                        if (w.id.toString() === currentWarehouseVal) {
                            newOpt.selected = true;
                            valueStillExists = true;
                        }
                        warehouseSelect.add(newOpt);
                        matches.push(w.id);
                    }
                });

                if (!valueStillExists) warehouseSelect.value = "";
                if (matches.length === 1 && !warehouseSelect.value) warehouseSelect.value = matches[0];

                $warehouse.select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    dir: document.documentElement.dir || 'ltr',
                    allowClear: true,
                    placeholder: '{{ __("common.select") }}'
                }).on('select2:select', function() {
                    const selection = $(this).next('.select2-container').find('.select2-selection')[0];
                    if (selection) setTimeout(() => moveFocus(selection), 50);
                });
            }

            function moveFocus(currentElement) {
                const form = document.getElementById('invoice-form');
                // Ensure we find the right element even if it's inside a Select2 container
                const target = currentElement.closest('.select2-selection') || currentElement;
                
                const focusableElements = 'input:not([type="hidden"]):not([disabled]):not([readonly]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]):not(.remove-item):not(#add-item-btn), .select2-selection--single';
                const elements = Array.from(form.querySelectorAll(focusableElements)).filter(el => {
                    return el.offsetParent !== null || el.classList.contains('select2-selection--single');
                });
                
                const index = elements.indexOf(target);
                
                if (index > -1 && index < elements.length - 1) {
                    let nextElement = elements[index + 1];
                    
                    // If it's a Select2 selection, open it
                    if (nextElement.classList.contains('select2-selection')) {
                        const select = nextElement.closest('.mb-3, td').querySelector('select');
                        if (select && select.id) {
                            $(`#${select.id}`).select2('open');
                        }
                    } else {
                        nextElement.focus();
                        if (nextElement.select) nextElement.select();
                    }
                }
            }

            let lastAddTimestamp = 0;
            function addItem() {
                const now = Date.now();
                if (now - lastAddTimestamp < 300) return; // Debounce 300ms
                lastAddTimestamp = now;

                const html = template.replace(/INDEX/g, itemIndex++);
                tableBody.insertAdjacentHTML('beforeend', html);
                const newRow = tableBody.lastElementChild;
                initProductSearch(newRow);
            }

            function initProductSearch(row) {
                const input = row.querySelector('.product-search-input');
                const results = row.querySelector('.product-results');
                const hidden = row.querySelector('.product-id-input');

                input.addEventListener('input', function () {
                    const search = this.value;
                    const warehouseId = warehouseSelect.value;
                    const branchId = branchSelect.value;
                    
                    if (!warehouseId) {
                        Swal.fire({
                            icon: 'warning',
                            title: '{{ __("messages.select_warehouse_first") ?? "Select Warehouse First" }}',
                            text: '{{ __("messages.please_select_warehouse_before_searching") ?? "Please select a warehouse before searching for items." }}',
                            confirmButtonText: '{{ __("messages.ok") ?? "OK" }}'
                        });
                        this.value = '';
                        results.style.display = 'none';
                        return;
                    }

                    if (search.length < 1) {
                        results.style.display = 'none';
                        return;
                    }

                    fetch(`{{ route('inventory.products.ajax-search') }}?q=${encodeURIComponent(search)}&warehouse_id=${warehouseId}&branch_id=${branchId}`)
                        .then(response => response.json())
                        .then(data => {
                            renderResults(results, data, (product) => {
                                selectItem(product, input, hidden, row, results);
                            }, true);
                        })
                        .catch(err => console.error('Error fetching products:', err));
                });

                input.addEventListener('keydown', function (e) {
                    const items = results.querySelectorAll('.search-result-item');
                    let activeIndex = Array.from(items).findIndex(item => item.classList.contains('active'));

                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        if (results.style.display === 'none') {
                            this.dispatchEvent(new Event('input'));
                            return;
                        }
                        if (activeIndex < items.length - 1) {
                            if (activeIndex >= 0) items[activeIndex].classList.remove('active');
                            items[activeIndex + 1].classList.add('active');
                            items[activeIndex + 1].scrollIntoView({ block: 'nearest' });
                        }
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        if (activeIndex > 0) {
                            items[activeIndex].classList.remove('active');
                            items[activeIndex - 1].classList.add('active');
                            items[activeIndex - 1].scrollIntoView({ block: 'nearest' });
                        }
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        if (results.style.display !== 'none' && activeIndex >= 0) {
                            items[activeIndex].click();
                        }
                    } else if (e.key === 'Escape') {
                        results.style.display = 'none';
                    }
                });

                input.addEventListener('focus', function() {
                    this.dispatchEvent(new Event('input'));
                });

                input.addEventListener('click', function() {
                    this.dispatchEvent(new Event('input'));
                });
            }

            function selectItem(product, input, hidden, row, results) {
                const stock = parseFloat(product.available_quantity) || 0;
                if (stock <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("messages.out_of_stock") ?? "Out of Stock" }}',
                        text: '{{ __("messages.product_not_available_in_warehouse") ?? "This product is not available in the selected warehouse." }}',
                        confirmButtonText: '{{ __("messages.ok") ?? "OK" }}'
                    });
                    results.style.display = 'none';
                    input.value = '';
                    return;
                }

                input.value = '{{ app()->getLocale() === 'ar' }}' === '1' ? (product.name_ar || product.name_en) : (product.name_en || product.name_ar);
                hidden.value = product.id;
                row.dataset.availableStock = product.available_quantity;
                row.querySelector('.price-input').value = product.sale_price || product.price;
                row.querySelector('.tax-rate-input').value = product.tax;
                results.style.display = 'none';
                calculateRow(row);
            }

            function renderResults(container, items, onSelect, isProduct = false) {
                if (items.length === 0) {
                    container.style.display = 'none';
                    return;
                }

                container.innerHTML = '';
                items.slice(0, 10).forEach((item) => {
                    const div = document.createElement('div');
                    div.className = 'search-result-item';

                    const currentLocale = '{{ app()->getLocale() }}';
                    const currentName = currentLocale === 'ar' ? (item.name_ar || item.name_en) : (item.name_en || item.name_ar);
                    const subName = currentLocale === 'ar' ? item.name_en : item.name_ar;

                    if (isProduct) {
                        const stockColor = item.available_quantity > 0 ? '#198754' : '#dc3545';
                        const costPrice = parseFloat(item.cost_price || 0).toFixed(2);
                        div.innerHTML = `
                            <div class="d-flex justify-content-between align-items-start w-100">
                                <div class="result-content pe-3 d-flex flex-column gap-1 flex-grow-1">
                                    <div class="fw-bold d-flex align-items-center gap-2 flex-wrap">
                                        ${item.code ? `<span style="background:#e9f0ff;color:#3d6bc7;font-size:0.7rem;font-weight:700;padding:1px 7px;border-radius:10px;flex-shrink:0;">${item.code}</span>` : ''}
                                        <span>${currentName}</span>
                                    </div>
                                    ${subName && subName !== currentName ? `<div class="small text-muted">${subName}</div>` : ''}
                                    <small class="fw-bold d-block" style="color:${stockColor};"><i class="fas fa-boxes" style="font-size:0.65rem;"></i> {{ __('messages.stock') }}: ${parseFloat(item.available_quantity).toFixed(item.decimals_count || 2)}</small>
                                </div>
                                <div class="d-flex flex-column align-items-end gap-1 flex-shrink-0 ms-auto small text-nowrap">
                                    <span style="color:#198754; font-weight:600;" title="Sale Price">{{ __('messages.sale_price') }}: ${parseFloat(item.sale_price).toFixed(2)}</span>
                                    <span style="color:#6c757d; font-weight:600;" title="Cost Price">{{ __('messages.cost_price') }}: ${costPrice}</span>
                                </div>
                            </div>
                        `;
                    } else {
                        div.innerHTML = `
                            <div class="item-title">${currentName}</div>
                            ${subName && subName !== currentName ? `<div class="item-subtitle text-muted" style="font-size: 0.8rem;">${subName}</div>` : ''}
                            ${item.code ? `<div class="item-subtitle">${item.code}</div>` : ''}
                        `;
                    }

                    div.addEventListener('click', () => onSelect(item));
                    container.appendChild(div);
                });

                container.style.display = 'block';
            }

            function calculateRow(row) {
                const qEl = row.querySelector('.quantity-input');
                const pEl = row.querySelector('.price-input');
                const dEl = row.querySelector('.discount-input');
                const trEl = row.querySelector('.tax-rate-input');

                if (!qEl || !pEl) return;

                const quantity = parseFloat(qEl.value) || 0;
                const availableStock = parseFloat(row.dataset.availableStock) || 0;
                const price = parseFloat(pEl.value) || 0;
                const discountPercent = parseFloat(dEl ? dEl.value : 0) || 0;
                const taxRate = parseFloat(trEl ? trEl.value : 0) || 0;

                if (quantity > availableStock && availableStock > 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: '{{ __("messages.stock_shortage") ?? "Stock Shortage" }}',
                        text: `{{ __('messages.quantity_exceeds_available_stock') ?? 'Quantity exceeds available stock' }} (${availableStock})`,
                        confirmButtonText: '{{ __("messages.ok") ?? "OK" }}'
                    });
                    qEl.value = availableStock;
                    return calculateRow(row); // Recalculate with corrected value
                }

                const gross = quantity * price * (1 - discountPercent / 100);
                const net = taxRate > 0 ? gross / (1 + taxRate / 100) : gross;
                const tax = gross - net;

                const tdEl = row.querySelector('.tax-display');
                const totEl = row.querySelector('.total-display');

                if (tdEl) tdEl.value = tax.toFixed(2);
                if (totEl) totEl.value = gross.toFixed(2);

                calculateTotals();
            }

            function calculateTotals() {
                let subtotal = 0;
                let taxAmount = 0;

                tableBody.querySelectorAll('tr').forEach(row => {
                    const qEl = row.querySelector('.quantity-input');
                    const pEl = row.querySelector('.price-input');
                    const dEl = row.querySelector('.discount-input');
                    const trEl = row.querySelector('.tax-rate-input');

                    if (qEl && pEl) {
                        const quantity = parseFloat(qEl.value) || 0;
                        const price = parseFloat(pEl.value) || 0;
                        const discountPercent = parseFloat(dEl ? dEl.value : 0) || 0;
                        const taxRate = parseFloat(trEl ? trEl.value : 0) || 0;

                        const gross = quantity * price * (1 - discountPercent / 100);
                        const net = taxRate > 0 ? gross / (1 + taxRate / 100) : gross;
                        const tax = gross - net;

                        subtotal += net;
                        taxAmount += tax;
                    }
                });

                const grandTotal = subtotal + taxAmount;
                document.getElementById('subtotal').textContent = subtotal.toFixed(2);
                document.getElementById('tax_amount').textContent = taxAmount.toFixed(2);
                document.getElementById('grand_total').textContent = grandTotal.toFixed(2);
            }

            // 4. Import Data Logic
            const importModal = new bootstrap.Modal(document.getElementById('importSourceModal'));
            const modalTableBody = document.querySelector('#modal-results-table tbody');
            const modalSearchInput = document.getElementById('modal-search-input');
            const modalLoader = document.getElementById('modal-loader');

            $('#import_source_type').on('change', function () {
                if ($(this).val()) {
                    document.getElementById('import-source-container').style.display = 'block';
                    document.getElementById('import_source_display').value = '';
                    importModal.show();
                    fetchSourceDocuments($(this).val(), '');
                } else {
                    document.getElementById('import-source-container').style.display = 'none';
                }
            });

            document.getElementById('btn-show-import-modal').addEventListener('click', () => importModal.show());

            modalSearchInput.addEventListener('input', function () {
                fetchSourceDocuments($('#import_source_type').val(), this.value);
            });

            function fetchSourceDocuments(type, query) {
                modalTableBody.innerHTML = '';
                modalLoader.style.display = 'block';

                fetch(`{{ route('sales.invoices.import-sources') }}?type=${type}&q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        modalLoader.style.display = 'none';
                        if (data.length === 0) {
                            modalTableBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">{{ __("common.no_results") }}</td></tr>';
                            return;
                        }

                        data.forEach(item => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${item.text}</td>
                                <td>${item.customer_name || '-'}</td>
                                <td>${item.date || '-'}</td>
                                <td>${item.total_amount ? parseFloat(item.total_amount).toFixed(2) : '-'}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-primary select-document" data-id="${item.id}">{{ __("common.select") }}</button>
                                </td>
                            `;

                            tr.querySelector('.select-document').addEventListener('click', function () {
                                if (confirm('Importing this document will clear existing items. Continue?')) {
                                    importModal.hide();
                                    loadSourceData(type, item.id);
                                    document.getElementById('import_source_display').value = item.text;
                                }
                            });
                            modalTableBody.appendChild(tr);
                        });
                    });
            }

            function loadSourceData(type, id) {
                fetch(`{{ route('sales.invoices.source-data', ['type' => '__TYPE__', 'id' => '__ID__']) }}`.replace('__TYPE__', type).replace('__ID__', id))
                    .then(response => response.json())
                    .then(data => {
                        if (data.customer_id) $(customerSelect).val(data.customer_id).trigger('change');
                        if (data.branch_id) {
                            $(branchSelect).val(data.branch_id).trigger('change');
                            setTimeout(() => { if (data.warehouse_id) $(warehouseSelect).val(data.warehouse_id).trigger('change'); }, 100);
                        }
                        if (data.salesman_id) $(salesmanSelect).val(data.salesman_id).trigger('change');

                        tableBody.innerHTML = '';
                        itemIndex = 0;
                        let skippedItems = [];
                        data.items.forEach(item => {
                            const stock = parseFloat(item.available_quantity) || 0;
                            if (stock <= 0) {
                                skippedItems.push(item.product_name);
                                return;
                            }

                            addItem();
                            const row = tableBody.lastElementChild;
                            row.querySelector('.product-search-input').value = item.product_name;
                            row.querySelector('.product-id-input').value = item.product_id;
                            row.querySelector('.quantity-input').value = Math.min(item.quantity, stock);
                            row.querySelector('.price-input').value = item.unit_price;
                            row.querySelector('.discount-input').value = item.discount_percentage;
                            row.querySelector('.tax-rate-input').value = item.tax_rate;
                            calculateRow(row);
                        });

                        if (skippedItems.length > 0) {
                            Swal.fire({
                                icon: 'warning',
                                title: '{{ __("messages.items_skipped") ?? "Items Skipped" }}',
                                html: '{{ __("messages.following_items_out_of_stock") ?? "The following items were skipped due to zero stock:" }}<br><br>' + 
                                      '<ul class="text-start">' + skippedItems.map(name => `<li>${name}</li>`).join('') + '</ul>',
                                confirmButtonText: '{{ __("messages.ok") ?? "OK" }}'
                            });
                        }
                    });
            }

            // 5. Event Listeners Initialization
            initSelect2();
            $(branchSelect).on('change', filterWarehouses);
            if (branchSelect.value) filterWarehouses();

            document.getElementById('add-item-btn').addEventListener('click', addItem);
            if (tableBody.children.length === 0) addItem();

            // 6. Payment Terms & Form Validation
            $('#payment_terms').on('change', function() {
                if ($(this).val() === 'credit' && !$('#customer_id').val()) {
                    Swal.fire({
                        icon: 'warning',
                        title: '{{ __("messages.info") }}',
                        text: '{{ __("messages.please_select_customer_first") }}',
                        confirmButtonText: '{{ __("messages.ok") ?? "OK" }}'
                    });
                    $(this).val('cash').trigger('change');
                }
            });

            document.getElementById('invoice-form').addEventListener('submit', function(e) {
                const paymentTerms = $('#payment_terms').val();
                const customerId = $('#customer_id').val();
                
                if (paymentTerms === 'credit' && !customerId) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: '{{ __("messages.info") }}',
                        text: '{{ __("messages.please_select_customer_first") }}',
                        confirmButtonText: '{{ __("messages.ok") ?? "OK" }}'
                    });
                }
            });

            // Prevent form submission on Enter and move to next field
            document.getElementById('invoice-form').addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                    // Don't prevent if it's a button (let them click via Enter if they want)
                    if (e.target.tagName === 'BUTTON' || e.target.type === 'submit') return;
                    
                    // Specific handling for product search input to avoid double processing
                    if (e.target.classList.contains('product-search-input')) {
                        const results = e.target.closest('td').querySelector('.product-results');
                        if (results && results.style.display !== 'none') {
                            const active = results.querySelector('.search-result-item.active');
                            if (active) {
                                e.preventDefault();
                                active.click();
                                return;
                            }
                        }
                    }

                    // Special case: Last field of an item row (Discount) adds a new row
                    if (e.target.classList.contains('discount-input')) {
                        e.preventDefault();
                        const trs = tableBody.querySelectorAll('tr');
                        const currentTr = e.target.closest('tr');
                        // Only add row if we are on the last row
                        if (currentTr === trs[trs.length - 1]) {
                            addItem();
                            // After adding row, move focus to the new search input
                            setTimeout(() => {
                                const newRow = tableBody.lastElementChild;
                                if (newRow) moveFocus(e.target);
                            }, 50);
                        } else {
                            moveFocus(e.target);
                        }
                        return;
                    }

                    e.preventDefault();
                    
                    // Logic to handle Select2 states
                    const isSelect2Search = e.target.classList.contains('select2-search__field');
                    
                    if (isSelect2Search) {
                        // Dropdown is open, let Select2 handle the Enter key for selection.
                        // Our select2:select listener will then move focus.
                        return;
                    }

                    // For Select2 containers or standard inputs, move focus forward
                    const targetEl = e.target.closest('.select2-container') ? e.target.closest('.select2-container').previousElementSibling : e.target;
                    moveFocus(targetEl);
                }
            });

            // Initial focus on first editable field
            setTimeout(() => {
                const firstField = document.getElementById('invoice_date');
                if (firstField) firstField.focus();
            }, 500);

            tableBody.addEventListener('click', function (e) {
                if (e.target.closest('.remove-item')) {
                    if (tableBody.querySelectorAll('tr').length > 1) {
                        e.target.closest('tr').remove();
                        calculateTotals();
                    }
                }
                
                const searchBtn = e.target.closest('.product-search-btn');
                if (searchBtn) {
                    e.preventDefault();
                    const input = searchBtn.closest('tr').querySelector('.product-search-input');
                    if (input) {
                        input.focus();
                        if (input.value.length > 0) input.dispatchEvent(new Event('input'));
                    }
                }
            });

            tableBody.addEventListener('input', function (e) {
                if (e.target.matches('.quantity-input, .price-input, .discount-input')) {
                    calculateRow(e.target.closest('tr'));
                }
            });

            document.addEventListener('click', function (e) {
                if (!e.target.closest('.position-relative')) {
                    document.querySelectorAll('.search-results-container').forEach(c => c.style.display = 'none');
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'F2') {
                    e.preventDefault();
                    const productInputs = document.querySelectorAll('.product-search-input');
                    const targetInput = Array.from(productInputs).find(i => !i.value) || productInputs[productInputs.length - 1];
                    if (targetInput) { targetInput.focus(); targetInput.select(); }
                }
            });

            // Removed redundant tableBody click listener
        });

    </script>

    {{-- Clear dynamic content before Turbo caches the page --}}
    <script>
        document.addEventListener('turbo:before-cache', function () {
            const tb = document.querySelector('#items-table tbody');
            if (tb) tb.innerHTML = '';
        });
    </script>
@endpush
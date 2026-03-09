@extends('layouts.app')

@section('title', __('messages.create_sales_order'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.create_sales_order') }}</h1>
            <a href="{{ route('sales.sales-orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
            </a>
        </div>

        <form action="{{ route('sales.sales-orders.store') }}" method="POST" id="salesOrderForm">
            @csrf
            <div class="card mb-4 glassy">
                <div class="card-body">
                    <div class="row">
                        <!-- Header Information -->
                        <div class="col-md-4 mb-3">
                            <label for="document_number" class="form-label fw-bold">{{ __('messages.document_number') }}
                                <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-white @error('document_number') is-invalid @enderror"
                                id="document_number" name="document_number"
                                value="{{ old('document_number', $document_number) }}" required readonly>
                            @error('document_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="order_number" class="form-label fw-bold">{{ __('messages.order_number') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-white @error('order_number') is-invalid @enderror"
                                id="order_number" name="order_number"
                                value="{{ old('order_number', 'SO-' . date('Y') . '-' . str_pad(\App\Models\SalesOrder::count() + 1, 5, '0', STR_PAD_LEFT)) }}"
                                required>
                            @error('order_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="order_date" class="form-label fw-bold">{{ __('messages.date') }} <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control bg-white @error('order_date') is-invalid @enderror"
                                id="order_date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required>
                            @error('order_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="expected_delivery_date"
                                class="form-label fw-bold">{{ __('messages.expected_delivery_date') }}</label>
                            <input type="date"
                                class="form-control bg-white @error('expected_delivery_date') is-invalid @enderror"
                                id="expected_delivery_date" name="expected_delivery_date"
                                value="{{ old('expected_delivery_date', date('Y-m-d', strtotime('+7 days'))) }}">
                            @error('expected_delivery_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="customer_search" class="form-label fw-bold">{{ __('messages.customer') }} <span
                                    class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="text" id="customer_search"
                                    class="form-control bg-white @error('customer_id') is-invalid @enderror"
                                    placeholder="{{ __('messages.select_customer') }}" autocomplete="off"
                                    value="{{ $selectedQuotation ? ($selectedQuotation->customer->name_en ?? $selectedQuotation->customer->name_ar) : old('customer_search') }}">
                                <input type="hidden" name="customer_id" id="customer_id"
                                    value="{{ $selectedQuotation ? $selectedQuotation->customer_id : old('customer_id') }}">
                                <div id="customer-results" class="search-results-container glassy"
                                    style="display: none; position: absolute; z-index: 1000; width: 100%;"></div>
                            </div>
                            @error('customer_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="branch_id" class="form-label fw-bold">{{ __('messages.branch') }} <span
                                    class="text-danger">*</span></label>
                            <select class="form-select bg-white @error('branch_id') is-invalid @enderror" id="branch_id"
                                name="branch_id" required>
                                <option value="">{{ __('messages.select_branch') }}</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ (old('branch_id', $selectedQuotation ? $selectedQuotation->branch_id : '')) == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name_en }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="warehouse_id" class="form-label fw-bold">{{ __('messages.warehouse') }} <span
                                    class="text-danger">*</span></label>
                            <select class="form-select bg-white @error('warehouse_id') is-invalid @enderror"
                                id="warehouse_id" name="warehouse_id" required>
                                <option value="">{{ __('messages.select_warehouse') }}</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ (old('warehouse_id', $selectedQuotation ? $selectedQuotation->warehouse_id : '')) == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name_en }}
                                    </option>
                                @endforeach
                            </select>
                            @error('warehouse_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="salesman_id" class="form-label fw-bold">{{ __('messages.salesman') }}</label>
                            <select class="form-select bg-white @error('salesman_id') is-invalid @enderror" id="salesman_id"
                                name="salesman_id">
                                <option value="">{{ __('messages.select_salesman') }}</option>
                                @foreach(\App\Models\User::active()->get() as $salesman)
                                    <option value="{{ $salesman->id }}" {{ (old('salesman_id', $selectedQuotation ? $selectedQuotation->salesman_id : '')) == $salesman->id ? 'selected' : '' }}>
                                        {{ $salesman->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('salesman_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="quotation_id" class="form-label fw-bold">{{ __('messages.quotation') }}</label>
                            <select class="form-select bg-white @error('quotation_id') is-invalid @enderror"
                                id="quotation_id" name="quotation_id">
                                <option value="">{{ __('messages.select_quotation') }}</option>
                                @foreach($quotations as $quotation)
                                    <option value="{{ $quotation->id }}" {{ (old('quotation_id', $selectedQuotation ? $selectedQuotation->id : '')) == $quotation->id ? 'selected' : '' }}>
                                        {{ $quotation->document_number }} ({{ $quotation->customer->name_en }})
                                    </option>
                                @endforeach
                            </select>
                            @error('quotation_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="card mb-4 glassy">
                <div
                    class="card-header d-flex justify-content-between align-items-center bg-transparent border-bottom-0 pt-3">
                    <h5 class="mb-0 fw-bold">{{ __('messages.items') }}</h5>
                    <button type="button" class="btn btn-sm btn-success" id="addItemBtn">
                        <i class="fas fa-plus"></i> {{ __('messages.add_item') }}
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="itemsTable">
                            <thead>
                                <tr class="bg-light">
                                    <th style="width: 45%">{{ __('messages.product') }}</th>
                                    <th style="width: 9%">{{ __('messages.quantity') }}</th>
                                    <th style="width: 11%">{{ __('messages.unit_price') }}</th>
                                    <th style="width: 8%">{{ __('messages.discount') }} (%)</th>
                                    <th style="width: 8%">{{ __('messages.tax') }} (%)</th>
                                    <th style="width: 8%">{{ __('messages.tax_amount') }}</th>
                                    <th style="width: 9%">{{ __('messages.total') }}</th>
                                    <th style="width: 2%"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <!-- Items will be added here via JS -->
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="6" class="text-end fw-bold">{{ __('messages.subtotal') }}:</td>
                                    <td>
                                        <input type="number"
                                            class="form-control form-control-sm text-end bg-transparent border-0 fw-bold"
                                            id="subtotal_display" readonly value="0.00">
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-end fw-bold">{{ __('messages.tax_amount') }}:</td>
                                    <td>
                                        <input type="number"
                                            class="form-control form-control-sm text-end bg-transparent border-0 fw-bold"
                                            id="tax_total_display" readonly value="0.00">
                                    </td>
                                    <td></td>
                                </tr>
                                <tr class="table-primary border-top">
                                    <td colspan="6" class="text-end fw-bold">{{ __('messages.grand_total') }}:</td>
                                    <td>
                                        <input type="number"
                                            class="form-control form-control-sm text-end bg-transparent border-0 fw-bold"
                                            id="grand_total_display" readonly value="0.00">
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @error('items')
                        <div class="alert alert-danger m-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="card mb-4 glassy">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="delivery_address"
                                class="form-label fw-bold">{{ __('messages.delivery_address') }}</label>
                            <textarea class="form-control bg-white" id="delivery_address" name="delivery_address"
                                rows="3">{{ old('delivery_address', $selectedQuotation ? $selectedQuotation->customer->address : '') }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="terms_conditions"
                                class="form-label fw-bold">{{ __('messages.terms_conditions') }}</label>
                            <textarea class="form-control bg-white" id="terms_conditions" name="terms_conditions"
                                rows="3">{{ old('terms_conditions', $selectedQuotation ? $selectedQuotation->terms_conditions : '') }}</textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="notes" class="form-label fw-bold">{{ __('messages.notes') }}</label>
                            <textarea class="form-control bg-white" id="notes" name="notes"
                                rows="2">{{ old('notes', $selectedQuotation ? $selectedQuotation->notes : '') }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">
                            <i class="fas fa-save me-2"></i> {{ __('messages.save_order') ?? 'Save Sales Order' }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('turbo:load', function () {
                const addItemBtn = document.getElementById('addItemBtn');
                const itemsBody = document.getElementById('itemsBody');
                let itemIndex = 0;

                const customers = @json($customers->map(function ($c) {
                    return ['id' => $c->id, 'name' => $c->name_en, 'code' => $c->code];
                }));
                const taxSetting = @json($taxSetting);

                // Customer Search Logic
                const customerSearch = document.getElementById('customer_search');
                const customerId = document.getElementById('customer_id');
                const customerResults = document.getElementById('customer-results');
                let customerHighlightIndex = -1;

                customerSearch.addEventListener('input', function () {
                    customerId.value = '';
                    performCustomerSearch(this.value);
                });

                customerSearch.addEventListener('focus', function () {
                    performCustomerSearch(this.value);
                });

                customerSearch.addEventListener('keydown', function (e) {
                    const items = customerResults.querySelectorAll('.search-result-item');
                    if (!items.length) return;

                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        customerHighlightIndex = (customerHighlightIndex + 1) % items.length;
                        updateHighlight(items, customerHighlightIndex);
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        customerHighlightIndex = (customerHighlightIndex - 1 + items.length) % items.length;
                        updateHighlight(items, customerHighlightIndex);
                    } else if (e.key === 'Enter' && customerHighlightIndex > -1) {
                        e.preventDefault();
                        items[customerHighlightIndex].click();
                    }
                });

                function performCustomerSearch(query) {
                    const results = customers.filter(c =>
                        c.name.toLowerCase().includes(query.toLowerCase()) ||
                        (c.code && c.code.toLowerCase().includes(query.toLowerCase()))
                    ).slice(0, 10);

                    customerHighlightIndex = -1;
                    if (results.length > 0) {
                        customerResults.innerHTML = results.map(c => `
                                                                        <div class="search-result-item p-2 border-bottom" data-id="${c.id}" data-name="${c.name}" style="cursor: pointer;">
                                                                            <div class="fw-bold">${c.name}</div>
                                                                            <small class="text-muted">${c.code || ''}</small>
                                                                        </div>
                                                                    `).join('');
                        customerResults.style.display = 'block';
                    } else {
                        customerResults.innerHTML = '<div class="p-2 text-muted">No customer found</div>';
                        customerResults.style.display = 'block';
                    }
                }

                function updateHighlight(items, index) {
                    items.forEach((item, i) => {
                        if (i === index) {
                            item.classList.add('bg-primary', 'text-white');
                        } else {
                            item.classList.remove('bg-primary', 'text-white');
                        }
                    });
                }

                customerResults.addEventListener('click', function (e) {
                    const item = e.target.closest('.search-result-item');
                    if (item && item.dataset.id) {
                        customerSearch.value = item.dataset.name;
                        customerId.value = item.dataset.id;
                        customerResults.style.display = 'none';
                    }
                });

                document.addEventListener('click', function (e) {
                    if (!customerSearch.contains(e.target) && !customerResults.contains(e.target)) {
                        customerResults.style.display = 'none';
                    }
                });

                // Items Logic
                function addItem(data = null) {
                    const index = itemIndex++;
                    const tr = document.createElement('tr');
                    tr.classList.add('item-row');

                    const productId = data ? data.product_id : '';
                    const productName = data && data.product ? (data.product.name_en || data.product.name_ar) : '';
                    const taxRate = data ? data.tax_rate : (taxSetting ? taxSetting.default_tax_rate : 0);

                    tr.innerHTML = `
                                                                    <td>
                                                                        <div class="position-relative product-search-container">
                                                                            <input type="text" class="form-control form-control-sm bg-white product-search-input" 
                                                                                placeholder="{{ __('messages.select_product') }}" autocomplete="off" value="${productName}" required>
                                                                            <input type="hidden" name="items[${index}][product_id]" class="product-id-input" value="${productId}" required>
                                                                            <div class="product-results search-results-container glassy" style="display: none; position: absolute; z-index: 1000; width: 100%;"></div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" step="0.001" class="form-control form-control-sm bg-white quantity-input" name="items[${index}][quantity]" value="${data ? data.quantity : 1}" required min="0.001">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" step="0.01" class="form-control form-control-sm bg-white price-input" name="items[${index}][unit_price]" value="${data ? data.unit_price : 0}" required min="0">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" step="0.01" class="form-control form-control-sm bg-white discount-input" name="items[${index}][discount_percentage]" value="${data ? (data.discount_percentage || 0) : 0}" min="0" max="100">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" step="0.01" class="form-control form-control-sm bg-light tax-rate-input" name="items[${index}][tax_rate]" value="${taxRate}" readonly tabindex="-1">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" step="0.01" class="form-control form-control-sm bg-light tax-amount-input" value="0.00" readonly tabindex="-1">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" step="0.01" class="form-control form-control-sm bg-light row-total-input" value="0.00" readonly tabindex="-1">
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <button type="button" class="btn btn-sm btn-link text-danger remove-item p-0">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </td>
                                                                `;

                    tr.dataset.availableStock = data ? (data.available_quantity || 0) : 0;
                    itemsBody.appendChild(tr);

                    const searchInput = tr.querySelector('.product-search-input');
                    const idInput = tr.querySelector('.product-id-input');
                    const resultsDiv = tr.querySelector('.product-results');
                    const qtyInput = tr.querySelector('.quantity-input');
                    const priceInput = tr.querySelector('.price-input');
                    const discountInput = tr.querySelector('.discount-input');
                    let productHighlightIndex = -1;

                    searchInput.addEventListener('input', function () {
                        idInput.value = '';
                        performProductSearch(this.value, resultsDiv, idInput, searchInput, tr);
                    });

                    searchInput.addEventListener('focus', function () {
                        performProductSearch(this.value, resultsDiv, idInput, searchInput, tr);
                    });

                    searchInput.addEventListener('keydown', function (e) {
                        const items = resultsDiv.querySelectorAll('.search-result-item');
                        if (!items.length) return;

                        if (e.key === 'ArrowDown') {
                            e.preventDefault();
                            productHighlightIndex = (productHighlightIndex + 1) % items.length;
                            updateHighlight(items, productHighlightIndex);
                        } else if (e.key === 'ArrowUp') {
                            e.preventDefault();
                            productHighlightIndex = (productHighlightIndex - 1 + items.length) % items.length;
                            updateHighlight(items, productHighlightIndex);
                        } else if (e.key === 'Enter' && productHighlightIndex > -1) {
                            e.preventDefault();
                            items[productHighlightIndex].click();
                        }
                    });

                    [qtyInput, priceInput, discountInput].forEach(input => {
                        input.addEventListener('input', () => calculateRow(tr));
                    });

                    tr.querySelector('.remove-item').addEventListener('click', function () {
                        tr.remove();
                        if (itemsBody.children.length === 0) addItem();
                        calculateTotals();
                    });

                    if (data) calculateRow(tr);
                }

                function performProductSearch(query, resultsDiv, idInput, searchInput, tr) {
                    const warehouseId = document.getElementById('warehouse_id').value;
                    const branchId = document.getElementById('branch_id').value;

                    if (!warehouseId) {
                        Swal.fire({
                            icon: 'warning',
                            title: '{{ __("messages.select_warehouse_first") ?? "Select Warehouse First" }}',
                            text: '{{ __("messages.please_select_warehouse_before_searching") ?? "Please select a warehouse before searching for items." }}',
                            confirmButtonText: '{{ __("messages.ok") ?? "OK" }}'
                        });
                        searchInput.value = '';
                        resultsDiv.style.display = 'none';
                        return;
                    }

                    if (!branchId) {
                        resultsDiv.innerHTML = '<div class="p-2 text-danger">Please select branch first</div>';
                        resultsDiv.style.display = 'block';
                        return;
                    }

                    fetch(`{{ route('inventory.products.ajax-search') }}?q=${encodeURIComponent(query)}&warehouse_id=${warehouseId}&branch_id=${branchId}`)
                        .then(response => response.json())
                        .then(results => {
                            if (results.length > 0) {
                                const currentLocale = '{{ app()->getLocale() }}';
                                resultsDiv.innerHTML = results.map(p => {
                                    const currentName = currentLocale === 'ar' ? p.name_ar || p.name_en : p.name_en || p.name_ar;
                                    const stockColor = (p.available_quantity > 0) ? 'text-success' : 'text-danger';
                                    return `
                                                                                    <div class="search-result-item p-2 border-bottom" 
                                                                                        data-id="${p.id}" 
                                                                                        data-name="${currentName}" 
                                                                                        data-price="${p.sale_price}" 
                                                                                        data-tax="${p.tax_rate || 0}"
                                                                                        data-stock="${p.available_quantity || 0}"
                                                                                        style="cursor: pointer;">
                                                                                        <div class="d-flex justify-content-between align-items-start w-100">
                                                                                            <div class="result-content pe-3 d-flex flex-column gap-1 flex-grow-1">
                                                                                                <div class="fw-bold d-flex align-items-center gap-2 flex-wrap">
                                                                                                    ${p.code ? `<span style="background:#e9f0ff;color:#3d6bc7;font-size:0.7rem;font-weight:700;padding:1px 7px;border-radius:10px;flex-shrink:0;">${p.code}</span>` : ''}
                                                                                                    <span>${currentName}</span>
                                                                                                </div>
                                                                                                <small class="${stockColor} fw-bold d-block"><i class="fas fa-boxes" style="font-size:0.65rem;"></i> {{ __('messages.stock') }}: ${parseFloat(p.available_quantity || 0).toFixed(2)}</small>
                                                                                            </div>
                                                                                            <div class="d-flex flex-column align-items-end gap-1 flex-shrink-0 ms-auto small text-nowrap">
                                                                                                <span style="color:#198754; font-weight:600;" title="Sale Price">{{ __('messages.sale_price') }}: ${parseFloat(p.sale_price || 0).toFixed(2)}</span>
                                                                                                <span style="color:#6c757d; font-weight:600;" title="Cost Price">{{ __('messages.cost_price') }}: ${parseFloat(p.cost_price || 0).toFixed(2)}</span>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                `;
                                }).join('');
                                resultsDiv.style.display = 'block';

                                resultsDiv.querySelectorAll('.search-result-item').forEach(item => {
                                    item.addEventListener('click', function () {
                                        const stock = parseFloat(this.dataset.stock) || 0;
                                        if (stock <= 0) {
                                            Swal.fire({
                                                icon: 'warning',
                                                title: 'Out of Stock',
                                                text: 'This product is currently unavailable in the selected warehouse.',
                                                confirmButtonColor: '#3085d6'
                                            });
                                            // Still allow selection but with warning? Or prevent?
                                            // For now, let's allow it but keep search open unless they confirm.
                                            // Actually, better to prevent adding if zero stock for a Sales Order.
                                            resultsDiv.style.display = 'none';
                                            searchInput.value = '';
                                            return;
                                        }

                                        searchInput.value = this.dataset.name;
                                        idInput.value = this.dataset.id;
                                        tr.dataset.availableStock = this.dataset.stock;
                                        tr.querySelector('.price-input').value = this.dataset.price;
                                        tr.querySelector('.tax-rate-input').value = this.dataset.tax;
                                        resultsDiv.style.display = 'none';
                                        calculateRow(tr);
                                    });
                                });
                            } else {
                                resultsDiv.innerHTML = '<div class="p-2 text-muted">No product found</div>';
                                resultsDiv.style.display = 'block';
                            }
                        });
                }

                function calculateRow(tr) {
                    const qEl = tr.querySelector('.quantity-input');
                    const qty = parseFloat(qEl.val || qEl.value) || 0;
                    const availableStock = parseFloat(tr.dataset.availableStock) || 0;

                    if (qty > availableStock && availableStock > 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: '{{ __("messages.stock_shortage") ?? "Stock Shortage" }}',
                            text: `{{ __('messages.quantity_exceeds_available_stock') ?? 'Quantity exceeds available stock' }} (${availableStock})`,
                            confirmButtonText: '{{ __("messages.ok") ?? "OK" }}'
                        });
                        qEl.value = availableStock;
                        return calculateRow(tr);
                    }

                    const price = parseFloat(tr.querySelector('.price-input').value) || 0;
                    const discPerc = parseFloat(tr.querySelector('.discount-input').value) || 0;
                    const taxRate = parseFloat(tr.querySelector('.tax-rate-input').value) || 0;

                    // Logic: Price is inclusive of tax
                    const grossAmount = (qty * price) * (1 - discPerc / 100);
                    const netAmount = grossAmount / (1 + taxRate / 100);
                    const taxAmount = grossAmount - netAmount;

                    tr.querySelector('.tax-amount-input').value = taxAmount.toFixed(2);
                    tr.querySelector('.row-total-input').value = grossAmount.toFixed(2);

                    calculateTotals();
                }

                function calculateTotals() {
                    let subtotal = 0;
                    let taxTotal = 0;
                    let grandTotal = 0;

                    document.querySelectorAll('.item-row').forEach(row => {
                        const rowTotal = parseFloat(row.querySelector('.row-total-input').value) || 0;
                        const tax = parseFloat(row.querySelector('.tax-amount-input').value) || 0;

                        grandTotal += rowTotal;
                        taxTotal += tax;
                        subtotal += (rowTotal - tax);
                    });

                    document.getElementById('subtotal_display').value = subtotal.toFixed(2);
                    document.getElementById('tax_total_display').value = taxTotal.toFixed(2);
                    document.getElementById('grand_total_display').value = grandTotal.toFixed(2);
                }

                addItemBtn.addEventListener('click', () => addItem());

                // Initial row or from Old Data/Quotation
                @if($selectedQuotation)
                    @foreach($selectedQuotation->items as $item)
                        addItem({
                            product_id: "{{ $item->product_id }}",
                            product: @json($item->product),
                            quantity: "{{ $item->quantity }}",
                            unit_price: "{{ $item->unit_price }}",
                            tax_rate: "{{ $item->tax_rate }}",
                            discount_percentage: "{{ $item->discount_percentage }}"
                        });
                    @endforeach
                @elseif(old('items'))
                    @foreach(old('items') as $index => $item)
                        @php 
                            $prod = \App\Models\Product::find($item['product_id']);
                        @endphp
                        addItem({
                            product_id: "{{ $item['product_id'] }}",
                            product: @json($prod),
                            quantity: "{{ $item['quantity'] }}",
                            unit_price: "{{ $item['unit_price'] }}",
                            tax_rate: "{{ $prod->tax_rate ?? 0 }}",
                            discount_percentage: "{{ $item['discount_percentage'] ?? 0 }}"
                        });
                    @endforeach
                @else
                    addItem();
                @endif
                                                        });
        </script>
    @endpush

    <style>
        .search-results-container {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-top: none;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .search-result-item:hover {
            background-color: #f8f9fa;
        }

        .glassy {
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        }
    </style>
@endsection
@extends('layouts.app')

@section('title', __('messages.create_quotation'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.create_quotation') }}</h1>
            <a href="{{ route('sales.quotations.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
            </a>
        </div>

        <form action="{{ route('sales.quotations.store') }}" method="POST" id="quotationForm">
            @csrf
            <div class="card mb-4 glassy">
                <div class="card-body">
                    <div class="row">
                        <!-- Header Information -->
                        <div class="col-md-4 mb-3">
                            <label for="document_number" class="form-label fw-bold">{{ __('messages.quotation_number') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-white @error('document_number') is-invalid @enderror"
                                id="document_number" name="document_number" value="{{ old('document_number', $document_number) }}" required readonly>
                            @error('document_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="quotation_date" class="form-label fw-bold">{{ __('messages.date') }} <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control bg-white @error('quotation_date') is-invalid @enderror"
                                id="quotation_date" name="quotation_date" value="{{ old('quotation_date', date('Y-m-d')) }}"
                                required>
                            @error('quotation_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="expiry_date" class="form-label fw-bold">{{ __('messages.expiry_date') }}</label>
                            <input type="date" class="form-control bg-white @error('expiry_date') is-invalid @enderror"
                                id="expiry_date" name="expiry_date"
                                value="{{ old('expiry_date', date('Y-m-d', strtotime('+30 days'))) }}">
                            @error('expiry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="customer_search" class="form-label fw-bold">{{ __('messages.customer') }} <span
                                    class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="text" id="customer_search"
                                    class="form-control bg-white @error('customer_id') is-invalid @enderror"
                                    placeholder="{{ __('messages.select_customer') }}" autocomplete="off">
                                <input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id') }}">
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
                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name_en }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="warehouse_id" class="form-label fw-bold">{{ __('messages.warehouse') }}</label>
                            <select class="form-select bg-white @error('warehouse_id') is-invalid @enderror" id="warehouse_id"
                                name="warehouse_id">
                                <option value="">{{ __('messages.select_warehouse') }}</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
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
                                @foreach($salesmen as $salesman)
                                    <option value="{{ $salesman->id }}" {{ old('salesman_id') == $salesman->id ? 'selected' : '' }}>
                                        {{ $salesman->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('salesman_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label fw-bold">{{ __('messages.status') }} <span
                                    class="text-danger">*</span></label>
                            <select class="form-select bg-white @error('status') is-invalid @enderror" id="status" name="status"
                                required>
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>
                                    {{ __('messages.draft') }}</option>
                                <option value="sent" {{ old('status') == 'sent' ? 'selected' : '' }}>{{ __('messages.sent') }}
                                </option>
                                <option value="accepted" {{ old('status') == 'accepted' ? 'selected' : '' }}>
                                    {{ __('messages.accepted') }}</option>
                                <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>
                                    {{ __('messages.rejected') }}</option>
                                <option value="expired" {{ old('status') == 'expired' ? 'selected' : '' }}>
                                    {{ __('messages.expired') }}</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="card mb-4 glassy">
                <div class="card-header d-flex justify-content-between align-items-center bg-transparent border-bottom-0 pt-3">
                    <h5 class="mb-0 fw-bold">{{ __('messages.items') }}</h5>
                    <button type="button" class="btn btn-sm btn-success" id="addItemBtn">
                        <i class="fas fa-plus"></i> {{ __('messages.add_item') }}
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="">
                        <table class="table table-hover mb-0" id="itemsTable">
                            <thead>
                                <tr class="bg-light">
                                    <th style="width: 35%">{{ __('messages.product') }}</th>
                                    <th style="width: 10%">{{ __('messages.quantity') }}</th>
                                    <th style="width: 15%">{{ __('messages.unit_price') }}</th>
                                    <th style="width: 10%">{{ __('messages.tax') }} (%)</th>
                                    <th style="width: 12%">{{ __('messages.tax_amount') }}</th>
                                    <th style="width: 13%">{{ __('messages.total') }}</th>
                                    <th style="width: 5%"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <!-- Items will be added here via JS -->
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="5" class="text-end fw-bold">{{ __('messages.subtotal') }}:</td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm text-end bg-transparent border-0 fw-bold"
                                            name="subtotal" id="subtotal" readonly value="0.00">
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end fw-bold">{{ __('messages.tax_amount') }}:</td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm text-end bg-transparent border-0 fw-bold"
                                            name="tax_amount" id="tax_total" readonly value="0.00">
                                    </td>
                                    <td></td>
                                </tr>
                                <tr class="table-primary border-top">
                                    <td colspan="5" class="text-end fw-bold">{{ __('messages.grand_total') }}:</td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm text-end bg-transparent border-0 fw-bold"
                                            name="total_amount" id="grand_total" readonly value="0.00">
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
                            <label for="terms_conditions" class="form-label fw-bold">{{ __('messages.terms_conditions') }}</label>
                            <textarea class="form-control bg-white" id="terms_conditions" name="terms_conditions"
                                rows="3">{{ old('terms_conditions') }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="notes" class="form-label fw-bold">{{ __('messages.notes') }}</label>
                            <textarea class="form-control bg-white" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-save me-1"></i> {{ __('messages.save') }}
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

            const products = @json($products);
            const customers = @json($customers->map(function($c) {
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
                    e.stopImmediatePropagation();
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
                        const container = item.closest('.search-results-container');
                        const itemTop = item.offsetTop;
                        const itemBottom = itemTop + item.offsetHeight;
                        const containerTop = container.scrollTop;
                        const containerBottom = containerTop + container.offsetHeight;

                        if (itemTop < containerTop) {
                            container.scrollTop = itemTop;
                        } else if (itemBottom > containerBottom) {
                            container.scrollTop = itemBottom - container.offsetHeight;
                        }
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
                    customerHighlightIndex = -1;
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

                const selectedProduct = data ? products.find(p => p.id == data.product_id) : null;
                const productName = selectedProduct ? selectedProduct.name_en : '';
                const productId = data ? data.product_id : '';
                const taxRate = taxSetting.tax_enabled ? taxSetting.default_tax_rate : 0;

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
                        <input type="number" step="0.01" class="form-control form-control-sm bg-light tax-rate-input" name="items[${index}][tax_rate]" value="${taxRate}" readonly tabindex="-1">
                    </td>
                    <td>
                        <input type="number" step="0.01" class="form-control form-control-sm bg-light tax-amount-input" name="items[${index}][tax_amount]" value="0.00" readonly tabindex="-1">
                    </td>
                    <td>
                        <input type="number" step="0.01" class="form-control form-control-sm bg-light row-total-input" name="items[${index}][net_amount]" value="0.00" readonly tabindex="-1">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-link text-danger remove-item p-0">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;

                itemsBody.appendChild(tr);
                if (window.initGlobalSelect2) window.initGlobalSelect2(tr);

                const searchInput = tr.querySelector('.product-search-input');
                const idInput = tr.querySelector('.product-id-input');
                const resultsDiv = tr.querySelector('.product-results');
                const qtyInput = tr.querySelector('.quantity-input');
                const priceInput = tr.querySelector('.price-input');
                let productHighlightIndex = -1;

                searchInput.addEventListener('input', function () {
                    idInput.value = '';
                    productHighlightIndex = -1;
                    performProductSearch(this.value, resultsDiv, idInput, searchInput, tr);
                });

                searchInput.addEventListener('focus', function () {
                    productHighlightIndex = -1;
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
                        e.stopImmediatePropagation();
                        items[productHighlightIndex].click();
                        productHighlightIndex = -1;
                    }
                });

                [qtyInput, priceInput].forEach(input => {
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
                const results = products.filter(p =>
                    p.name_en.toLowerCase().includes(query.toLowerCase()) ||
                    (p.code && p.code.toLowerCase().includes(query.toLowerCase()))
                ).slice(0, 10);

                if (results.length > 0) {
                    resultsDiv.innerHTML = results.map(p => `
                        <div class="search-result-item p-2 border-bottom" data-id="${p.id}" data-name="${p.name_en}" data-price="${p.sale_price}" style="cursor: pointer;">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="fw-bold">${p.name_en}</div>
                                <div class="d-flex gap-1 flex-shrink-0 ms-2">
                                    <span class="badge" style="background-color:#dc3545; color:white;" title="Cost">${parseFloat(p.cost_price || 0).toFixed(2)}</span>
                                    <span class="badge" style="background-color:#198754; color:white;" title="Price">${parseFloat(p.sale_price || 0).toFixed(2)}</span>
                                </div>
                            </div>
                            <small class="text-muted">${p.product_code || ''}</small>
                        </div>
                    `).join('');
                    resultsDiv.style.display = 'block';

                    resultsDiv.querySelectorAll('.search-result-item').forEach(item => {
                        item.addEventListener('click', function () {
                            searchInput.value = this.dataset.name;
                            idInput.value = this.dataset.id;
                            tr.querySelector('.price-input').value = this.dataset.price;
                            resultsDiv.style.display = 'none';
                            calculateRow(tr);
                        });
                    });
                } else {
                    resultsDiv.innerHTML = '<div class="p-2 text-muted">No product found</div>';
                    resultsDiv.style.display = 'block';
                }
            }

            function calculateRow(tr) {
                const qty = parseFloat(tr.querySelector('.quantity-input').value) || 0;
                const price = parseFloat(tr.querySelector('.price-input').value) || 0;
                const taxRate = parseFloat(tr.querySelector('.tax-rate-input').value) || 0;

                // Inclusive Logic
                const inclusiveTotal = qty * price;
                const taxAmount = inclusiveTotal - (inclusiveTotal / (1 + (taxRate / 100)));
                const netAmount = inclusiveTotal - taxAmount;

                tr.querySelector('.tax-amount-input').value = taxAmount.toFixed(2);
                tr.querySelector('.row-total-input').value = inclusiveTotal.toFixed(2);

                calculateTotals();
            }

            function calculateTotals() {
                let subtotal = 0;
                let taxTotal = 0;
                let grandTotal = 0;

                document.querySelectorAll('.item-row').forEach(row => {
                    const inclusiveRowTotal = parseFloat(row.querySelector('.row-total-input').value) || 0;
                    const tax = parseFloat(row.querySelector('.tax-amount-input').value) || 0;

                    grandTotal += inclusiveRowTotal;
                    taxTotal += tax;
                    subtotal += (inclusiveRowTotal - tax);
                });

                document.getElementById('subtotal').value = subtotal.toFixed(2);
                document.getElementById('tax_total').value = taxTotal.toFixed(2);
                document.getElementById('grand_total').value = grandTotal.toFixed(2);
            }

            // Use named function to avoid stacking listeners on turbo:load
            addItemBtn.removeEventListener('click', addItemBtn._addItemHandler);
            addItemBtn._addItemHandler = () => addItem();
            addItemBtn.addEventListener('click', addItemBtn._addItemHandler);

            // Add initial row only if table is empty
            @if(old('items'))
                @foreach(old('items') as $item)
                    addItem(@json($item));
                @endforeach
            @else
                if (itemsBody.children.length === 0) addItem();
            @endif
        });

        // Clear dynamic content before Turbo caches the page
        document.addEventListener('turbo:before-cache', function () {
            const tb = document.getElementById('itemsBody');
            if (tb) tb.innerHTML = '';
        });

        // Enter key to next field navigation
        document.getElementById('quotationForm').addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA' && e.target.type !== 'submit') {
                // Skip if it was handled by the search dropdowns
                if (e.defaultPrevented) return;
                
                e.preventDefault();

                const form = this;
                const index = Array.prototype.indexOf.call(form.elements, e.target);

                // Find next visible and enabled focusable element
                for (let i = index + 1; i < form.elements.length; i++) {
                    const nextElement = form.elements[i];
                    if (nextElement.tabIndex > -1 &&
                        !nextElement.disabled &&
                        nextElement.type !== 'hidden' &&
                        nextElement.offsetParent !== null) {
                        nextElement.focus();
                        if (nextElement.tagName === 'INPUT') nextElement.select();
                        break;
                    }
                }
            }
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
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .search-result-item:hover, .search-result-item.active {
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

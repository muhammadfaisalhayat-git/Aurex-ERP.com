@extends('layouts.app')

@section('title', __('messages.create_customer_request') ?? 'Create Customer Request')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.create_customer_request') ?? 'Create Customer Request' }}</h1>
            <a href="{{ route('sales.customer-requests.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
            </a>
        </div>

        <form action="{{ route('sales.customer-requests.store') }}" method="POST" id="customerRequestForm">
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
                            <label for="request_date" class="form-label fw-bold">{{ __('messages.date') }} <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control bg-white @error('request_date') is-invalid @enderror"
                                id="request_date" name="request_date" value="{{ old('request_date', date('Y-m-d')) }}"
                                required>
                            @error('request_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="needed_date"
                                class="form-label fw-bold">{{ __('messages.needed_date') ?? 'Needed By' }}</label>
                            <input type="date" class="form-control bg-white @error('needed_date') is-invalid @enderror"
                                id="needed_date" name="needed_date" value="{{ old('needed_date') }}">
                            @error('needed_date')
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
                                    style="display: none; position: absolute;"></div>
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
                            <label for="status" class="form-label fw-bold">{{ __('messages.status') }} <span
                                    class="text-danger">*</span></label>
                            <select class="form-select bg-white @error('status') is-invalid @enderror" id="status"
                                name="status" required>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>
                                    {{ __('messages.pending') ?? 'Pending' }}
                                </option>
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>
                                    {{ __('messages.draft') }}
                                </option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>
                                    {{ __('messages.cancelled') ?? 'Cancelled' }}
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="notes" class="form-label fw-bold">{{ __('messages.notes') }}</label>
                            <textarea class="form-control bg-white" id="notes" name="notes"
                                rows="2">{{ old('notes') }}</textarea>
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
                    <div class="">
                        <table class="table table-hover mb-0" id="itemsTable">
                            <thead>
                                <tr class="bg-light">
                                    <th style="width: 35%">{{ __('messages.product') }}</th>
                                    <th style="width: 10%">{{ __('messages.quantity') }}</th>
                                    <th style="width: 12%">{{ __('messages.unit_price') ?? 'Unit Price' }}</th>
                                    <th style="width: 8%">{{ __('messages.tax') ?? 'Tax' }} %</th>
                                    <th style="width: 10%">{{ __('messages.tax_amount') ?? 'Tax Amt' }}</th>
                                    <th style="width: 10%">{{ __('messages.total') ?? 'Total' }}</th>
                                    <th style="width: 10%">{{ __('messages.notes') }}</th>
                                    <th style="width: 5%"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <!-- Items will be added here via JS -->
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="5" class="text-end fw-bold">{{ __('messages.subtotal') ?? 'Subtotal' }}:
                                    </td>
                                    <td class="fw-bold" id="grand_subtotal">0.00</td>
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end fw-bold">
                                        {{ __('messages.tax_amount') ?? 'Tax Amount' }}:</td>
                                    <td class="fw-bold" id="grand_tax">0.00</td>
                                    <td colspan="2"></td>
                                </tr>
                                <tr class="table-primary">
                                    <td colspan="5" class="text-end fw-bold">
                                        {{ __('messages.total_amount') ?? 'Total Amount' }}:</td>
                                    <td class="fw-bold" id="grand_total">0.00</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @error('items')
                        <div class="alert alert-danger m-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end mb-4">
                <button type="submit" class="btn btn-primary px-4 py-2">
                    <i class="fas fa-save me-1"></i> {{ __('messages.save') }}
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const addItemBtn = document.getElementById('addItemBtn');
                const itemsBody = document.getElementById('itemsBody');
                let itemIndex = 0;

                const products = @json($products);
                const taxSetting = @json($taxSetting);
                const customers = @json($customers->map(function ($c) {
                    return ['id' => $c->id, 'name' => $c->name_en, 'code' => $c->code];
                }));

                // Customer Search Logic
                // ... (keeping existing search logic)
                const customerSearch = document.getElementById('customer_search');
                const customerId = document.getElementById('customer_id');
                const customerResults = document.getElementById('customer-results');

                customerSearch.addEventListener('input', function () {
                    customerId.value = '';
                    performCustomerSearch(this.value);
                });

                customerSearch.addEventListener('focus', function () {
                    performCustomerSearch(this.value);
                });

                function performCustomerSearch(query) {
                    const results = customers.filter(c =>
                        c.name.toLowerCase().includes(query.toLowerCase()) ||
                        (c.code && c.code.toLowerCase().includes(query.toLowerCase()))
                    ).slice(0, 10);

                    if (results.length > 0) {
                        customerResults.innerHTML = results.map(c => `
                                    <div class="search-result-item p-2 border-bottom" data-id="${c.id}" style="cursor: pointer;">
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

                customerResults.addEventListener('click', function (e) {
                    const item = e.target.closest('.search-result-item');
                    if (item) {
                        customerSearch.value = item.querySelector('.fw-bold').textContent;
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
                                    <input type="text" class="form-control form-control-sm bg-light tax-rate-display" value="${taxRate}%" readonly tabindex="-1">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm bg-light tax-amount-display" value="0.00" readonly tabindex="-1">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm bg-light total-amount-display" value="0.00" readonly tabindex="-1">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm bg-white" name="items[${index}][notes]" value="${data ? data.notes || '' : ''}">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-link text-danger remove-item p-0">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            `;

                    itemsBody.appendChild(tr);

                    const searchInput = tr.querySelector('.product-search-input');
                    const idInput = tr.querySelector('.product-id-input');
                    const resultsDiv = tr.querySelector('.product-results');
                    const qtyInput = tr.querySelector('.quantity-input');
                    const priceInput = tr.querySelector('.price-input');

                    searchInput.addEventListener('input', function () {
                        idInput.value = '';
                        performProductSearch(this.value, resultsDiv, idInput, searchInput);
                    });

                    searchInput.addEventListener('focus', function () {
                        performProductSearch(this.value, resultsDiv, idInput, searchInput);
                    });

                    [qtyInput, priceInput].forEach(input => {
                        input.addEventListener('input', calculateTotals);
                    });

                    tr.querySelector('.remove-item').addEventListener('click', function () {
                        tr.remove();
                        if (itemsBody.children.length === 0) addItem();
                        calculateTotals();
                    });

                    calculateTotals();
                }

                function calculateTotals() {
                    let grandSubtotal = 0;
                    let grandTax = 0;

                    document.querySelectorAll('.item-row').forEach(row => {
                        const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
                        const price = parseFloat(row.querySelector('.price-input').value) || 0;
                        const taxRate = taxSetting.tax_enabled ? parseFloat(taxSetting.default_tax_rate) : 0;

                        const subtotal = qty * price;
                        const tax = subtotal * (taxRate / 100);
                        const total = subtotal + tax;

                        row.querySelector('.tax-amount-display').value = tax.toFixed(2);
                        row.querySelector('.total-amount-display').value = total.toFixed(2);

                        grandSubtotal += subtotal;
                        grandTax += tax;
                    });

                    document.getElementById('grand_subtotal').textContent = grandSubtotal.toFixed(2);
                    document.getElementById('grand_tax').textContent = grandTax.toFixed(2);
                    document.getElementById('grand_total').textContent = (grandSubtotal + grandTax).toFixed(2);
                }

                function performProductSearch(query, resultsDiv, idInput, searchInput) {
                    const results = products.filter(p =>
                        p.name_en.toLowerCase().includes(query.toLowerCase()) ||
                        (p.code && p.code.toLowerCase().includes(query.toLowerCase()))
                    ).slice(0, 10);

                    if (results.length > 0) {
                        resultsDiv.innerHTML = results.map(p => `
                                    <div class="search-result-item p-2 border-bottom" data-id="${p.id}" data-name="${p.name_en}" style="cursor: pointer;">
                                        <div class="fw-bold">${p.name_en}</div>
                                        <small class="text-muted">${p.code || ''}</small>
                                    </div>
                                `).join('');
                        resultsDiv.style.display = 'block';

                        resultsDiv.querySelectorAll('.search-result-item').forEach(item => {
                            item.addEventListener('click', function () {
                                searchInput.value = this.dataset.name;
                                idInput.value = this.dataset.id;
                                resultsDiv.style.display = 'none';
                                calculateTotals();
                            });
                        });
                    } else {
                        resultsDiv.innerHTML = '<div class="p-2 text-muted">No product found</div>';
                        resultsDiv.style.display = 'block';
                    }
                }

                addItemBtn.addEventListener('click', () => addItem());

                // Add initial row if empty
                @if(old('items'))
                    @foreach(old('items') as $item)
                        addItem(@json($item));
                    @endforeach
                @else
                    addItem();
            });

            // Enter key to next field navigation
            document.getElementById('customerRequestForm').addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA' && e.target.type !== 'submit') {
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

        <style>
            .search-result-item:hover {
                background-color: #f8f9fa;
            }

            .glassy {
                background: rgba(255, 255, 255, 0.8) !important;
                backdrop-filter: blur(10px);
            }
        </style>
    @endpush
@endsection
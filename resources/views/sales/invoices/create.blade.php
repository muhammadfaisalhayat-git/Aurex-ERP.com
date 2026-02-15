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
                                <div class="col-md-6 position-relative" id="import-source-container" style="display: none;">
                                    <label class="form-label fw-bold">{{ __('sales.source_document') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="import_source_search" autocomplete="off"
                                            placeholder="{{ __('sales.search_document_placeholder') }}">
                                        <button class="btn btn-outline-secondary" type="button"><i
                                                class="fas fa-search"></i></button>
                                    </div>
                                    <div id="import-source-results" class="search-results-container glassy"></div>
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
                                <div class="col-md-4 mb-3 position-relative" id="customer-container">
                                    <label for="customer_search" class="form-label">{{ __('sales.customer') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="customer_search" autocomplete="off"
                                            placeholder="Type to search customer..."
                                            value="{{ __('sales.cash_customer') }}">
                                        <input type="hidden" id="customer_id" name="customer_id"
                                            value="{{ old('customer_id') }}">
                                        <button class="btn btn-outline-secondary" type="button">
                                            <i class="fas fa-users"></i>
                                        </button>
                                    </div>
                                    <div id="customer-results" class="search-results-container glassy"></div>
                                    @error('customer_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="branch_id" class="form-label">{{ __('sales.branch') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('branch_id') is-invalid @enderror" id="branch_id"
                                        name="branch_id" required>
                                        <option value="">{{ __('common.select') }}</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ old('branch_id', auth()->user()->branch_id) == $branch->id ? 'selected' : '' }}>
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
                                            <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name_en }}</option>
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

                                <div class="col-md-4 mb-3 position-relative" id="salesman-container">
                                    <label for="salesman_search" class="form-label">{{ __('sales.salesman') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="salesman_search" autocomplete="off"
                                            placeholder="Type to search salesman...">
                                        <input type="hidden" id="salesman_id" name="salesman_id"
                                            value="{{ old('salesman_id') }}">
                                        <button class="btn btn-outline-secondary" type="button">
                                            <i class="fas fa-user-tie"></i>
                                        </button>
                                    </div>
                                    <div id="salesmen-results" class="search-results-container glassy"></div>
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
                                            <th style="width: 30%;">{{ __('sales.product') }}</th>
                                            <th style="width: 12%;">{{ __('sales.quantity') }}</th>
                                            <th style="width: 15%;">{{ __('sales.unit_price') }}</th>
                                            <th style="width: 12%;">{{ __('sales.discount') }} (%)</th>
                                            <th style="width: 12%;">{{ __('sales.tax') }}</th>
                                            <th style="width: 15%;">{{ __('sales.total') }}</th>
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
                                            <td colspan="5" class="text-end fw-bold">{{ __('sales.tax') }}</td>
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
                                                                        <div class="input-group">
                                                                            <input type="text" class="form-control product-search-input" 
                                                                                   placeholder="Type to search products..." 
                                                                                   autocomplete="off"
                                                                                   data-product-id="">
                                                                            <input type="hidden" class="product-id-input" name="items[INDEX][product_id]" required>
                                                                            <button class="btn btn-outline-secondary product-search-btn" type="button" title="Search Product (F2)">
                                                                                <i class="fas fa-search"></i>
                                                                            </button>
                                                                        </div>
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
                                                                    <td>
                                                                        <input type="text" class="form-control tax-display" readonly>
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

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let itemIndex = 0;
            const defaultTaxRate = {{ $taxSetting->default_tax_rate ?? 0 }};
            const tableBody = document.querySelector('#items-table tbody');
            const template = document.getElementById('item-row-template').innerHTML;

            const salesmanData = [
                @foreach($salesmen as $salesman)
                    { id: {{ $salesman->id }}, name: "{{ $salesman->name }}" },
                @endforeach
                            ];

            const customerData = [
                @foreach($customers as $customer)
                    { id: {{ $customer->id }}, name: "{{ $customer->name_en }}", code: "{{ $customer->customer_code }}" },
                @endforeach
                            ];

            const productData = [
                @foreach($products as $product)
                                    {
                    id: {{ $product->id }},
                    name: "{{ $product->name }}",
                    code: "{{ $product->product_code }}",
                    price: {{ $product->sale_price ?? 0 }},
                    tax: {{ $product->tax_rate ?? $taxSetting->default_tax_rate ?? 0 }} 
                                    },
                @endforeach
                            ];

            function addItem() {
                const html = template.replace(/INDEX/g, itemIndex++);
                tableBody.insertAdjacentHTML('beforeend', html);
                const newRow = tableBody.lastElementChild;
                initProductSearch(newRow);
            }

            // Add initial item
            addItem();

            document.getElementById('add-item-btn').addEventListener('click', function () {
                addItem();
            });

            // Customer Search
            const customerInput = document.getElementById('customer_search');
            const customerResults = document.getElementById('customer-results');
            const customerHidden = document.getElementById('customer_id');

            customerInput.addEventListener('input', function () {
                const search = this.value.toLowerCase();
                if (search.length < 1) {
                    customerResults.style.display = 'none';
                    return;
                }

                const filtered = customerData.filter(c =>
                    c.name.toLowerCase().includes(search) ||
                    (c.code && c.code.toLowerCase().includes(search))
                );

                renderResults(customerResults, filtered, (item) => {
                    customerInput.value = item.name;
                    customerHidden.value = item.id;
                    customerResults.style.display = 'none';
                });
            });

            // Salesman Search
            const salesmanInput = document.getElementById('salesman_search');
            const salesmanResults = document.getElementById('salesmen-results');
            const salesmanHidden = document.getElementById('salesman_id');

            salesmanInput.addEventListener('input', function () {
                const search = this.value.toLowerCase();
                if (search.length < 1) {
                    salesmanResults.style.display = 'none';
                    return;
                }

                const filtered = salesmanData.filter(s => s.name.toLowerCase().includes(search));
                renderResults(salesmanResults, filtered, (item) => {
                    salesmanInput.value = item.name;
                    salesmanHidden.value = item.id;
                    salesmanResults.style.display = 'none';
                });
            });

            function initProductSearch(row) {
                const input = row.querySelector('.product-search-input');
                const results = row.querySelector('.product-results');
                const hidden = row.querySelector('.product-id-input');

                input.addEventListener('input', function () {
                    const search = this.value.toLowerCase();
                    if (search.length < 1) {
                        results.style.display = 'none';
                        return;
                    }

                    const filtered = productData.filter(p =>
                        p.name.toLowerCase().includes(search) ||
                        p.code.toLowerCase().includes(search)
                    );

                    renderResults(results, filtered, (product) => {
                        input.value = product.name;
                        hidden.value = product.id;
                        row.querySelector('.price-input').value = product.price;
                        row.querySelector('.tax-rate-input').value = product.tax;
                        results.style.display = 'none';
                        calculateRow(row);
                    }, true);
                });
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
                    if (isProduct) {
                        div.innerHTML = `
                                            <div class="item-title">${item.name}</div>
                                            <div class="item-subtitle">${item.code}</div>
                                            <div class="item-meta">{{ __('sales.price') }}: ${parseFloat(item.price).toFixed(2)}</div>
                                        `;
                    } else {
                        div.innerHTML = `
                                            <div class="item-title">${item.name}</div>
                                            ${item.code ? `<div class="item-subtitle">${item.code}</div>` : ''}
                                        `;
                    }

                    div.addEventListener('click', () => onSelect(item));
                    container.appendChild(div);
                });

                container.style.display = 'block';
            }

            // Import Data Logic
            const importSourceType = document.getElementById('import_source_type');
            const importSourceContainer = document.getElementById('import-source-container');
            const importSourceSearch = document.getElementById('import_source_search');
            const importSourceResults = document.getElementById('import-source-results');

            importSourceType.addEventListener('change', function () {
                if (this.value) {
                    importSourceContainer.style.display = 'block';
                    importSourceSearch.value = '';
                    importSourceResults.style.display = 'none';
                } else {
                    importSourceContainer.style.display = 'none';
                }
            });

            importSourceSearch.addEventListener('input', function () {
                const type = importSourceType.value;
                const search = this.value;
                if (!type || search.length < 1) {
                    importSourceResults.style.display = 'none';
                    return;
                }

                fetch(`{{ route('sales.invoices.import-sources') }}?type=${type}&q=${search}`)
                    .then(response => response.json())
                    .then(data => {
                        renderResults(importSourceResults, data, (item) => {
                            if (confirm('Importing this document will clear existing items. Continue?')) {
                                loadSourceData(type, item.id);
                            }
                            importSourceResults.style.display = 'none';
                            importSourceSearch.value = item.text;
                        });
                    });
            });

            function loadSourceData(type, id) {
                fetch(`{{ url('sales/invoices/source-data') }}/${type}/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        // Populate Main Fields
                        if (data.customer_id) {
                            document.getElementById('customer_id').value = data.customer_id;
                            document.getElementById('customer_search').value = data.customer_name;
                        }
                        if (data.branch_id) document.getElementById('branch_id').value = data.branch_id;
                        if (data.warehouse_id) document.getElementById('warehouse_id').value = data.warehouse_id;
                        if (data.salesman_id) {
                            document.getElementById('salesman_id').value = data.salesman_id;
                            const salesman = salesmanData.find(s => s.id == data.salesman_id);
                            if (salesman) document.getElementById('salesman_search').value = salesman.name;
                        }

                        // Clear and Populate Items
                        tableBody.innerHTML = '';
                        itemIndex = 0;

                        data.items.forEach(item => {
                            addItem();
                            const row = tableBody.lastElementChild;
                            const prInput = row.querySelector('.product-search-input');
                            const prHidden = row.querySelector('.product-id-input');

                            prInput.value = item.product_name;
                            prHidden.value = item.product_id;
                            row.querySelector('.quantity-input').value = item.quantity;
                            row.querySelector('.price-input').value = item.unit_price;
                            row.querySelector('.discount-input').value = item.discount_percentage;
                            row.querySelector('.tax-rate-input').value = item.tax_rate;

                            calculateRow(row);
                        });
                    });
            }

            // Global click to close dropdowns
            document.addEventListener('click', function (e) {
                if (!e.target.closest('.position-relative')) {
                    document.querySelectorAll('.search-results-container').forEach(c => c.style.display = 'none');
                }
            });

            tableBody.addEventListener('click', function (e) {
                if (e.target.closest('.remove-item')) {
                    const rows = tableBody.querySelectorAll('tr');
                    if (rows.length > 1) {
                        e.target.closest('tr').remove();
                        calculateTotals();
                    }
                    return;
                }

                const searchBtn = e.target.closest('.product-search-btn');
                if (searchBtn) {
                    e.preventDefault();
                    searchBtn.closest('tr').querySelector('.product-search-input').focus();
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'F2') {
                    e.preventDefault();
                    const customerField = document.getElementById('customer_search');
                    if (customerField && !customerField.value) {
                        customerField.focus();
                    } else {
                        // Focus first empty or last row product input
                        const productInputs = document.querySelectorAll('.product-search-input');
                        const targetInput = Array.from(productInputs).find(i => !i.value) || productInputs[productInputs.length - 1];
                        if (targetInput) { targetInput.focus(); targetInput.select(); }
                    }
                }

                if (e.key === 'Enter' && !e.target.matches('textarea')) {
                    if (e.target.classList.contains('search-result-item')) return;

                    const form = e.target.form;
                    if (!form) return;

                    const index = Array.from(form.elements).indexOf(e.target);
                    if (index > -1) {
                        // Special case: if in discount field of last row, add new row
                        if (e.target.classList.contains('discount-input')) {
                            const row = e.target.closest('tr');
                            if (row === tableBody.lastElementChild) {
                                e.preventDefault();
                                addItem();
                                setTimeout(() => {
                                    tableBody.lastElementChild.querySelector('.product-search-input').focus();
                                }, 10);
                                return;
                            }
                        }

                        // Normal behavior: focus next element
                        let next = form.elements[index + 1];
                        while (next && (next.readOnly || next.type === 'hidden' || next.disabled)) {
                            next = form.elements[Array.from(form.elements).indexOf(next) + 1];
                        }
                        if (next && next.type !== 'submit') {
                            e.preventDefault();
                            next.focus();
                        }
                    }
                }
            });

            tableBody.addEventListener('input', function (e) {
                if (e.target.matches('.quantity-input, .price-input, .discount-input')) {
                    calculateRow(e.target.closest('tr'));
                }
            });

            function calculateRow(row) {
                const qEl = row.querySelector('.quantity-input');
                const pEl = row.querySelector('.price-input');
                const dEl = row.querySelector('.discount-input');
                const trEl = row.querySelector('.tax-rate-input');

                if (!qEl || !pEl) return;

                const quantity = parseFloat(qEl.value) || 0;
                const price = parseFloat(pEl.value) || 0;
                const discountPercent = parseFloat(dEl ? dEl.value : 0) || 0;
                const taxRate = parseFloat(trEl ? trEl.value : 0) || 0;

                const gross = quantity * price;
                const discount = gross * (discountPercent / 100);
                const taxable = gross - discount;
                const tax = taxable * (taxRate / 100);
                const total = taxable + tax;

                const tdEl = row.querySelector('.tax-display');
                const totEl = row.querySelector('.total-display');

                if (tdEl) tdEl.value = tax.toFixed(2);
                if (totEl) totEl.value = total.toFixed(2);

                calculateTotals();
            }

            function calculateTotals() {
                let subtotal = 0;
                let taxAmount = 0;
                let grandTotal = 0;

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

                        const gross = quantity * price;
                        const discount = gross * (discountPercent / 100);
                        const taxable = gross - discount;
                        const tax = taxable * (taxRate / 100);

                        subtotal += taxable;
                        taxAmount += tax;
                    }
                });

                grandTotal = subtotal + taxAmount;

                const subEl = document.getElementById('subtotal');
                const taxEl = document.getElementById('tax_amount');
                const grandEl = document.getElementById('grand_total');

                if (subEl) subEl.textContent = subtotal.toFixed(2);
                if (taxEl) taxEl.textContent = taxAmount.toFixed(2);
                if (grandEl) grandEl.textContent = grandTotal.toFixed(2);
            }
        });
    </script>
@endpush
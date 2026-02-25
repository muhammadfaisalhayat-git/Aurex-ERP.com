@extends('layouts.app')

@section('title', __('messages.create_return') ?? 'Create Return')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">{{ __('messages.create_return') ?? 'Create Return' }}</h3>
                        <a href="{{ route('sales.returns.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> {{ __('messages.back') }}
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('sales.returns.store') }}" method="POST" id="return-form"
                            class="glassy-form">
                            @csrf
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="return_number"
                                        class="form-label">{{ __('messages.return_number') ?? 'Return Number' }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('return_number') is-invalid @enderror"
                                        id="return_number" name="return_number"
                                        value="{{ old('return_number', $documentNumber) }}" readonly>
                                    @error('return_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <input type="hidden" name="document_number" value="{{ $documentNumber }}">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="return_date" class="form-label">{{ __('messages.date') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('return_date') is-invalid @enderror"
                                        id="return_date" name="return_date" value="{{ old('return_date', date('Y-m-d')) }}"
                                        required>
                                    @error('return_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="customer_id" class="form-label">{{ __('messages.customer') }} <span
                                            class="text-danger" id="customer_asterisk">*</span></label>
                                    <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id"
                                        name="customer_id">
                                        <option value="">{{ __('messages.select_customer') }}</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="sales_invoice_id"
                                        class="form-label">{{ __('messages.sales_invoice') ?? 'Sales Invoice' }}</label>
                                    <select class="form-select @error('sales_invoice_id') is-invalid @enderror"
                                        name="sales_invoice_id" id="sales_invoice_id">
                                        <option value="">-- {{ __('messages.select_invoice') ?? 'Select Invoice' }} --
                                        </option>
                                        @foreach($invoices as $invoice)
                                            <option value="{{ $invoice->id }}" 
                                                data-customer-id="{{ $invoice->customer_id }}"
                                                {{ old('sales_invoice_id') == $invoice->id ? 'selected' : '' }}>
                                                {{ $invoice->invoice_number }}
                                                ({{ $invoice->customer->name ?? __('messages.unknown_customer') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('sales_invoice_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="return_type"
                                        class="form-label">{{ __('messages.return_type') ?? 'Return Type' }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('return_type') is-invalid @enderror"
                                        name="return_type" id="return_type" required>
                                        <option value="credit" {{ old('return_type', 'credit') == 'credit' ? 'selected' : '' }}>{{ __('messages.credit') ?? 'Credit' }}</option>
                                        <option value="cash" {{ old('return_type') == 'cash' ? 'selected' : '' }}>
                                            {{ __('messages.cash') ?? 'Cash' }}
                                        </option>
                                    </select>
                                    @error('return_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 mb-3 d-none" id="bank_account_container">
                                    <label for="bank_account_id"
                                        class="form-label">{{ __('messages.bank_account') ?? 'Bank/Cash Account' }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('bank_account_id') is-invalid @enderror"
                                        name="bank_account_id" id="bank_account_id">
                                        <option value="">-- {{ __('messages.select_account') ?? 'Select Account' }} --
                                        </option>
                                        @foreach($bankAccounts as $account)
                                            <option value="{{ $account->id }}" {{ old('bank_account_id') == $account->id ? 'selected' : '' }}>
                                                {{ $account->name }} ({{ $account->account_number }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('bank_account_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">


                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="branch_id" class="form-label">{{ __('messages.branch') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('branch_id') is-invalid @enderror" id="branch_id"
                                        name="branch_id" required>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ old('branch_id', auth()->user()->branch_id) == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="warehouse_id" class="form-label">{{ __('messages.warehouse') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('warehouse_id') is-invalid @enderror"
                                        id="warehouse_id" name="warehouse_id" required>
                                        <option value="">{{ __('messages.select_warehouse') }}</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                                {{ $warehouse->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="return_reason"
                                        class="form-label">{{ __('messages.return_reason') ?? 'Return Reason' }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('return_reason') is-invalid @enderror"
                                        id="return_reason" name="return_reason" value="{{ old('return_reason') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="reason_description"
                                        class="form-label">{{ __('messages.reason_description') ?? 'Reason Description' }}</label>
                                    <textarea class="form-control" id="reason_description" name="reason_description"
                                        rows="1">{{ old('reason_description') }}</textarea>
                                </div>
                            </div>

                            <h4 class="mt-4">{{ __('messages.items') }}</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="items-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 40%;">{{ __('messages.product') }}</th>
                                            <th style="width: 15%;">{{ __('messages.quantity') }}</th>
                                            <th style="width: 20%;">{{ __('messages.unit_price') }}</th>
                                            <th style="width: 20%;">{{ __('messages.total') }}</th>
                                            <th style="width: 5%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Row Template --}}
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5">
                                                <button type="button" class="btn btn-sm btn-success" id="add-item-btn">
                                                    <i class="fas fa-plus"></i> {{ __('messages.add_item') }}
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold">{{ __('messages.total') }}</td>
                                            <td class="text-end fw-bold" id="total_amount_display">0.00</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="mb-3 mt-3">
                                <label for="notes" class="form-label">{{ __('messages.notes') }}</label>
                                <textarea class="form-control" id="notes" name="notes"
                                    rows="3">{{ old('notes') }}</textarea>
                            </div>

                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-primary px-5">
                                    <i class="fas fa-save me-1"></i> {{ __('messages.save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/template" id="item-row-template">
                                            <tr>
                                                <td>
                                                    <div class="position-relative product-search-container">
                                                        <input type="text" class="form-control product-search-input"
                                                            placeholder="{{ __('messages.select_product') ?? 'Search product...' }}"
                                                            autocomplete="off" required>
                                                        <input type="hidden" name="items[INDEX][product_id]" class="product-id-input" required>
                                                        <div class="product-results" style="display: none; position: absolute; z-index: 1000; width: 100%; background: white; border: 1px solid #ddd; max-height: 250px; overflow-y: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"></div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="number" name="items[INDEX][quantity]" class="form-control quantity-input" step="0.001" min="0.001" value="1" required>
                                                </td>
                                                <td>
                                                    <input type="number" name="items[INDEX][unit_price]" class="form-control price-input" step="0.01" min="0" required>
                                                </td>
                                                <td class="text-end">
                                                    <span class="row-total">0.00</span>
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
        document.addEventListener('turbo:load', function () {
            let itemIndex = 0;
            const tableBody = document.querySelector('#items-table tbody');
            const template = document.getElementById('item-row-template').innerHTML;
            const invoiceSelect = document.getElementById('sales_invoice_id');
            const customerSelect = document.getElementById('customer_id');

            if (!invoiceSelect || !customerSelect) return;

            // Store original invoice options for filtering immediately on load
            const allInvoiceOptions = Array.from(invoiceSelect.options).map(opt => ({
                value: opt.value,
                text: opt.innerText,
                customerId: opt.getAttribute('data-customer-id') || ''
            }));

            const productData = [
                @foreach($products as $product)
                        {
                    id: {{ $product->id }},
                    name_en: "{{ addslashes($product->name_en) }}",
                    name_ar: "{{ addslashes($product->name_ar) }}",
                    name: "{{ addslashes($product->name) }}",
                    code: "{{ $product->product_code ?? '' }}",
                    price: {{ $product->sale_price ?? 0 }},
                    cost: {{ $product->cost_price ?? 0 }}
                        },
                @endforeach
                ];

            function initProductSearch(row) {
                const searchInput = row.querySelector('.product-search-input');
                const idInput = row.querySelector('.product-id-input');
                const resultsDiv = row.querySelector('.product-results');
                let highlightIndex = -1;

                if (!searchInput) return;

                searchInput.addEventListener('input', function () {
                    idInput.value = '';
                    highlightIndex = -1;
                    performSearch(this.value);
                });

                searchInput.addEventListener('focus', function () {
                    performSearch(this.value);
                });

                searchInput.addEventListener('keydown', function (e) {
                    const items = resultsDiv.querySelectorAll('.search-result-item');
                    if (!items.length) return;
                    if (e.key === 'ArrowDown') { e.preventDefault(); highlightIndex = (highlightIndex + 1) % items.length; updateHL(items, highlightIndex); }
                    else if (e.key === 'ArrowUp') { e.preventDefault(); highlightIndex = (highlightIndex - 1 + items.length) % items.length; updateHL(items, highlightIndex); }
                    else if (e.key === 'Enter' && highlightIndex > -1) { e.preventDefault(); e.stopImmediatePropagation(); items[highlightIndex].click(); highlightIndex = -1; }
                });

                function updateHL(items, idx) {
                    items.forEach((item, i) => {
                        item.classList.toggle('bg-primary', i === idx);
                        item.classList.toggle('text-white', i === idx);
                    });
                }

                function performSearch(query) {
                    const results = productData.filter(p =>
                        p.name.toLowerCase().includes(query.toLowerCase()) ||
                        p.code.toLowerCase().includes(query.toLowerCase())
                    ).slice(0, 10);

                    if (results.length > 0) {
                        const currentLocale = '{{ app()->getLocale() }}';
                        resultsDiv.innerHTML = results.map(p => {
                            const currentName = currentLocale === 'ar' ? p.name_ar || p.name_en : p.name_en || p.name_ar;
                            const subName = currentLocale === 'ar' ? p.name_en : p.name_ar;
                            return `
                                    <div class="search-result-item p-2 border-bottom" data-id="${p.id}" data-name="${currentName}" data-price="${p.price}" 
                                         data-cost="${p.cost}" style="cursor:pointer;">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="result-content">
                                                <div class="fw-bold">${currentName}</div>
                                                ${subName && subName !== currentName ? `<div class="small text-muted">${subName}</div>` : ''}
                                                <small class="text-muted">${p.code}</small>
                                            </div>
                                            <div class="d-flex gap-2 flex-shrink-0 ms-2 small">
                                                <span style="color:#dc3545; font-weight:600;" title="Cost">${parseFloat(p.cost || 0).toFixed(2)}</span>
                                                <span style="color:#198754; font-weight:600;" title="Price">${parseFloat(p.price || 0).toFixed(2)}</span>
                                            </div>
                                        </div>
                                    </div>
                                `;
                        }).join('');
                        resultsDiv.style.display = 'block';

                        resultsDiv.querySelectorAll('.search-result-item').forEach(item => {
                            item.addEventListener('click', function () {
                                searchInput.value = this.dataset.name;
                                idInput.value = this.dataset.id;
                                row.querySelector('.price-input').value = this.dataset.price;
                                resultsDiv.style.display = 'none';
                                calculateRow(row);
                            });
                        });
                    } else {
                        resultsDiv.innerHTML = '<div class="p-2 text-muted">No product found</div>';
                        resultsDiv.style.display = 'block';
                    }
                }
            }

            function addItem(data = null) {
                const html = template.replace(/INDEX/g, itemIndex++);
                tableBody.insertAdjacentHTML('beforeend', html);
                const row = tableBody.lastElementChild;
                initProductSearch(row);

                if (data) {
                    const product = productData.find(p => p.id == data.product_id);
                    const searchInput = row.querySelector('.product-search-input');
                    const idInput = row.querySelector('.product-id-input');
                    const quantityInput = row.querySelector('.quantity-input');
                    const priceInput = row.querySelector('.price-input');

                    if (product) searchInput.value = product.name;
                    idInput.value = data.product_id;
                    quantityInput.value = data.quantity;
                    priceInput.value = data.unit_price;
                    calculateRow(row);
                }

                const quantityInput = row.querySelector('.quantity-input');
                const priceInputEl = row.querySelector('.price-input');
                quantityInput.addEventListener('input', () => calculateRow(row));
                priceInputEl.addEventListener('input', () => calculateRow(row));
                row.querySelector('.remove-item').addEventListener('click', () => {
                    row.remove();
                    calculateTotal();
                });
            }

            function calculateRow(row) {
                const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
                const price = parseFloat(row.querySelector('.price-input').value) || 0;
                const total = qty * price;
                row.querySelector('.row-total').textContent = total.toFixed(2);
                calculateTotal();
            }

            function calculateTotal() {
                let total = 0;
                tableBody.querySelectorAll('.row-total').forEach(el => {
                    total += parseFloat(el.textContent) || 0;
                });
                document.getElementById('total_amount_display').textContent = total.toFixed(2);
            }

            $(invoiceSelect).on('change', function () {
                const invoiceId = this.value;
                if (!invoiceId) return;

                fetch(`/sales/invoices/source-data/sales_invoice/${invoiceId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                            return;
                        }

                        // Populate header
                        $(customerSelect).val(data.customer_id).trigger('change');

                        document.getElementById('branch_id').value = data.branch_id;
                        document.getElementById('warehouse_id').value = data.warehouse_id;
                        
                        // Force update Select2 displays for branch/warehouse too
                        window.jQuery && $('#branch_id, #warehouse_id').trigger('change.select2');

                        // Clear and populate items
                        tableBody.innerHTML = '';
                        itemIndex = 0;
                        data.items.forEach(item => {
                            addItem(item);
                        });
                    })
                    .catch(error => console.error('Error fetching invoice data:', error));
            });

            // Close dropdowns on outside click
            document.addEventListener('click', function (e) {
                if (!e.target.closest('.product-search-container')) {
                    document.querySelectorAll('.product-results').forEach(c => c.style.display = 'none');
                }
            });

            // Handle Return Type Change
            const returnTypeSelect = document.getElementById('return_type');
            const bankAccountContainer = document.getElementById('bank_account_container');
            const customerAsterisk = document.getElementById('customer_asterisk');

            function toggleReturnFields() {
                if (returnTypeSelect.value === 'cash') {
                    // Cash Return
                    if (bankAccountContainer) bankAccountContainer.classList.remove('d-none');
                    const bankAccIdEl = document.getElementById('bank_account_id');
                    if (bankAccIdEl) bankAccIdEl.setAttribute('required', 'required');

                    if (customerAsterisk) customerAsterisk.classList.add('d-none');
                    customerSelect.removeAttribute('required');
                } else {
                    // Credit Return
                    if (bankAccountContainer) bankAccountContainer.classList.add('d-none');
                    const bankAccIdEl = document.getElementById('bank_account_id');
                    if (bankAccIdEl) bankAccIdEl.removeAttribute('required');

                    if (customerAsterisk) customerAsterisk.classList.remove('d-none');
                    customerSelect.setAttribute('required', 'required');
                }
            }

            $(returnTypeSelect).on('change', toggleReturnFields);
            toggleReturnFields(); // Initial check

            function filterInvoices() {
                const selectedCustomerId = String(customerSelect.value || '');
                const currentInvoiceVal = invoiceSelect.value;
                
                // If Select2 is active, we MUST destroy it before modifying options
                const $invoice = $(invoiceSelect);
                const isSelect2 = $invoice.data('select2');
                if (isSelect2) {
                    $invoice.select2('destroy');
                }

                // Clear and rebuild options
                invoiceSelect.innerHTML = '';
                let foundMatch = false;
                
                allInvoiceOptions.forEach(opt => {
                    const optCustomerId = String(opt.customerId || '');
                    if (opt.value === "" || !selectedCustomerId || optCustomerId === selectedCustomerId) {
                        const newOpt = new Option(opt.text, opt.value);
                        if (opt.customerId) newOpt.setAttribute('data-customer-id', opt.customerId);
                        if (opt.value === currentInvoiceVal && opt.value !== "") {
                            newOpt.selected = true;
                            foundMatch = true;
                        }
                        invoiceSelect.add(newOpt);
                    }
                });

                if (!foundMatch && currentInvoiceVal !== "") {
                    invoiceSelect.value = "";
                }

                // Re-initialize Select2 if it was previously active (or should be)
                if (isSelect2 || $invoice.hasClass('form-select')) {
                    const placeholder = $invoice.find('option[value=""]').first().text().trim() || '--';
                    $invoice.select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        dir: document.documentElement.dir || 'ltr',
                        allowClear: true,
                        placeholder: placeholder
                    });
                } else {
                    invoiceSelect.dispatchEvent(new Event('change'));
                }
            }

            $(customerSelect).on('change', filterInvoices);
            
            // Initial filter if needed
            if (customerSelect.value) {
                setTimeout(filterInvoices, 500);
            }

            const addItemBtn = document.getElementById('add-item-btn');
            addItemBtn.removeEventListener('click', addItemBtn._addItemHandler);
            addItemBtn._addItemHandler = () => addItem();
            addItemBtn.addEventListener('click', addItemBtn._addItemHandler);

            // Add initial row only if table is empty and no invoice is pre-selected
            if (!invoiceSelect.value && tableBody.children.length === 0) {
                addItem();
            }
        });

        // Clear dynamic content before Turbo caches the page
        document.addEventListener('turbo:before-cache', function () {
            const tb = document.querySelector('#items-table tbody');
            if (tb) tb.innerHTML = '';
        });
    </script>
@endpush
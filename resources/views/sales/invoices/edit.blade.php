@extends('layouts.app')

@section('title', __('sales.edit_invoice'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('sales.edit_invoice') }}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('sales.invoices.update', $invoice) }}" method="POST" id="invoice-form">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="document_number"
                                        class="form-label">{{ __('sales.document_number') }}</label>
                                    <input type="text" class="form-control" value="{{ $invoice->document_number }}"
                                        readonly>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="invoice_number" class="form-label">{{ __('sales.invoice_number') }}</label>
                                    <input type="text" class="form-control" value="{{ $invoice->invoice_number }}" readonly>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="invoice_date" class="form-label">{{ __('sales.date') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('invoice_date') is-invalid @enderror"
                                        id="invoice_date" name="invoice_date"
                                        value="{{ old('invoice_date', $invoice->invoice_date->format('Y-m-d')) }}" required>
                                    @error('invoice_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="due_date" class="form-label">{{ __('sales.due_date') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                                        id="due_date" name="due_date"
                                        value="{{ old('due_date', $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '') }}"
                                        required>
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="customer_id" class="form-label">{{ __('sales.customer') }}</label>
                                    <select class="form-select select2 @error('customer_id') is-invalid @enderror"
                                        id="customer_id" name="customer_id">
                                        <option value="">{{ __('common.select') }}</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ old('customer_id', $invoice->customer_id) == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="branch_id" class="form-label">{{ __('sales.branch') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('branch_id') is-invalid @enderror" id="branch_id"
                                        name="branch_id" required>
                                        <option value="">{{ __('common.select') }}</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ old('branch_id', $invoice->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->name_en }}
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
                                            <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $invoice->warehouse_id) == $warehouse->id ? 'selected' : '' }}>
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
                                        <option value="cash" {{ old('payment_terms', $invoice->payment_terms) == 'cash' ? 'selected' : '' }}>{{ __('sales.cash') }}</option>
                                        <option value="credit" {{ old('payment_terms', $invoice->payment_terms) == 'credit' ? 'selected' : '' }}>{{ __('sales.credit') }}</option>
                                        <option value="installment" {{ old('payment_terms', $invoice->payment_terms) == 'installment' ? 'selected' : '' }}>
                                            {{ __('sales.installment') }}
                                        </option>
                                    </select>
                                    @error('payment_terms')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="salesman_id" class="form-label">{{ __('sales.salesman') }}</label>
                                    <select class="form-select @error('salesman_id') is-invalid @enderror" id="salesman_id"
                                        name="salesman_id">
                                        <option value="">{{ __('common.select') }}</option>
                                        {{-- Populate salesman based on branch/user via JS or backend --}}
                                    </select>
                                    @error('salesman_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="reference_number"
                                        class="form-label">{{ __('sales.reference_number') }}</label>
                                    <input type="text" class="form-control @error('reference_number') is-invalid @enderror"
                                        id="reference_number" name="reference_number"
                                        value="{{ old('reference_number', $invoice->reference_number) }}">
                                    @error('reference_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <h4 class="mt-4">{{ __('sales.items') }}</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="items-table">
                                    <thead>
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
                                        @foreach($invoice->items as $index => $item)
                                            <tr data-available-stock="{{ $item->available_stock ?? 0 }}">
                                                <td>
                                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                                    <div class="position-relative product-search-container">
                                                        <input type="text" class="form-control product-search-input"
                                                            placeholder="{{ __('messages.select_product') }}" autocomplete="off"
                                                            value="{{ $item->product->name ?? '' }}" required>
                                                        <input type="hidden" name="items[{{ $index }}][product_id]"
                                                            class="product-id-input" value="{{ $item->product_id }}" required>
                                                        <div class="product-results search-results-container"
                                                            style="display: none; position: absolute; z-index: 1000; width: 100%; background: white; border: 1px solid #ddd; max-height: 250px; overflow-y: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control quantity-input"
                                                        name="items[{{ $index }}][quantity]" step="0.001" min="0.001"
                                                        value="{{ $item->quantity }}" required>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control price-input"
                                                        name="items[{{ $index }}][unit_price]" step="0.01" min="0"
                                                        value="{{ $item->unit_price }}" required>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control discount-input"
                                                        name="items[{{ $index }}][discount_percentage]" step="0.01" min="0"
                                                        max="100" value="{{ $item->discount_percentage }}">
                                                </td>
                                                <td class="d-none">
                                                    <input type="text" class="form-control tax-display"
                                                        placeholder="{{ __('sales.vat') }}"
                                                        value="{{ number_format($item->tax_amount, 2) }}" readonly>
                                                    <input type="hidden" class="tax-rate-input" value="{{ $item->tax_rate }}">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control total-display"
                                                        value="{{ number_format($item->total, 2) }}" readonly>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger remove-item">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="6">
                                                <button type="button" class="btn btn-sm btn-success" id="add-item">
                                                    <i class="fas fa-plus"></i> {{ __('sales.add_item') }}
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="text-end fw-bold">{{ __('sales.subtotal') }}</td>
                                            <td class="text-end" id="subtotal">{{ number_format($invoice->subtotal, 2) }}
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="text-end fw-bold">{{ __('sales.vat') }}</td>
                                            <td class="text-end" id="tax_amount">
                                                {{ number_format($invoice->tax_amount, 2) }}
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="text-end fw-bold">{{ __('sales.grand_total') }}</td>
                                            <td class="text-end fw-bold" id="grand_total">
                                                {{ number_format($invoice->grand_total, 2) }}
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">{{ __('sales.notes') }}</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes"
                                    rows="3">{{ old('notes', $invoice->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-end">
                                <a href="{{ route('sales.invoices.index') }}"
                                    class="btn btn-secondary">{{ __('common.cancel') }}</a>
                                <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- New Item Row Template --}}
    <template id="item-row-template">
        <tr>
            <td>
                <input type="hidden" name="items[INDEX][id]" value="">
                <div class="position-relative product-search-container">
                    <input type="text" class="form-control product-search-input"
                        placeholder="{{ __('messages.select_product') }}" autocomplete="off" required>
                    <input type="hidden" name="items[INDEX][product_id]" class="product-id-input" required>
                    <div class="product-results search-results-container"
                        style="display: none; position: absolute; z-index: 1000; width: 100%; background: white; border: 1px solid #ddd; max-height: 250px; overflow-y: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    </div>
                </div>
            </td>
            <td>
                <input type="number" class="form-control quantity-input" name="items[INDEX][quantity]" step="0.001"
                    min="0.001" value="1" required>
            </td>
            <td>
                <input type="number" class="form-control price-input" name="items[INDEX][unit_price]" step="0.01" min="0"
                    required>
            </td>
            <td>
                <input type="number" class="form-control discount-input" name="items[INDEX][discount_percentage]"
                    step="0.01" min="0" max="100" value="0">
            </td>
            <td class="d-none">
                <input type="text" class="form-control tax-display" placeholder="{{ __('sales.vat') }}" readonly>
                <input type="hidden" class="tax-rate-input">
            </td>
            <td>
                <input type="text" class="form-control total-display" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-item">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    </template>

@endsection

@push('scripts')
    <script>
        document.addEventListener('turbo:load', function () {
            let itemIndex = {{ $invoice->items->count() }};
            const defaultTaxRate = {{ $taxSetting->default_tax_rate ?? 0 }};
            const tableBody = document.querySelector('#items-table tbody');


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
                    const warehouseId = document.getElementById('warehouse_id')?.value;

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

                    const transliterated = window.transliterateToArabic(query);
                    const branchId = document.getElementById('branch_id')?.value;

                    fetch(`{{ route('inventory.products.ajax-search') }}?q=${encodeURIComponent(query)}&warehouse_id=${warehouseId}&branch_id=${branchId}`)
                        .then(response => response.json())
                        .then(results => {
                            if (results.length > 0) {
                                const currentLocale = '{{ app()->getLocale() }}';
                                resultsDiv.innerHTML = results.map(p => {
                                    const currentName = currentLocale === 'ar' ? p.name_ar || p.name_en : p.name_en || p.name_ar;
                                    const subName = currentLocale === 'ar' ? p.name_en : p.name_ar;
                                    const stockColor = (p.available_quantity > 0) ? 'text-success' : 'text-danger';
                                    const stockLabel = '{{ __("messages.stock") ?? "Stock" }}';

                                    return `
                                                                <div class="search-result-item p-2 border-bottom" 
                                                                    data-id="${p.id}" 
                                                                    data-name="${currentName}" 
                                                                    data-price="${p.sale_price || 0}" 
                                                                    data-tax="${p.tax_rate || 15}"
                                                                    data-cost="${p.cost_price || 0}"
                                                                    data-stock="${p.available_quantity || 0}"
                                                                    style="cursor: pointer;">
                                                                    <div class="d-flex justify-content-between align-items-start w-100">
                                                                        <div class="result-content pe-3 d-flex flex-column gap-1 flex-grow-1">
                                                                            <div class="fw-bold d-flex align-items-center gap-2 flex-wrap">
                                                                                ${p.code ? `<span style="background:#e9f0ff;color:#3d6bc7;font-size:0.7rem;font-weight:700;padding:1px 7px;border-radius:10px;flex-shrink:0;">${p.code}</span>` : ''}
                                                                                <span>${currentName}</span>
                                                                            </div>
                                                                            ${subName && subName !== currentName ? `<div class="small text-muted">${subName}</div>` : ''}
                                                                            <small class="${stockColor} fw-bold d-block"><i class="fas fa-boxes" style="font-size:0.65rem;"></i> ${stockLabel}: ${parseFloat(p.available_quantity || 0).toFixed(2)}</small>
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
                                                icon: 'error',
                                                title: '{{ __("messages.out_of_stock") ?? "Out of Stock" }}',
                                                text: '{{ __("messages.product_not_available") ?? "This product is currently out of stock." }}',
                                                confirmButtonText: '{{ __("messages.ok") ?? "OK" }}'
                                            });
                                            resultsDiv.style.display = 'none';
                                            searchInput.value = '';
                                            return;
                                        }

                                        searchInput.value = this.dataset.name;
                                        idInput.value = this.dataset.id;
                                        row.dataset.availableStock = this.dataset.stock;
                                        const priceInput = row.querySelector('.price-input');
                                        const taxRateInput = row.querySelector('.tax-rate-input');
                                        if (priceInput) priceInput.value = this.dataset.price;
                                        if (taxRateInput) taxRateInput.value = this.dataset.tax;
                                        if (!row.querySelector('.quantity-input').value) {
                                            row.querySelector('.quantity-input').value = 1;
                                        }
                                        resultsDiv.style.display = 'none';
                                        calculateRow($(row));
                                    });
                                });
                            } else {
                                resultsDiv.innerHTML = '<div class="p-2 text-muted">No product found</div>';
                                resultsDiv.style.display = 'block';
                            }
                        })
                        .catch(err => {
                            console.error('Search error:', err);
                            resultsDiv.innerHTML = '<div class="p-2 text-danger">Error fetching results</div>';
                            resultsDiv.style.display = 'block';
                        });
                }
            }

            // Init search on pre-existing rows
            tableBody.querySelectorAll('tr').forEach(row => {
                initProductSearch(row);
            });

            // Add Row
            function addItem() {
                const template = document.getElementById('item-row-template');
                const clone = template.content.cloneNode(true);
                const tr = clone.querySelector('tr');
                tr.querySelectorAll('[name*="INDEX"]').forEach(el => {
                    el.name = el.name.replace('INDEX', itemIndex);
                });
                tableBody.appendChild(tr);
                initProductSearch(tr);
                itemIndex++;
            }

            // Use named function to avoid stacking listeners on turbo:load
            const addItemBtnEl = document.getElementById('add-item');
            addItemBtnEl.removeEventListener('click', addItemBtnEl._addItemHandler);
            addItemBtnEl._addItemHandler = addItem;
            addItemBtnEl.addEventListener('click', addItemBtnEl._addItemHandler);

            // Remove Row
            $(document).on('click', '.remove-item', function () {
                $(this).closest('tr').remove();
                calculateTotals();
            });

            // Close dropdowns on outside click
            document.addEventListener('click', function (e) {
                if (!e.target.closest('.product-search-container')) {
                    document.querySelectorAll('.product-results').forEach(c => c.style.display = 'none');
                }
            });

            $(document).on('input', '.quantity-input, .price-input, .discount-input', function () {
                calculateRow($(this).closest('tr'));
            });

            function calculateRow(row) {
                const qEl = row.find('.quantity-input');
                const quantity = parseFloat(qEl.val()) || 0;
                const availableStock = parseFloat(row.attr('data-available-stock')) || parseFloat(row.get(0).dataset.availableStock) || 0;

                if (quantity > availableStock && availableStock > 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: '{{ __("messages.stock_shortage") ?? "Stock Shortage" }}',
                        text: `{{ __('messages.quantity_exceeds_available_stock') ?? 'Quantity exceeds available stock' }} (${availableStock})`,
                        confirmButtonText: '{{ __("messages.ok") ?? "OK" }}'
                    });
                    qEl.val(availableStock);
                    return calculateRow(row);
                }

                const price = parseFloat(row.find('.price-input').val()) || 0;
                const discountPercent = parseFloat(row.find('.discount-input').val()) || 0;
                const taxRate = parseFloat(row.find('.tax-rate-input').val()) || 0;

                // Tax-inclusive: price already contains tax
                const gross = quantity * price * (1 - discountPercent / 100);
                const net = taxRate > 0 ? gross / (1 + taxRate / 100) : gross;
                const tax = gross - net;

                row.find('.tax-display').val(tax.toFixed(2));
                row.find('.total-display').val(gross.toFixed(2));  // Total = inclusive price

                calculateTotals();
            }

            function calculateTotals() {
                let subtotal = 0, taxAmount = 0;

                $('#items-table tbody tr').each(function () {
                    const row = $(this);
                    const quantity = parseFloat(row.find('.quantity-input').val()) || 0;
                    const price = parseFloat(row.find('.price-input').val()) || 0;
                    const discountPercent = parseFloat(row.find('.discount-input').val()) || 0;
                    const taxRate = parseFloat(row.find('.tax-rate-input').val()) || 0;

                    // Tax-inclusive extraction
                    const gross = quantity * price * (1 - discountPercent / 100);
                    const net = taxRate > 0 ? gross / (1 + taxRate / 100) : gross;
                    const tax = gross - net;

                    subtotal += net;
                    taxAmount += tax;
                });

                const grandTotal = subtotal + taxAmount;
                $('#subtotal').text(subtotal.toFixed(2));
                $('#tax_amount').text(taxAmount.toFixed(2));
                $('#grand_total').text(grandTotal.toFixed(2));
            }

            // Keyboard shortcut F2 to focus first empty product field
            $(document).on('keydown', function (e) {
                if (e.key === 'F2') {
                    e.preventDefault();
                    const emptyInput = Array.from(document.querySelectorAll('.product-search-input')).find(i => !i.value);
                    if (emptyInput) { emptyInput.focus(); emptyInput.select(); }
                }
            });
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
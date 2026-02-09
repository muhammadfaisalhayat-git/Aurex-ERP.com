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
                        <form action="{{ route('sales.invoices.store') }}" method="POST" id="invoice-form">
                            @csrf

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
                                    <label for="due_date" class="form-label">{{ __('sales.due_date') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                                        id="due_date" name="due_date" value="{{ old('due_date') }}" required>
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="customer_id" class="form-label">{{ __('sales.customer') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id"
                                        name="customer_id" required>
                                        <option value="">{{ __('common.select') }}</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name_en }}</option>
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
                                        <option value="cash" {{ old('payment_terms') == 'cash' ? 'selected' : '' }}>
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
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control product-search-input" 
                                                               list="products-list-INDEX" 
                                                               placeholder="Type to search products..." 
                                                               autocomplete="off"
                                                               data-product-id="">
                                                        <input type="hidden" class="product-id-input" name="items[INDEX][product_id]" required>
                                                        <button class="btn btn-outline-secondary product-search-btn" type="button" title="Search Product (F2)">
                                                            <i class="fas fa-search"></i>
                                                        </button>
                                                    </div>
                                                    <datalist id="products-list-INDEX">
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->name }}" data-id="{{ $product->id }}" data-price="{{ $product->unit_price }}" data-tax="{{ $product->tax_rate }}">
                                                        @endforeach
                                                    </datalist>
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

            function addItem() {
                const html = template.replace(/INDEX/g, itemIndex++);
                tableBody.insertAdjacentHTML('beforeend', html);

                // Re-initialize select2 if needed, or other plugins
            }

            // Add initial item
            addItem();

            document.getElementById('add-item-btn').addEventListener('click', function () {
                addItem();
            });

            tableBody.addEventListener('click', function (e) {
                if (e.target.closest('.remove-item')) {
                    e.target.closest('tr').remove();
                    calculateTotals();
                    return;
                }

                // Search button click - focus on the product search input
                const searchBtn = e.target.closest('.product-search-btn');
                if (searchBtn) {
                    e.preventDefault();
                    const row = searchBtn.closest('tr');
                    const productInput = row.querySelector('.product-search-input');
                    if (productInput) {
                        productInput.focus();
                        productInput.select();
                    }
                }
            });

            // Keyboard shortcut: F2 to focus on first empty product input
            document.addEventListener('keydown', function (e) {
                if (e.key === 'F2') {
                    e.preventDefault();
                    const emptyInput = document.querySelector('.product-search-input');
                    if (emptyInput) {
                        emptyInput.focus();
                        emptyInput.select();
                    }
                }
            });

            // Handle product selection from datalist
            tableBody.addEventListener('input', function (e) {
                if (e.target.matches('.product-search-input')) {
                    const input = e.target;
                    const row = input.closest('tr');
                    const datalistId = input.getAttribute('list');
                    const datalist = document.getElementById(datalistId);

                    if (datalist) {
                        const options = datalist.querySelectorAll('option');
                        const selectedOption = Array.from(options).find(opt => opt.value === input.value);

                        if (selectedOption) {
                            const productId = selectedOption.getAttribute('data-id');
                            const price = selectedOption.getAttribute('data-price') || 0;
                            const tax = selectedOption.getAttribute('data-tax') || defaultTaxRate;

                            // Set hidden product ID
                            row.querySelector('.product-id-input').value = productId;

                            // Set price and tax
                            row.querySelector('.price-input').value = price;
                            row.querySelector('.tax-rate-input').value = tax;

                            calculateRow(row);
                        }
                    }
                }

                // Handle quantity, price, discount changes
                if (e.target.matches('.quantity-input, .discount-input')) {
                    calculateRow(e.target.closest('tr'));
                }
            });

            function calculateRow(row) {
                const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
                const price = parseFloat(row.querySelector('.price-input').value) || 0;
                const discountPercent = parseFloat(row.querySelector('.discount-input').value) || 0;
                const taxRate = parseFloat(row.querySelector('.tax-rate-input').value) || 0;

                const gross = quantity * price;
                const discount = gross * (discountPercent / 100);
                const taxable = gross - discount;
                const tax = taxable * (taxRate / 100);
                const total = taxable + tax;

                row.querySelector('.tax-display').value = tax.toFixed(2);
                row.querySelector('.total-display').value = total.toFixed(2);

                calculateTotals();
            }

            function calculateTotals() {
                let subtotal = 0;
                let taxAmount = 0;
                let grandTotal = 0;

                document.querySelectorAll('#items-table tbody tr').forEach(row => {
                    const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
                    const price = parseFloat(row.querySelector('.price-input').value) || 0;
                    const discountPercent = parseFloat(row.querySelector('.discount-input').value) || 0;
                    const taxRate = parseFloat(row.querySelector('.tax-rate-input').value) || 0;

                    const gross = quantity * price;
                    const discount = gross * (discountPercent / 100);
                    const taxable = gross - discount;
                    const tax = taxable * (taxRate / 100);

                    subtotal += taxable;
                    taxAmount += tax;
                });

                grandTotal = subtotal + taxAmount;

                document.getElementById('subtotal').textContent = subtotal.toFixed(2);
                document.getElementById('tax_amount').textContent = taxAmount.toFixed(2);
                document.getElementById('grand_total').textContent = grandTotal.toFixed(2);
            }
        });
    </script>
@endpush
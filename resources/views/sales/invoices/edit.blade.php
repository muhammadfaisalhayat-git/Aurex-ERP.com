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
                                    <label for="customer_id" class="form-label">{{ __('sales.customer') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select select2 @error('customer_id') is-invalid @enderror"
                                        id="customer_id" name="customer_id" required>
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
                                            <th>{{ __('sales.product') }}</th>
                                            <th width="150">{{ __('sales.quantity') }}</th>
                                            <th width="150">{{ __('sales.unit_price') }}</th>
                                            <th width="150">{{ __('sales.discount') }} (%)</th>
                                            <th width="150" class="d-none">{{ __('sales.tax') }}</th>
                                            <th width="150">{{ __('sales.total') }}</th>
                                            <th width="50"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoice->items as $index => $item)
                                            <tr>
                                                <td>
                                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                                    <select class="form-select product-select"
                                                        name="items[{{ $index }}][product_id]" required>
                                                        <option value="">{{ __('common.select') }}</option>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}"
                                                                data-price="{{ $product->unit_price }}"
                                                                data-tax="{{ $product->tax_rate }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                                {{ $product->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control quantity-input"
                                                        name="items[{{ $index }}][quantity]" step="0.001" min="0.001"
                                                        value="{{ $item->quantity }}" required>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control price-input"
                                                        name="items[{{ $index }}][unit_price]" step="0.01" min="0"
                                                        value="{{ $item->unit_price }}" readonly required>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control discount-input"
                                                        name="items[{{ $index }}][discount_percentage]" step="0.01" min="0"
                                                        max="100" value="{{ $item->discount_percentage }}">
                                                </td>
                                                <td class="d-none">
                                                    <input type="text" class="form-control tax-display"
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
                                            <td colspan="5" class="text-end fw-bold">{{ __('sales.tax') }}</td>
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

    <template id="item-row-template">
        <tr>
            <td>
                <input type="hidden" name="items[INDEX][id]" value="">
                <div class="input-group">
                    <select class="form-select product-select" name="items[INDEX][product_id]" required>
                        <option value="">{{ __('common.select') }}</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->unit_price }}"
                                data-tax="{{ $product->tax_rate }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-outline-secondary product-search-btn" type="button" title="Search Product (F2)">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </td>
            <td>
                <input type="number" class="form-control quantity-input" name="items[INDEX][quantity]" step="0.001"
                    min="0.001" required>
            </td>
            <td>
                <input type="number" class="form-control price-input" name="items[INDEX][unit_price]" step="0.01" min="0"
                    readonly required>
            </td>
            <td>
                <input type="number" class="form-control discount-input" name="items[INDEX][discount_percentage]"
                    step="0.01" min="0" max="100" value="0">
            </td>
            <td class="d-none">
                <input type="text" class="form-control tax-display" readonly>
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

            function addItem() {
                const template = document.getElementById('item-row-template').innerHTML;
                const html = template.replace(/INDEX/g, itemIndex++);
                $('#items-table tbody').append(html);
            }

            $('#add-item').click(function () {
                addItem();
            });

            $(document).on('click', '.remove-item', function () {
                $(this).closest('tr').remove();
                calculateTotals();
            });

            // Search button click - focus on the product select
            $(document).on('click', '.product-search-btn', function () {
                const row = $(this).closest('tr');
                const productSelect = row.find('.product-select')[0];
                if (productSelect) {
                    productSelect.focus();
                    // Use showPicker() to open dropdown
                    if (typeof productSelect.showPicker === 'function') {
                        try {
                            productSelect.showPicker();
                        } catch (err) {
                            console.log('showPicker not supported or blocked');
                        }
                    }
                }
            });

            // Keyboard shortcut: F2 to focus on first empty product select
            $(document).on('keydown', function (e) {
                if (e.key === 'F2') {
                    e.preventDefault();
                    const emptySelect = $('.product-select:not([disabled])').filter(function () {
                        return $(this).val() === '';
                    }).first()[0];
                    if (emptySelect) {
                        emptySelect.focus();
                        // Use showPicker() to open dropdown
                        if (typeof emptySelect.showPicker === 'function') {
                            try {
                                emptySelect.showPicker();
                            } catch (err) {
                                console.log('showPicker not supported or blocked');
                            }
                        }
                    }
                }
            });

            $(document).on('change', '.product-select', function () {
                const selected = $(this).find(':selected');
                const price = selected.data('price') || 0;
                const tax = selected.data('tax') || defaultTaxRate;
                const row = $(this).closest('tr');

                row.find('.price-input').val(price);
                row.find('.tax-rate-input').val(tax);
                if (!row.find('.quantity-input').val()) {
                    row.find('.quantity-input').val(1);
                }

                calculateRow(row);
            });

            $(document).on('input', '.quantity-input, .price-input, .discount-input', function () {
                calculateRow($(this).closest('tr'));
            });

            function calculateRow(row) {
                const quantity = parseFloat(row.find('.quantity-input').val()) || 0;
                const price = parseFloat(row.find('.price-input').val()) || 0;
                const discountPercent = parseFloat(row.find('.discount-input').val()) || 0;
                const taxRate = parseFloat(row.find('.tax-rate-input').val()) || 0;

                const gross = quantity * price;
                const discount = gross * (discountPercent / 100);
                const taxable = gross - discount;
                const tax = taxable * (taxRate / 100);
                const total = taxable + tax;

                row.find('.tax-display').val(tax.toFixed(2));
                row.find('.total-display').val(total.toFixed(2));

                calculateTotals();
            }

            function calculateTotals() {
                let subtotal = 0;
                let taxAmount = 0;
                let grandTotal = 0;

                $('#items-table tbody tr').each(function () {
                    const row = $(this);
                    const quantity = parseFloat(row.find('.quantity-input').val()) || 0;
                    const price = parseFloat(row.find('.price-input').val()) || 0;
                    const discountPercent = parseFloat(row.find('.discount-input').val()) || 0;
                    const taxRate = parseFloat(row.find('.tax-rate-input').val()) || 0;

                    const gross = quantity * price;
                    const discount = gross * (discountPercent / 100);
                    const taxable = gross - discount;
                    const tax = taxable * (taxRate / 100);

                    subtotal += taxable;
                    taxAmount += tax;
                });

                grandTotal = subtotal + taxAmount;

                $('#subtotal').text(subtotal.toFixed(2));
                $('#tax_amount').text(taxAmount.toFixed(2));
                $('#grand_total').text(grandTotal.toFixed(2));
            }
        });
    </script>
@endpush
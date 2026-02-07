@extends('layouts.app')

@section('title', __('messages.edit_quotation'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.edit_quotation') }}: {{ $quotation->document_number }}</h1>
            <a href="{{ route('sales.quotations.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
            </a>
        </div>

        <form action="{{ route('sales.quotations.update', $quotation) }}" method="POST" id="quotationForm">
            @csrf
            @method('PUT')
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <!-- Header Information -->
                        <div class="col-md-4 mb-3">
                            <label for="document_number" class="form-label">{{ __('messages.document_number') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('document_number') is-invalid @enderror"
                                id="document_number" name="document_number"
                                value="{{ old('document_number', $quotation->document_number) }}" required>
                            @error('document_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="quotation_date" class="form-label">{{ __('messages.date') }} <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('quotation_date') is-invalid @enderror"
                                id="quotation_date" name="quotation_date"
                                value="{{ old('quotation_date', $quotation->quotation_date ? $quotation->quotation_date->format('Y-m-d') : '') }}"
                                required>
                            @error('quotation_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="expiry_date" class="form-label">{{ __('messages.expiry_date') }} <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('expiry_date') is-invalid @enderror"
                                id="expiry_date" name="expiry_date"
                                value="{{ old('expiry_date', $quotation->expiry_date ? $quotation->expiry_date->format('Y-m-d') : '') }}"
                                required>
                            @error('expiry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="customer_id" class="form-label">{{ __('messages.customer') }} <span
                                    class="text-danger">*</span></label>
                            <select class="form-control @error('customer_id') is-invalid @enderror" id="customer_id"
                                name="customer_id" required>
                                <option value="">{{ __('messages.select_customer') }}</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id', $quotation->customer_id) == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="branch_id" class="form-label">{{ __('messages.branch') }} <span
                                    class="text-danger">*</span></label>
                            <select class="form-control @error('branch_id') is-invalid @enderror" id="branch_id"
                                name="branch_id" required>
                                <option value="">{{ __('messages.select_branch') }}</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id', $quotation->branch_id) == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="warehouse_id" class="form-label">{{ __('messages.warehouse') }}</label>
                            <select class="form-control @error('warehouse_id') is-invalid @enderror" id="warehouse_id"
                                name="warehouse_id">
                                <option value="">{{ __('messages.select_warehouse') }}</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $quotation->warehouse_id) == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('warehouse_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="salesman_id" class="form-label">{{ __('messages.salesman') }}</label>
                            <select class="form-control @error('salesman_id') is-invalid @enderror" id="salesman_id"
                                name="salesman_id">
                                <option value="">{{ __('messages.select_salesman') }}</option>
                                @foreach($salesmen as $salesman)
                                    <option value="{{ $salesman->id }}" {{ old('salesman_id', $quotation->salesman_id) == $salesman->id ? 'selected' : '' }}>
                                        {{ $salesman->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('salesman_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">{{ __('messages.status') }} <span
                                    class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status"
                                required>
                                <option value="draft" {{ old('status', $quotation->status) == 'draft' ? 'selected' : '' }}>
                                    {{ __('messages.draft') }}</option>
                                <option value="sent" {{ old('status', $quotation->status) == 'sent' ? 'selected' : '' }}>
                                    {{ __('messages.sent') }}</option>
                                <option value="accepted" {{ old('status', $quotation->status) == 'accepted' ? 'selected' : '' }}>{{ __('messages.accepted') }}</option>
                                <option value="rejected" {{ old('status', $quotation->status) == 'rejected' ? 'selected' : '' }}>{{ __('messages.rejected') }}</option>
                                <option value="expired" {{ old('status', $quotation->status) == 'expired' ? 'selected' : '' }}>{{ __('messages.expired') }}</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('messages.items') }}</h5>
                    <button type="button" class="btn btn-sm btn-success" id="addItemBtn">
                        <i class="fas fa-plus"></i> {{ __('messages.add_item') }}
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0" id="itemsTable">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 30%">{{ __('messages.product') }}</th>
                                    <th style="width: 15%">{{ __('messages.quantity') }}</th>
                                    <th style="width: 20%">{{ __('messages.unit_price') }}</th>
                                    <th style="width: 15%">{{ __('messages.tax') }} (%)</th>
                                    <th style="width: 15%">{{ __('messages.total') }}</th>
                                    <th style="width: 5%"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <!-- Items will be added here via JS -->
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">{{ __('messages.subtotal') }}</td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm text-end bg-white"
                                            name="subtotal" id="subtotal" readonly
                                            value="{{ number_format($quotation->subtotal, 2, '.', '') }}">
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">{{ __('messages.tax_amount') }}</td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm text-end bg-white"
                                            name="tax_amount" id="tax_total" readonly
                                            value="{{ number_format($quotation->tax_amount, 2, '.', '') }}">
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">{{ __('messages.grand_total') }}</td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm text-end bg-white fw-bold"
                                            name="total_amount" id="grand_total" readonly
                                            value="{{ number_format($quotation->total_amount, 2, '.', '') }}">
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

            <div class="card mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="terms_conditions" class="form-label">{{ __('messages.terms_conditions') }}</label>
                        <textarea class="form-control" id="terms_conditions" name="terms_conditions"
                            rows="3">{{ old('terms_conditions', $quotation->terms_conditions) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">{{ __('messages.notes') }}</label>
                        <textarea class="form-control" id="notes" name="notes"
                            rows="3">{{ old('notes', $quotation->notes) }}</textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('messages.update') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addItemBtn = document.getElementById('addItemBtn');
            const itemsBody = document.getElementById('itemsBody');
            let itemIndex = 0;

            const products = @json($products);
            const existingItems = @json($quotation->items);

            function addItem(data = null) {
                const index = itemIndex++;
                const tr = document.createElement('tr');

                let productOptions = '<option value="">{{ __("messages.select_product") }}</option>';
                products.forEach(p => {
                    const selected = data && data.product_id == p.id ? 'selected' : '';
                    productOptions += `<option value="${p.id}" data-price="${p.sale_price}" ${selected}>${p.name_en}</option>`;
                });

                tr.innerHTML = `
                    <td>
                        <select class="form-control form-control-sm product-select" name="items[${index}][product_id]" required>
                            ${productOptions}
                        </select>
                    </td>
                    <td>
                        <input type="number" step="0.01" class="form-control form-control-sm quantity-input" name="items[${index}][quantity]" value="${data ? data.quantity : 1}" required min="0.001">
                    </td>
                    <td>
                        <input type="number" step="0.01" class="form-control form-control-sm price-input" name="items[${index}][unit_price]" value="${data ? data.unit_price : 0}" required min="0">
                    </td>
                    <td>
                        <input type="number" step="0.01" class="form-control form-control-sm tax-input" name="items[${index}][tax_rate]" value="${data ? data.tax_rate : 0}" min="0">
                        <input type="hidden" name="items[${index}][tax_amount]" class="tax-amount-input" value="${data ? data.tax_amount : 0}">
                    </td>
                    <td>
                        <input type="number" step="0.01" class="form-control form-control-sm total-input bg-white" name="items[${index}][net_amount]" readonly value="${data ? data.net_amount : 0}">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger remove-item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;

                itemsBody.appendChild(tr);

                // Event listeners
                const inputs = tr.querySelectorAll('input, select');
                inputs.forEach(input => {
                    input.addEventListener('change', () => calculateRow(tr));
                    input.addEventListener('input', () => calculateRow(tr));
                });

                tr.querySelector('.product-select').addEventListener('change', function () {
                    const option = this.options[this.selectedIndex];
                    const price = option.getAttribute('data-price');
                    if (price) {
                        tr.querySelector('.price-input').value = price;
                        calculateRow(tr);
                    }
                });

                tr.querySelector('.remove-item').addEventListener('click', function () {
                    tr.remove();
                    calculateTotals();
                });

                if (!data) calculateRow(tr); // Calc logic for new rows, existing data already has values but we may want to recalc to be safe
                else calculateRow(tr);
            }

            function calculateRow(tr) {
                const quantity = parseFloat(tr.querySelector('.quantity-input').value) || 0;
                const price = parseFloat(tr.querySelector('.price-input').value) || 0;
                const taxRate = parseFloat(tr.querySelector('.tax-input').value) || 0;

                const netAmount = quantity * price;
                const taxAmount = netAmount * (taxRate / 100);

                tr.querySelector('.total-input').value = netAmount.toFixed(2);
                tr.querySelector('.tax-amount-input').value = taxAmount.toFixed(2);

                calculateTotals();
            }

            function calculateTotals() {
                let subtotal = 0;
                let taxTotal = 0;

                document.querySelectorAll('#itemsBody tr').forEach(tr => {
                    const netAmount = parseFloat(tr.querySelector('.total-input').value) || 0;
                    const taxAmount = parseFloat(tr.querySelector('.tax-amount-input').value) || 0;

                    subtotal += netAmount;
                    taxTotal += taxAmount;
                });

                document.getElementById('subtotal').value = subtotal.toFixed(2);
                document.getElementById('tax_total').value = taxTotal.toFixed(2);
                document.getElementById('grand_total').value = (subtotal + taxTotal).toFixed(2);
            }

            addItemBtn.addEventListener('click', () => addItem());

            // Load existing items or empty row
            if (existingItems && existingItems.length > 0) {
                existingItems.forEach(item => addItem(item));
                calculateTotals();
            } else {
                addItem();
            }
        });
    </script>
@endsection
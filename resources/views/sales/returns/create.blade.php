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
                                    <label for="sales_invoice_id"
                                        class="form-label">{{ __('messages.sales_invoice') ?? 'Sales Invoice' }}</label>
                                    <select class="form-select @error('sales_invoice_id') is-invalid @enderror" name="sales_invoice_id" id="sales_invoice_id">
                                        <option value="">-- {{ __('messages.select_invoice') ?? 'Select Invoice' }} --
                                        </option>
                                        @foreach($invoices as $invoice)
                                            <option value="{{ $invoice->id }}" {{ old('sales_invoice_id') == $invoice->id ? 'selected' : '' }}>
                                                {{ $invoice->invoice_number }} ({{ $invoice->customer->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('sales_invoice_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="return_type" class="form-label">{{ __('messages.return_type') ?? 'Return Type' }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('return_type') is-invalid @enderror" name="return_type" id="return_type" required>
                                        <option value="credit" {{ old('return_type', 'credit') == 'credit' ? 'selected' : '' }}>{{ __('messages.credit') ?? 'Credit' }}</option>
                                        <option value="cash" {{ old('return_type') == 'cash' ? 'selected' : '' }}>{{ __('messages.cash') ?? 'Cash' }}</option>
                                    </select>
                                    @error('return_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 mb-3 d-none" id="bank_account_container">
                                    <label for="bank_account_id" class="form-label">{{ __('messages.bank_account') ?? 'Bank/Cash Account' }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('bank_account_id') is-invalid @enderror" name="bank_account_id" id="bank_account_id">
                                        <option value="">-- {{ __('messages.select_account') ?? 'Select Account' }} --</option>
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
                                <div class="col-md-4 mb-3">
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

                                <div class="col-md-4 mb-3">
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

                                <div class="col-md-4 mb-3">
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
                    <select name="items[INDEX][product_id]" class="form-select product-select" required>
                        <option value="">-- {{ __('messages.select_product') }} --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->sale_price }}">
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
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

            function addItem(data = null) {
                const html = template.replace(/INDEX/g, itemIndex++);
                tableBody.insertAdjacentHTML('beforeend', html);
                const row = tableBody.lastElementChild;
                initRow(row);

                if (data) {
                    const productSelect = row.querySelector('.product-select');
                    const quantityInput = row.querySelector('.quantity-input');
                    const priceInput = row.querySelector('.price-input');

                    productSelect.value = data.product_id;
                    quantityInput.value = data.quantity;
                    priceInput.value = data.unit_price;
                    calculateRow(row);
                }
            }

            function initRow(row) {
                const productSelect = row.querySelector('.product-select');
                const quantityInput = row.querySelector('.quantity-input');
                const priceInput = row.querySelector('.price-input');
                const removeBtn = row.querySelector('.remove-item');

                productSelect.addEventListener('change', function () {
                    const selected = this.options[this.selectedIndex];
                    const price = selected.dataset.price || 0;
                    priceInput.value = price;
                    calculateRow(row);
                });

                quantityInput.addEventListener('input', () => calculateRow(row));
                priceInput.addEventListener('input', () => calculateRow(row));
                removeBtn.addEventListener('click', () => {
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

            invoiceSelect.addEventListener('change', function () {
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
                        document.getElementById('customer_id').value = data.customer_id;
                        document.getElementById('branch_id').value = data.branch_id;
                        document.getElementById('warehouse_id').value = data.warehouse_id;

                        // Clear and populate items
                        tableBody.innerHTML = '';
                        itemIndex = 0;
                        data.items.forEach(item => {
                            addItem(item);
                        });
                    })
                    .catch(error => console.error('Error fetching invoice data:', error));
            });

            // Handle Return Type Change
            const returnTypeSelect = document.getElementById('return_type');
            const bankAccountContainer = document.getElementById('bank_account_container');
            const customerAsterisk = document.getElementById('customer_asterisk');
            const customerSelect = document.getElementById('customer_id');

            function toggleReturnFields() {
                if (returnTypeSelect.value === 'cash') {
                    // Cash Return
                    bankAccountContainer.classList.remove('d-none');
                    document.getElementById('bank_account_id').setAttribute('required', 'required');
                    
                    customerAsterisk.classList.add('d-none');
                    customerSelect.removeAttribute('required');
                } else {
                    // Credit Return
                    bankAccountContainer.classList.add('d-none');
                    document.getElementById('bank_account_id').removeAttribute('required');
                    
                    customerAsterisk.classList.remove('d-none');
                    customerSelect.setAttribute('required', 'required');
                }
            }

            returnTypeSelect.addEventListener('change', toggleReturnFields);
            toggleReturnFields(); // Initial check

            document.getElementById('add-item-btn').addEventListener('click', () => addItem());

            // Add initial row if not editing/fetching
            if (!invoiceSelect.value) {
                addItem();
            }
        });
    </script>
@endpush
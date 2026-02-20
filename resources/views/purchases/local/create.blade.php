@extends('layouts.app')

@section('title', __('local_purchase.create'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('local_purchase.create') }}</h1>
            <div class="d-flex gap-2">
                <input type="file" id="invoiceScanInput" accept="image/*" class="d-none">
                <button type="button" class="btn btn-info" id="scanInvoiceBtn">
                    <i class="fas fa-magic"></i> {{ __('local_purchase.scan_invoice') }}
                </button>
                <a href="{{ route('purchases.local-purchases.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> {{ __('general.back') }}
                </a>
            </div>
        </div>

        <form action="{{ route('purchases.local-purchases.store') }}" method="POST" id="localPurchaseForm">
            @csrf
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('local_purchase.supplier_info') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="supplier_name" class="form-label">{{ __('local_purchase.supplier_name') }}
                                        *</label>
                                    <input type="text" class="form-control @error('supplier_name') is-invalid @enderror"
                                        id="supplier_name" name="supplier_name" value="{{ old('supplier_name') }}" required>
                                    @error('supplier_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="supplier_phone"
                                        class="form-label">{{ __('local_purchase.supplier_phone') }}</label>
                                    <input type="text" class="form-control @error('supplier_phone') is-invalid @enderror"
                                        id="supplier_phone" name="supplier_phone" value="{{ old('supplier_phone') }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="supplier_email"
                                        class="form-label">{{ __('local_purchase.supplier_email') }}</label>
                                    <input type="email" class="form-control @error('supplier_email') is-invalid @enderror"
                                        id="supplier_email" name="supplier_email" value="{{ old('supplier_email') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="invoice_number" class="form-label">{{ __('local_purchase.invoice_number') }}
                                        *</label>
                                    <input type="text" class="form-control @error('invoice_number') is-invalid @enderror"
                                        id="invoice_number" name="invoice_number" value="{{ old('invoice_number') }}"
                                        required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="invoice_date" class="form-label">{{ __('local_purchase.invoice_date') }}
                                        *</label>
                                    <input type="date" class="form-control @error('invoice_date') is-invalid @enderror"
                                        id="invoice_date" name="invoice_date"
                                        value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="supplier_address"
                                        class="form-label">{{ __('local_purchase.supplier_address') }}</label>
                                    <textarea class="form-control @error('supplier_address') is-invalid @enderror"
                                        id="supplier_address" name="supplier_address"
                                        rows="2">{{ old('supplier_address') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('local_purchase.items') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table" id="itemsTable">
                                    <thead>
                                        <tr>
                                            <th>{{ __('local_purchase.item') }}</th>
                                            <th>{{ __('local_purchase.quantity') }}</th>
                                            <th>{{ __('local_purchase.unit_price') }}</th>
                                            <th>{{ __('local_purchase.discount') }}</th>
                                            <th>{{ __('local_purchase.tax_rate') }}</th>
                                            <th>{{ __('local_purchase.total') }}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsBody">
                                        <!-- Items will be added here -->
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-sm btn-success" id="addItemBtn">
                                <i class="fas fa-plus"></i> {{ __('local_purchase.add_item') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('local_purchase.document_info') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="branch_id" class="form-label">{{ __('local_purchase.branch') }} *</label>
                                <select class="form-select @error('branch_id') is-invalid @enderror" id="branch_id"
                                    name="branch_id" required>
                                    <option value="">{{ __('general.select') }}</option>
                                    @foreach(\App\Models\Branch::where('is_active', true)->get() as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="warehouse_id" class="form-label">{{ __('local_purchase.warehouse') }} *</label>
                                <select class="form-select @error('warehouse_id') is-invalid @enderror" id="warehouse_id"
                                    name="warehouse_id" required>
                                    <option value="">{{ __('general.select') }}</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                            {{ $warehouse->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('warehouse_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">{{ __('local_purchase.notes') }}</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes"
                                    rows="3">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('local_purchase.totals') }}</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td>{{ __('local_purchase.subtotal') }}</td>
                                    <td class="text-end" id="subtotal">0.00</td>
                                </tr>
                                <tr>
                                    <td>{{ __('local_purchase.discount') }}</td>
                                    <td class="text-end" id="totalDiscount">0.00</td>
                                </tr>
                                <tr>
                                    <td>{{ __('local_purchase.tax') }}</td>
                                    <td class="text-end" id="totalTax">0.00</td>
                                </tr>
                                <tr class="fw-bold">
                                    <td>{{ __('local_purchase.total') }}</td>
                                    <td class="text-end" id="grandTotal">0.00</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i> {{ __('general.save') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <template id="itemRowTemplate">
        <tr class="item-row">
            <td>
                <select class="form-select item-select" name="items[INDEX][product_id]" required>
                    <option value="">{{ __('general.select_item') }}</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->purchase_price ?? $product->sale_price }}">
                            {{ $product->code }} - {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" class="form-control quantity" name="items[INDEX][quantity]" value="1" min="0.01"
                    step="0.01" required>
            </td>
            <td>
                <input type="number" class="form-control unit-price" name="items[INDEX][unit_price]" value="0" min="0"
                    step="0.01" required>
            </td>
            <td>
                <input type="number" class="form-control discount" name="items[INDEX][discount_amount]" value="0" min="0"
                    step="0.01">
            </td>
            <td>
                <select class="form-select tax-rate" name="items[INDEX][tax_rate]">
                    <option value="0">0%</option>
                    <option value="5">5%</option>
                    <option value="15" selected>15%</option>
                </select>
            </td>
            <td class="item-total">0.00</td>
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
            let itemIndex = 0;
            const itemsBody = document.getElementById('itemsBody');
            const template = document.getElementById('itemRowTemplate');
            const addItemBtn = document.getElementById('addItemBtn');

            function addItemRow() {
                const clone = template.content.cloneNode(true);
                const row = clone.querySelector('tr');

                // Update indices
                row.querySelectorAll('[name*="INDEX"]').forEach(el => {
                    el.name = el.name.replace('INDEX', itemIndex);
                });

                // Add event listeners
                row.querySelector('.remove-item').addEventListener('click', function () {
                    row.remove();
                    calculateTotals();
                });

                row.querySelector('.item-select').addEventListener('change', function () {
                    const option = this.options[this.selectedIndex];
                    const price = option.dataset.price || 0;
                    row.querySelector('.unit-price').value = price;
                    calculateRowTotal(row);
                });

                ['quantity', 'unit-price', 'discount', 'tax-rate'].forEach(cls => {
                    row.querySelector('.' + cls).addEventListener('input', function () {
                        calculateRowTotal(row);
                    });
                });

                itemsBody.appendChild(row);
                itemIndex++;
            }

            function calculateRowTotal(row) {
                const qty = parseFloat(row.querySelector('.quantity').value) || 0;
                const price = parseFloat(row.querySelector('.unit-price').value) || 0;
                const discount = parseFloat(row.querySelector('.discount').value) || 0;
                const taxRate = parseFloat(row.querySelector('.tax-rate').value) || 0;

                const gross = qty * price;
                const grossAfterDiscount = gross - discount;
                const net = grossAfterDiscount / (1 + (taxRate / 100));
                const tax = grossAfterDiscount - net;

                row.querySelector('.item-total').textContent = grossAfterDiscount.toFixed(2);
                calculateTotals();
            }

            function calculateTotals() {
                let subtotal = 0;
                let totalDiscount = 0;
                let totalTax = 0;
                let grandTotal = 0;

                document.querySelectorAll('.item-row').forEach(row => {
                    const qty = parseFloat(row.querySelector('.quantity').value) || 0;
                    const price = parseFloat(row.querySelector('.unit-price').value) || 0;
                    const discount = parseFloat(row.querySelector('.discount').value) || 0;
                    const taxRate = parseFloat(row.querySelector('.tax-rate').value) || 0;

                    const gross = qty * price;
                    const grossAfterDiscount = gross - discount;
                    const net = grossAfterDiscount / (1 + (taxRate / 100));
                    const tax = grossAfterDiscount - net;

                    subtotal += gross;
                    totalDiscount += discount;
                    totalTax += tax;
                    grandTotal += grossAfterDiscount;
                });

                document.getElementById('subtotal').textContent = subtotal.toFixed(2);
                document.getElementById('totalDiscount').textContent = totalDiscount.toFixed(2);
                document.getElementById('totalTax').textContent = totalTax.toFixed(2);
                document.getElementById('grandTotal').textContent = grandTotal.toFixed(2);
            }

            addItemBtn.addEventListener('click', addItemRow);

            // Scanning Logic
            const scanBtn = document.getElementById('scanInvoiceBtn');
            const scanInput = document.getElementById('invoiceScanInput');

            scanBtn.addEventListener('click', () => scanInput.click());

            let scannedImagePath = null; // Store the scanned image path
            
            scanInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const formData = new FormData();
                    formData.append('invoice_image', this.files[0]);
                    formData.append('_token', '{{ csrf_token() }}');

                    // Show loading state
                    scanBtn.disabled = true;
                    scanBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("local_purchase.scanning") }}...';

                    fetch('{{ route("purchases.ai.scan") }}', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Store scanned image path for later use
                            scannedImagePath = data.data.image_path;
                            
                            // Pre-fill main fields
                            if (data.data.supplier_name) document.getElementById('supplier_name').value = data.data.supplier_name;
                            if (data.data.invoice_number) document.getElementById('invoice_number').value = data.data.invoice_number;
                            if (data.data.invoice_date) document.getElementById('invoice_date').value = data.data.invoice_date;

                            // Add items
                            if (data.data.items && data.data.items.length > 0) {
                                // Clear existing empty items if any
                                const rows = document.querySelectorAll('.item-row');
                                if (rows.length === 1) {
                                    const firstRow = rows[0];
                                    if (!firstRow.querySelector('.item-select').value) {
                                        firstRow.remove();
                                    }
                                }

                                data.data.items.forEach(item => {
                                    const clone = template.content.cloneNode(true);
                                    const row = clone.querySelector('tr');
                                    
                                    row.querySelectorAll('[name*="INDEX"]').forEach(el => {
                                        el.name = el.name.replace('INDEX', itemIndex);
                                    });

                                    // Pre-select product if product_id is provided
                                    const productSelect = row.querySelector('.item-select');
                                    if (item.product_id) {
                                        productSelect.value = item.product_id;
                                    }

                                    // Pre-fill item row data
                                    row.querySelector('.quantity').value = item.quantity;
                                    row.querySelector('.unit-price').value = item.unit_price;
                                    row.querySelector('.discount').value = item.discount_amount || 0;
                                    row.querySelector('.tax-rate').value = item.tax_rate || 15;

                                    // Add event listeners (standard item logic)
                                    row.querySelector('.remove-item').addEventListener('click', function () {
                                        row.remove();
                                        calculateTotals();
                                    });
                                    row.querySelector('.item-select').addEventListener('change', function () {
                                        const option = this.options[this.selectedIndex];
                                        const price = option.dataset.price || 0;
                                        row.querySelector('.unit-price').value = price;
                                        calculateRowTotal(row);
                                    });
                                    ['quantity', 'unit-price', 'discount', 'tax-rate'].forEach(cls => {
                                        row.querySelector('.' + cls).addEventListener('input', function () {
                                            calculateRowTotal(row);
                                        });
                                    });

                                    itemsBody.appendChild(row);
                                    calculateRowTotal(row);
                                    itemIndex++;
                                });
                            }

                            Swal.fire({
                                icon: 'success',
                                title: '{{ __("local_purchase.scan_complete") }}',
                                text: '{{ __("local_purchase.scan_success_msg") }}',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __("general.error") }}',
                                text: data.message || '{{ __("local_purchase.scan_failed") }}'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Scan Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("general.error") }}',
                            text: '{{ __("local_purchase.scan_failed") }}'
                        });
                    })
                    .finally(() => {
                        scanBtn.disabled = false;
                        scanBtn.innerHTML = '<i class="fas fa-magic"></i> {{ __("local_purchase.scan_invoice") }}';
                        scanInput.value = ''; // Reset input
                    });
                }
            });

            // Save scanned invoice to vendor on form submit
            const form = document.getElementById('localPurchaseForm');
            form.addEventListener('submit', function(e) {
                if (scannedImagePath) {
                    e.preventDefault();
                    
                    // First save the invoice image to vendor
                    const vendorFormData = new FormData();
                    vendorFormData.append('vendor_id', document.getElementById('vendor_id')?.value || '');
                    vendorFormData.append('image_path', scannedImagePath);
                    vendorFormData.append('invoice_number', document.getElementById('invoice_number').value);
                    vendorFormData.append('_token', '{{ csrf_token() }}');
                    
                    fetch('{{ route("purchases.ai.save-to-vendor") }}', {
                        method: 'POST',
                        body: vendorFormData
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Continue with form submission regardless of whether save succeeded
                        scannedImagePath = null; // Clear to avoid duplicate saves
                        form.submit();
                    })
                    .catch(error => {
                        console.error('Save to vendor error:', error);
                        // Still submit the form
                        scannedImagePath = null;
                        form.submit();
                    });
                }
            });

            // Add first item row
            addItemRow();
        });
    </script>
@endpush
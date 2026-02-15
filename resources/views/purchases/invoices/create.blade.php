@extends('layouts.app')

@section('title', __('messages.create_purchase_invoice'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.create_purchase_invoice') }}</h1>
            <a href="{{ route('purchases.invoices.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
            </a>
        </div>

        <form action="{{ route('purchases.invoices.store') }}" method="POST" id="invoice-form">
            @csrf
            <div class="row">
                <div class="col-md-9">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="vendor_id" class="form-label">{{ __('messages.vendor') }} <span class="text-danger">*</span></label>
                                    <select class="form-control select2 @error('vendor_id') is-invalid @enderror" id="vendor_id" name="vendor_id" required>
                                        <option value="">{{ __('messages.select_vendor') }}</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                                {{ $vendor->name_en }} / {{ $vendor->name_ar }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vendor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="invoice_number" class="form-label">{{ __('messages.invoice_number') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('invoice_number') is-invalid @enderror" id="invoice_number" name="invoice_number" value="{{ old('invoice_number') }}" required>
                                    @error('invoice_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="document_number" class="form-label">{{ __('messages.document_number') }}</label>
                                    <input type="text" class="form-control" id="document_number" value="{{ $nextDocumentNumber }}" readonly>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="invoice_date" class="form-label">{{ __('messages.invoice_date') }} <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('invoice_date') is-invalid @enderror" id="invoice_date" name="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                                    @error('invoice_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="due_date" class="form-label">{{ __('messages.due_date') }}</label>
                                    <input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date') }}">
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="payment_terms" class="form-label">{{ __('messages.payment_terms') }} <span class="text-danger">*</span></label>
                                    <select class="form-control @error('payment_terms') is-invalid @enderror" id="payment_terms" name="payment_terms" required>
                                        <option value="cash" {{ old('payment_terms') == 'cash' ? 'selected' : '' }}>{{ __('messages.cash') }}</option>
                                        <option value="credit" {{ old('payment_terms') == 'credit' ? 'selected' : '' }}>{{ __('messages.credit') }}</option>
                                        <option value="installment" {{ old('payment_terms') == 'installment' ? 'selected' : '' }}>{{ __('messages.installment') }}</option>
                                    </select>
                                    @error('payment_terms')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ __('messages.items') }}</h5>
                            <button type="button" class="btn btn-sm btn-success" id="add-item">
                                <i class="fas fa-plus"></i> {{ __('messages.add_item') }}
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0" id="items-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 40%">{{ __('messages.product') }}</th>
                                            <th style="width: 15%">{{ __('messages.quantity') }}</th>
                                            <th style="width: 15%">{{ __('messages.unit_price') }}</th>
                                            <th style="width: 10%">{{ __('messages.tax') }} %</th>
                                            <th style="width: 15%">{{ __('messages.total') }}</th>
                                            <th style="width: 5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Item rows will be added here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <label for="notes" class="form-label">{{ __('messages.notes') }}</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('messages.location') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="branch_id" class="form-label">{{ __('messages.branch') }} <span class="text-danger">*</span></label>
                                <select class="form-control @error('branch_id') is-invalid @enderror" id="branch_id" name="branch_id" required>
                                    <option value="">{{ __('messages.select_branch') }}</option>
                                    @foreach($branches as $branch)
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
                                <label for="warehouse_id" class="form-label">{{ __('messages.warehouse') }} <span class="text-danger">*</span></label>
                                <select class="form-control @error('warehouse_id') is-invalid @enderror" id="warehouse_id" name="warehouse_id" required>
                                    <option value="">{{ __('messages.select_warehouse') }}</option>
                                    <!-- Populated via AJAX -->
                                </select>
                                @error('warehouse_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('messages.summary') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ __('messages.subtotal') }}</span>
                                <span id="summary-subtotal">0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ __('messages.tax_amount') }}</span>
                                <span id="summary-tax">0.00</span>
                            </div>
                            <div class="mb-2">
                                <label for="discount_amount" class="form-label">{{ __('messages.discount') }}</label>
                                <input type="number" step="0.01" class="form-control form-control-sm" id="discount_amount" name="discount_amount" value="{{ old('discount_amount', 0) }}">
                            </div>
                            <div class="mb-2">
                                <label for="shipping_amount" class="form-label">{{ __('messages.shipping') }}</label>
                                <input type="number" step="0.01" class="form-control form-control-sm" id="shipping_amount" name="shipping_amount" value="{{ old('shipping_amount', 0) }}">
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold h5">
                                <span>{{ __('messages.total') }}</span>
                                <span id="summary-total">0.00</span>
                                <input type="hidden" name="total_amount" id="total_amount_input">
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save"></i> {{ __('messages.save_invoice') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Row Template -->
    <template id="item-row-template">
        <tr class="item-row">
            <td>
                <select name="items[INDEX][product_id]" class="form-control product-select select2" required>
                    <option value="">{{ __('messages.select_product') }}</option>
                </select>
            </td>
            <td>
                <input type="number" name="items[INDEX][quantity]" class="form-control quantity" step="0.01" min="0.01" value="1" required>
            </td>
            <td>
                <input type="number" name="items[INDEX][unit_price]" class="form-control unit-price" step="0.01" min="0" value="0" required>
            </td>
            <td>
                <input type="number" name="items[INDEX][tax_rate]" class="form-control tax-rate" step="0.01" min="0" value="15">
            </td>
            <td class="text-end">
                <span class="row-total">0.00</span>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-row">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>
    </template>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single {
            height: 38px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            let rowIndex = 0;

            function addItemRow() {
                const template = document.getElementById('item-row-template').innerHTML;
                const html = template.replace(/INDEX/g, rowIndex);
                $('#items-table tbody').append(html);

                const $row = $('#items-table tbody tr').last();
                
                // Initialize Select2 for product search
                $row.find('.product-select').select2({
                    ajax: {
                        url: "{{ route('ajax.products.search') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return { q: params.term };
                        },
                        processResults: function (data) {
                            return {
                                results: data.map(function (item) {
                                    return {
                                        id: item.id,
                                        text: item.code + ' - ' + item.name_en,
                                        price: item.cost_price || item.sale_price
                                    };
                                })
                            };
                        }
                    }
                }).on('select2:select', function(e) {
                    const data = e.params.data;
                    $(this).closest('tr').find('.unit-price').val(data.price);
                    calculateTotals();
                });

                rowIndex++;
                calculateTotals();
            }

            // Add first row by default
            addItemRow();

            $('#add-item').click(function() {
                addItemRow();
            });

            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                calculateTotals();
            });

            $(document).on('input', '.quantity, .unit-price, .tax-rate, #discount_amount, #shipping_amount', function() {
                calculateTotals();
            });

            function calculateTotals() {
                let subtotal = 0;
                let totalTax = 0;

                $('.item-row').each(function() {
                    const qty = parseFloat($(this).find('.quantity').val()) || 0;
                    const price = parseFloat($(this).find('.unit-price').val()) || 0;
                    const taxRate = parseFloat($(this).find('.tax-rate').val()) || 0;

                    const rowSubtotal = qty * price;
                    const rowTax = rowSubtotal * (taxRate / 100);
                    const rowTotal = rowSubtotal + rowTax;

                    subtotal += rowSubtotal;
                    totalTax += rowTax;

                    $(this).find('.row-total').text(rowTotal.toFixed(2));
                });

                const discount = parseFloat($('#discount_amount').val()) || 0;
                const shipping = parseFloat($('#shipping_amount').val()) || 0;
                const total = subtotal + totalTax + shipping - discount;

                $('#summary-subtotal').text(subtotal.toFixed(2));
                $('#summary-tax').text(totalTax.toFixed(2));
                $('#summary-total').text(total.toFixed(2));
                $('#total_amount_input').val(total.toFixed(2));
            }

            // Branch to Warehouse AJAX
            $('#branch_id').change(function() {
                const branchId = $(this).val();
                const $warehouseSelect = $('#warehouse_id');
                
                $warehouseSelect.empty().append('<option value="">{{ __("messages.select_warehouse") }}</option>');
                
                if (branchId) {
                    $.get("{{ route('ajax.warehouses.by-branch') }}", { branch_id: branchId }, function(data) {
                        data.forEach(function(warehouse) {
                            $warehouseSelect.append(`<option value="${warehouse.id}">${warehouse.name_en}</option>`);
                        });
                    });
                }
            });

            // Trigger branch change to populate warehouses if old value exists
            if ($('#branch_id').val()) {
                $('#branch_id').trigger('change');
            }
        });
    </script>
@endpush

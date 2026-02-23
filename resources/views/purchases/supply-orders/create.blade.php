@extends('layouts.app')

@section('title', __('messages.create_supply_order'))

@section('content')
    <div class="container-fluid">
        <form action="{{ route('purchases.supply-orders.store') }}" method="POST" id="supplyOrderForm">
            @csrf
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">{{ __('messages.create_supply_order') }}</h1>
                    <p class="text-muted mb-0 small">{{ __('messages.supply_order_subtitle') }}</p>
                </div>
                <div class="d-flex">
                    <a href="{{ route('purchases.supply-orders.index') }}" class="btn btn-light border border-secondary text-secondary me-2">
                        <i class="fas fa-times me-1"></i> {{ __('messages.cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary shadow-sm" id="btnSave">
                        <i class="fas fa-save me-1"></i> {{ __('messages.save_order') }}
                    </button>
                </div>
            </div>

            <div class="row g-4">
                <!-- Header Info -->
                <div class="col-lg-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                            <h5 class="card-title fw-bold mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>{{ __('messages.basic_information') }}</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted">{{ __('messages.document_number') }}</label>
                                    <input type="text" name="document_number" class="form-control bg-light fw-bold" 
                                           value="{{ $documentNumber }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted">{{ __('messages.order_number') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="order_number" class="form-control" required 
                                           placeholder="e.g. PO-2024-001" value="{{ old('order_number') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted">{{ __('messages.order_date') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="order_date" class="form-control" required 
                                           value="{{ old('order_date', date('Y-m-d')) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted">{{ __('messages.expected_delivery_date') }}</label>
                                    <input type="date" name="expected_delivery_date" class="form-control" 
                                           value="{{ old('expected_delivery_date') }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted">{{ __('messages.vendor') }} <span class="text-danger">*</span></label>
                                    <select name="vendor_id" class="form-select select2" required>
                                        <option value="">{{ __('messages.select_vendor') }}</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                                {{ $vendor->code }} - {{ app()->getLocale() == 'ar' ? $vendor->name_ar : $vendor->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted">{{ __('messages.branch') }} <span class="text-danger">*</span></label>
                                    <select name="branch_id" id="branch_id" class="form-select select2" required>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ (old('branch_id') ?? session('active_branch_id')) == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted">{{ __('messages.warehouse') }} <span class="text-danger">*</span></label>
                                    <select name="warehouse_id" id="warehouse_id" class="form-select select2" required>
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
                    </div>
                </div>

                <!-- Items Table -->
                <div class="col-lg-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                            <h5 class="card-title fw-bold mb-0"><i class="fas fa-boxes me-2 text-primary"></i>{{ __('messages.order_items') }}</h5>
                            <button type="button" class="btn btn-sm btn-success rounded-pill px-3" id="btnAddRow">
                                <i class="fas fa-plus me-1"></i> {{ __('messages.add_item') }}
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-flush align-middle mb-0" id="itemsTable">
                                    <thead class="bg-light text-muted small text-uppercase fw-bold">
                                        <tr>
                                            <th class="px-4 py-3" style="width: 35%;">{{ __('messages.product') }}</th>
                                            <th class="py-3 text-center" style="width: 15%;">{{ __('messages.quantity') }}</th>
                                            <th class="py-3 text-center" style="width: 15%;">{{ __('messages.unit_price') }}</th>
                                            <th class="py-3 text-center" style="width: 10%;">{{ __('messages.discount') }} %</th>
                                            <th class="py-3 text-center" style="width: 15%;">{{ __('messages.total') }}</th>
                                            <th class="px-4 py-3 text-end" style="width: 5%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsBody">
                                        <tr class="item-row">
                                            <td class="px-4">
                                                <select name="items[0][product_id]" class="form-select select2-product" required data-placeholder="{{ __('messages.select_product') }}">
                                                    <option value=""></option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" 
                                                                data-price="{{ $product->purchase_price }}" 
                                                                data-tax="{{ $product->tax_rate ?? $taxSetting->default_tax_rate }}">
                                                            {{ $product->code }} - {{ $product->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="items[0][quantity]" class="form-control text-center qty-input" 
                                                       step="0.001" min="0.001" required value="1">
                                            </td>
                                            <td>
                                                <input type="number" name="items[0][unit_price]" class="form-control text-center price-input" 
                                                       step="0.01" min="0" required value="0.00">
                                            </td>
                                            <td>
                                                <input type="number" name="items[0][discount_percentage]" class="form-control text-center discount-input" 
                                                       step="0.01" min="0" max="100" value="0.00">
                                            </td>
                                            <td class="text-center fw-bold row-total">0.00</td>
                                            <td class="px-4 text-end">
                                                <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle btn-remove" disabled>
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer & Totals -->
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">{{ __('messages.terms_conditions') }}</label>
                                <textarea name="terms_conditions" rows="3" class="form-control" placeholder="{{ __('messages.terms_placeholder') }}"></textarea>
                            </div>
                            <div>
                                <label class="form-label small fw-bold text-muted">{{ __('messages.notes') }}</label>
                                <textarea name="notes" rows="3" class="form-control" placeholder="{{ __('messages.notes_placeholder') }}"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 bg-light">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">{{ __('messages.order_summary') }}</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">{{ __('messages.subtotal') }}</span>
                                <span id="summary-subtotal" class="fw-semibold text-dark">0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 text-danger">
                                <span>{{ __('messages.discount') }}</span>
                                <span id="summary-discount">0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">{{ __('messages.tax') }}</span>
                                <span id="summary-tax" class="fw-semibold text-dark">0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="text-muted">{{ __('messages.shipping') }}</span>
                                <div class="w-50">
                                    <input type="number" name="shipping_amount" class="form-control form-control-sm text-end" id="shipping_amount" step="0.01" min="0" value="0.00">
                                </div>
                            </div>
                            <hr class="my-4 border-2 border-primary">
                            <div class="d-flex justify-content-between align-items-center mb-0">
                                <h4 class="fw-bold mb-0 text-primary">{{ __('messages.total_amount') }}</h4>
                                <h4 class="fw-bold mb-0 text-primary" id="summary-total">0.00</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('styles')
<style>
    .select2-container--bootstrap-5 .select2-selection { border: 1px solid #dee2e6; border-radius: 0.5rem; height: calc(2.25rem + 2px); }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered { line-height: 2.25rem; }
    .form-control:focus, .form-select:focus { border-color: #2563eb; box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.1); }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        let rowCount = 1;

        // Initialize Select2
        function initSelect2() {
            $('.select2').select2({ theme: 'bootstrap-5', width: '100%' });
            $('.select2-product').select2({ 
                theme: 'bootstrap-5', 
                width: '100%',
                placeholder: "{{ __('messages.select_product') }}",
                allowClear: true
            });
        }
        initSelect2();

        // Branch and Warehouse dependencies
        $('#branch_id').on('change', function() {
            let branchId = $(this).val();
            if (branchId) {
                $.get("{{ route('ajax.warehouses.by-branch') }}", { branch_id: branchId }, function(data) {
                    let warehouseSelect = $('#warehouse_id');
                    warehouseSelect.empty().append('<option value="">{{ __("messages.select_warehouse") }}</option>');
                    $.each(data, function(key, warehouse) {
                        warehouseSelect.append('<option value="'+warehouse.id+'">'+warehouse.name+'</option>');
                    });
                });
            }
        });

        // Add Row
        $('#btnAddRow').on('click', function() {
            let newRow = $('.item-row:first').clone();
            
            // Update names and reset values
            newRow.find('select').attr('name', `items[${rowCount}][product_id]`).val('').removeClass('select2-hidden-accessible');
            newRow.find('.qty-input').attr('name', `items[${rowCount}][quantity]`).val(1);
            newRow.find('.price-input').attr('name', `items[${rowCount}][unit_price]`).val('0.00');
            newRow.find('.discount-input').attr('name', `items[${rowCount}][discount_percentage]`).val('0.00');
            newRow.find('.row-total').text('0.00');
            newRow.find('.btn-remove').prop('disabled', false);

            // Remove existing select2 container before appending
            newRow.find('.select2-container').remove();
            
            $('#itemsBody').append(newRow);
            if (window.initGlobalSelect2) window.initGlobalSelect2(newRow[0] || newRow);
            
            // Re-initialize select2 on the new row
            newRow.find('.select2-product').select2({ 
                theme: 'bootstrap-5', 
                width: '100%',
                placeholder: "{{ __('messages.select_product') }}",
                allowClear: true
            });
            
            rowCount++;
            calculateTotals();
        });

        // Remove Row
        $(document).on('click', '.btn-remove', function() {
            if ($('.item-row').length > 1) {
                $(this).closest('tr').remove();
                calculateTotals();
            }
        });

        // Product selection logic
        $(document).on('change', '.select2-product', function() {
            let selected = $(this).find(':selected');
            let price = selected.data('price') || 0;
            $(this).closest('tr').find('.price-input').val(parseFloat(price).toFixed(2));
            calculateTotals();
        });

        // Recalculate on input change
        $(document).on('input', '.qty-input, .price-input, .discount-input, #shipping_amount', function() {
            calculateTotals();
        });

        function calculateTotals() {
            let subtotal = 0;
            let totalDiscount = 0;
            let totalTax = 0;
            let shipping = parseFloat($('#shipping_amount').val()) || 0;

            $('.item-row').each(function() {
                let qty = parseFloat($(this).find('.qty-input').val()) || 0;
                let price = parseFloat($(this).find('.price-input').val()) || 0;
                let discountPercent = parseFloat($(this).find('.discount-input').val()) || 0;
                let taxRate = parseFloat($(this).find('.select2-product :selected').data('tax')) || 0;

                let lineGross = qty * price;
                let lineDiscount = lineGross * (discountPercent / 100);
                let lineNet = lineGross - lineDiscount;
                let lineTax = lineNet * (taxRate / 100);
                let lineTotal = lineNet + lineTax;

                $(this).find('.row-total').text(lineTotal.toFixed(2));

                subtotal += lineGross;
                totalDiscount += lineDiscount;
                totalTax += lineTax;
            });

            $('#summary-subtotal').text(subtotal.toFixed(2));
            $('#summary-discount').text(totalDiscount.toFixed(2));
            $('#summary-tax').text(totalTax.toFixed(2));
            $('#summary-total').text((subtotal - totalDiscount + totalTax + shipping).toFixed(2));
        }
    });
</script>
@endpush

@extends('layouts.app')

@section('title', __('messages.edit_supply_order'))

@section('content')
    <div class="container-fluid">
        <form action="{{ route('purchases.supply-orders.update', $supplyOrder) }}" method="POST" id="supplyOrderForm">
            @csrf
            @method('PUT')

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">{{ __('messages.edit_supply_order') }}</h1>
                    <p class="text-muted mb-0 small">{{ $supplyOrder->document_number }}</p>
                </div>
                <div class="d-flex">
                    <a href="{{ route('purchases.supply-orders.show', $supplyOrder) }}"
                        class="btn btn-light border border-secondary text-secondary me-2">
                        <i class="fas fa-times me-1"></i> {{ __('messages.cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary shadow-sm" id="btnSave">
                        <i class="fas fa-save me-1"></i> {{ __('messages.update_order') }}
                    </button>
                </div>
            </div>

            <div class="row g-4">
                <!-- Header Info -->
                <div class="col-lg-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                            <h5 class="card-title fw-bold mb-0"><i
                                    class="fas fa-info-circle me-2 text-primary"></i>{{ __('messages.basic_information') }}
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label
                                        class="form-label small fw-bold text-muted">{{ __('messages.document_number') }}</label>
                                    <input type="text" class="form-control bg-light fw-bold"
                                        value="{{ $supplyOrder->document_number }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted">{{ __('messages.order_number') }}
                                        <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control bg-light"
                                        value="{{ $supplyOrder->order_number }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted">{{ __('messages.order_date') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="order_date" class="form-control" required
                                        value="{{ old('order_date', $supplyOrder->order_date->format('Y-m-d')) }}">
                                </div>
                                <div class="col-md-3">
                                    <label
                                        class="form-label small fw-bold text-muted">{{ __('messages.expected_delivery_date') }}</label>
                                    <input type="date" name="expected_delivery_date" class="form-control"
                                        value="{{ old('expected_delivery_date', $supplyOrder->expected_delivery_date ? $supplyOrder->expected_delivery_date->format('Y-m-d') : '') }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted">{{ __('messages.vendor') }} <span
                                            class="text-danger">*</span></label>
                                    <select name="vendor_id" class="form-select select2" required>
                                        <option value="">{{ __('messages.select_vendor') }}</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}" {{ old('vendor_id', $supplyOrder->vendor_id) == $vendor->id ? 'selected' : '' }}>
                                                {{ $vendor->code }} -
                                                {{ app()->getLocale() == 'ar' ? $vendor->name_ar : $vendor->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted">{{ __('messages.branch') }} <span
                                            class="text-danger">*</span></label>
                                    <select name="branch_id" id="branch_id" class="form-select select2" required>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ old('branch_id', $supplyOrder->branch_id) == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted">{{ __('messages.warehouse') }} <span
                                            class="text-danger">*</span></label>
                                    <select name="warehouse_id" id="warehouse_id" class="form-select select2" required>
                                        <option value="">{{ __('messages.select_warehouse') }}</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $supplyOrder->warehouse_id) == $warehouse->id ? 'selected' : '' }}>
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
                        <div
                            class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                            <h5 class="card-title fw-bold mb-0"><i
                                    class="fas fa-boxes me-2 text-primary"></i>{{ __('messages.order_items') }}</h5>
                            <button type="button" class="btn btn-sm btn-success rounded-pill px-3" id="btnAddRow">
                                <i class="fas fa-plus me-1"></i> {{ __('messages.add_item') }}
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-flush align-middle mb-0" id="itemsTable">
                                    <thead class="bg-light text-muted small text-uppercase fw-bold">
                                        <tr>
                                            <th class="px-4 py-3" style="width: 25%;">{{ __('messages.product') }}</th>
                                            <th class="py-3 text-center" style="width: 25%;">{{ __('messages.quantity') }} / {{ __('messages.unit') ?? 'Unit' }}</th>
                                            <th class="py-3 text-center" style="width: 15%;">{{ __('messages.unit_price') }}
                                            </th>
                                            <th class="py-3 text-center" style="width: 10%;">{{ __('messages.discount') }} %
                                            </th>
                                            <th class="py-3 text-center" style="width: 15%;">{{ __('messages.total') }}</th>
                                            <th class="px-4 py-3 text-end" style="width: 5%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsBody">
                                        @foreach($supplyOrder->items as $index => $item)
                                            <tr class="item-row">
                                                <td class="px-4">
                                                    <select name="items[{{ $index }}][product_id]"
                                                        class="form-select select2-product" required>
                                                        <option value=""></option>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}"
                                                                data-price="{{ $product->purchase_price }}"
                                                                data-tax="{{ $product->tax_rate ?? $taxSetting->default_tax_rate }}"
                                                                data-units="{{ json_encode($product->units) }}"
                                                                {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                                {{ $product->code }} - {{ $product->name }} ({{ __('messages.stock') }}: {{ $product->available_stock }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-sm w-100">
                                                        <span class="input-group-text p-0" style="width: 40%">
                                                            <select class="form-select border-0 bg-transparent item-unit-dropdown" name="items[{{ $index }}][measurement_unit_id]" required style="box-shadow: none; cursor: pointer;">
                                                                @php
                                                                    $selectedProduct = $products->firstWhere('id', $item->product_id);
                                                                    $units = $selectedProduct ? $selectedProduct->units : collect();
                                                                @endphp
                                                                @if($units->count() > 0)
                                                                    @foreach($units as $unit)
                                                                        @php
                                                                            $unitId = is_array($item) ? ($item['measurement_unit_id'] ?? '') : $item->measurement_unit_id;
                                                                            $unitName = $unit->measurementUnit ? $unit->measurementUnit->name : $unit->name;
                                                                        @endphp
                                                                        <option value="{{ $unit->measurement_unit_id }}" {{ $unit->measurement_unit_id == $unitId ? 'selected' : '' }}>
                                                                            {{ $unitName }}
                                                                        </option>
                                                                    @endforeach
                                                                @else
                                                                    <option value="">-</option>
                                                                @endif
                                                            </select>
                                                        </span>
                                                        <input type="number" name="items[{{ $index }}][quantity]"
                                                            class="form-control text-center qty-input" step="0.001" min="0.001"
                                                            required value="{{ $item->quantity }}">
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="number" name="items[{{ $index }}][unit_price]"
                                                        class="form-control text-center price-input" step="0.01" min="0"
                                                        required value="{{ $item->unit_price }}">
                                                </td>
                                                <td>
                                                    <input type="number" name="items[{{ $index }}][discount_percentage]"
                                                        class="form-control text-center discount-input" step="0.01" min="0"
                                                        max="100" value="{{ $item->discount_percentage }}">
                                                </td>
                                                <td class="text-center fw-bold row-total">
                                                    {{ number_format($item->total_amount, 2) }}
                                                </td>
                                                <td class="px-4 text-end">
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger border-0 rounded-circle btn-remove"
                                                        {{ $supplyOrder->items->count() == 1 ? 'disabled' : '' }}>
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
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
                                <label
                                    class="form-label small fw-bold text-muted">{{ __('messages.terms_conditions') }}</label>
                                <textarea name="terms_conditions" rows="3" class="form-control"
                                    placeholder="{{ __('messages.terms_placeholder') }}">{{ old('terms_conditions', $supplyOrder->terms_conditions) }}</textarea>
                            </div>
                            <div>
                                <label class="form-label small fw-bold text-muted">{{ __('messages.notes') }}</label>
                                <textarea name="notes" rows="3" class="form-control"
                                    placeholder="{{ __('messages.notes_placeholder') }}">{{ old('notes', $supplyOrder->notes) }}</textarea>
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
                                <span id="summary-subtotal"
                                    class="fw-semibold text-dark">{{ number_format($supplyOrder->subtotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 text-danger">
                                <span>{{ __('messages.discount') }}</span>
                                <span id="summary-discount">{{ number_format($supplyOrder->discount_amount, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">{{ __('messages.tax') }}</span>
                                <span id="summary-tax"
                                    class="fw-semibold text-dark">{{ number_format($supplyOrder->tax_amount, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="text-muted">{{ __('messages.shipping') }}</span>
                                <div class="w-50">
                                    <input type="number" name="shipping_amount"
                                        class="form-control form-control-sm text-end" id="shipping_amount" step="0.01"
                                        min="0" value="{{ old('shipping_amount', $supplyOrder->shipping_amount) }}">
                                </div>
                            </div>
                            <hr class="my-4 border-2 border-primary">
                            <div class="d-flex justify-content-between align-items-center mb-0">
                                <h4 class="fw-bold mb-0 text-primary">{{ __('messages.total_amount') }}</h4>
                                <h4 class="fw-bold mb-0 text-primary" id="summary-total">
                                    {{ number_format($supplyOrder->total_amount, 2) }}
                                </h4>
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
        .select2-container--bootstrap-5 .select2-selection {
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            height: calc(2.25rem + 2px);
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            line-height: 2.25rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function () {
            let rowCount = {{ $supplyOrder->items->count() }};

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

            $('#btnAddRow').on('click', function () {
                let firstRow = $('.item-row:first');
                let newRow = firstRow.clone();

                newRow.find('select').attr('name', `items[${rowCount}][product_id]`).val('').removeClass('select2-hidden-accessible');
                newRow.find('.qty-input').attr('name', `items[${rowCount}][quantity]`).val(1);
                newRow.find('.price-input').attr('name', `items[${rowCount}][unit_price]`).val('0.00');
                newRow.find('.discount-input').attr('name', `items[${rowCount}][discount_percentage]`).val('0.00');
                newRow.find('.row-total').text('0.00');
                newRow.find('.btn-remove').prop('disabled', false);
                newRow.find('.select2-container').remove();
                newRow.find('.item-unit-dropdown').attr('name', `items[${rowCount}][measurement_unit_id]`).empty().append('<option value="">-</option>');

                $('#itemsBody').append(newRow);
                if (window.initGlobalSelect2) window.initGlobalSelect2(newRow[0] || newRow);
                newRow.find('.select2-product').select2({ theme: 'bootstrap-5', width: '100%', placeholder: "{{ __('messages.select_product') }}", allowClear: true });

                rowCount++;
                calculateTotals();
            });

            $(document).on('click', '.btn-remove', function () {
                if ($('.item-row').length > 1) {
                    $(this).closest('tr').remove();
                    calculateTotals();
                }
            });

            $(document).on('change', '.select2-product', function () {
                let selected = $(this).find(':selected');
                let price = selected.data('price') || 0;
                let unitsArr = selected.data('units');
                let $row = $(this).closest('tr');
                
                $row.find('.price-input').val(parseFloat(price).toFixed(2));
                
                let $unitDropdown = $row.find('.item-unit-dropdown');
                $unitDropdown.empty();
                if (unitsArr && Array.isArray(unitsArr) && unitsArr.length > 0) {
                    unitsArr.forEach(u => {
                        const unitName = u.measurement_unit ? u.measurement_unit.name : (u.name || (u.measurementUnit ? u.measurementUnit.name : ''));
                        const option = new Option(unitName, u.measurement_unit_id);
                        $(option).attr('data-price', u.price);
                        $unitDropdown.append(option);
                    });
                } else {
                    $unitDropdown.append(new Option('-', ''));
                }
                
                calculateTotals();
            });

            $(document).on('change', '.item-unit-dropdown', function() {
                const $row = $(this).closest('tr');
                const $option = $(this).find('option:selected');
                const price = $option.attr('data-price');
                if (price !== undefined && price !== null && price !== '') {
                    $row.find('.price-input').val(parseFloat(price).toFixed(2));
                    calculateTotals();
                }
            });

            $(document).on('input', '.qty-input, .price-input, .discount-input, #shipping_amount', function () {
                calculateTotals();
            });

            function calculateTotals() {
                let subtotal = 0;
                let totalDiscount = 0;
                let totalTax = 0;
                let shipping = parseFloat($('#shipping_amount').val()) || 0;

                $('.item-row').each(function () {
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
@extends('layouts.app')

@section('title', __('messages.create_stock_supply'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card glassy">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">{{ __('messages.create_stock_supply') }}</h3>
                        <a href="{{ route('inventory.stock-supply.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> {{ __('messages.back') }}
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('inventory.stock-supply.store') }}" method="POST" id="supplyForm">
                            @csrf
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">{{ __('messages.document_number') }}</label>
                                    <input type="text" class="form-control" value="{{ $documentNumber }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="supply_date" class="form-label fw-bold">{{ __('messages.date') }}</label>
                                    <input type="date" name="supply_date" id="supply_date" class="form-control"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="warehouse_id"
                                        class="form-label fw-bold">{{ __('messages.warehouse') }}</label>
                                    <select name="warehouse_id" id="warehouse_id" class="form-select" required>
                                        <option value="">{{ __('messages.select_warehouse') }}</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="vendor_id" class="form-label fw-bold">{{ __('messages.vendor') }}</label>
                                    <select name="vendor_id" id="vendor_id" class="form-select" required>
                                        <option value="">{{ __('messages.select_vendor') }}</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label for="reference_number"
                                        class="form-label fw-bold">{{ __('messages.reference_number') }}</label>
                                    <input type="text" name="reference_number" id="reference_number" class="form-control">
                                </div>
                                <div class="col-md-8">
                                    <label for="notes" class="form-label fw-bold">{{ __('messages.notes') }}</label>
                                    <textarea name="notes" id="notes" class="form-control" rows="1"></textarea>
                                </div>
                            </div>

                            <hr>

                            <h4 class="mb-3">{{ __('messages.items') }}</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="itemsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 40%">{{ __('messages.product') }}</th>
                                            <th style="width: 15%">{{ __('messages.quantity') }}</th>
                                            <th style="width: 15%">{{ __('messages.unit_cost') }}</th>
                                            <th style="width: 20%">{{ __('messages.total') }}</th>
                                            <th style="width: 10%"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <select name="items[0][product_id]" class="form-select product-select"
                                                    required>
                                                    <option value="">{{ __('messages.select_product') }}</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}"
                                                            data-cost="{{ $product->cost_price }}">
                                                            {{ $product->name }} ({{ $product->code }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="items[0][quantity]"
                                                    class="form-control qty-input" step="0.001" min="0.001" required>
                                            </td>
                                            <td>
                                                <input type="number" name="items[0][unit_cost]"
                                                    class="form-control cost-input" step="0.01" min="0" required>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control total-input" readonly value="0.00">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-row"><i
                                                        class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold">{{ __('messages.grand_total') }}</td>
                                            <td colspan="2">
                                                <input type="text" id="grandTotal" class="form-control fw-bold" readonly
                                                    value="0.00">
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addRow">
                                <i class="fas fa-plus me-1"></i> {{ __('messages.add_row') }}
                            </button>

                            <div class="mt-4 text-end">
                                <button type="submit" class="btn btn-primary px-5 shadow-sm">
                                    <i class="fas fa-save me-1"></i> {{ __('messages.save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                let rowCount = 1;

                $('#addRow').click(function () {
                    let newRow = `
                            <tr>
                                <td>
                                    <select name="items[${rowCount}][product_id]" class="form-select product-select" required>
                                        <option value="">{{ __('messages.select_product') }}</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-cost="{{ $product->cost_price }}">
                                                {{ $product->name }} ({{ $product->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="items[${rowCount}][quantity]" class="form-control qty-input" step="0.001" min="0.001" required>
                                </td>
                                <td>
                                    <input type="number" name="items[${rowCount}][unit_cost]" class="form-control cost-input" step="0.01" min="0" required>
                                </td>
                                <td>
                                    <input type="text" class="form-control total-input" readonly value="0.00">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        `;
                    $('#itemsTable tbody').append(newRow);
                    if (window.initGlobalSelect2) window.initGlobalSelect2(newRow[0] || newRow);
                    rowCount++;
                    $('.product-select').select2(); // If select2 is available
                });

                $(document).on('click', '.remove-row', function () {
                    if ($('#itemsTable tbody tr').length > 1) {
                        $(this).closest('tr').remove();
                        calculateGrandTotal();
                    }
                });

                $(document).on('change', '.product-select', function () {
                    let cost = $(this).find(':selected').data('cost');
                    if (cost) {
                        $(this).closest('tr').find('.cost-input').val(cost);
                        calculateRowTotal($(this).closest('tr'));
                    }
                });

                $(document).on('input', '.qty-input, .cost-input', function () {
                    calculateRowTotal($(this).closest('tr'));
                });

                function calculateRowTotal(row) {
                    let qty = parseFloat(row.find('.qty-input').val()) || 0;
                    let cost = parseFloat(row.find('.cost-input').val()) || 0;
                    let total = qty * cost;
                    row.find('.total-input').val(total.toFixed(2));
                    calculateGrandTotal();
                }

                function calculateGrandTotal() {
                    let grandTotal = 0;
                    $('.total-input').each(function () {
                        grandTotal += parseFloat($(this).val()) || 0;
                    });
                    $('#grandTotal').val(grandTotal.toFixed(2));
                }
            });
        </script>
    @endpush
@endsection
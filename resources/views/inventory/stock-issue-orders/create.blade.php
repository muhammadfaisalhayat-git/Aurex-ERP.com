@extends('layouts.app')

@section('title', __('messages.create_issue_order'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.create_issue_order') }}</h1>
            <a href="{{ route('inventory.issue-orders.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> {{ __('messages.back') }}
            </a>
        </div>

        <form action="{{ route('inventory.issue-orders.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card glassy mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('messages.issue_details') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('messages.date') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="issue_date" class="form-control" value="{{ date('Y-m-d') }}"
                                        required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('messages.warehouse') }} <span
                                            class="text-danger">*</span></label>
                                    <select name="warehouse_id" class="form-select" required>
                                        <option value="">{{ __('messages.select_warehouse') }}</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ __('messages.issue_type') }} <span
                                            class="text-danger">*</span></label>
                                    <select name="issue_type" class="form-select" required>
                                        <option value="wastage">{{ __('messages.wastage') }}</option>
                                        <option value="adjustment">{{ __('messages.adjustment') }}</option>
                                        <option value="sale">{{ __('messages.sale') }}</option>
                                        <option value="return">{{ __('messages.return') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ __('messages.customer') }}</label>
                                    <select name="customer_id" class="form-select">
                                        <option value="">{{ __('messages.select_customer') }}</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ __('messages.vendor') }}</label>
                                    <select name="vendor_id" class="form-select">
                                        <option value="">{{ __('messages.select_vendor') }}</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card glassy mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">{{ __('messages.items') }}</h5>
                            <button type="button" class="btn btn-sm btn-primary" onclick="addRow()">
                                <i class="fas fa-plus me-1"></i> {{ __('messages.add_item') }}
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table mb-0" id="items-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50%">{{ __('messages.product') }}</th>
                                            <th style="width: 25%">{{ __('messages.quantity') }} /
                                                {{ __('messages.unit') ?? 'Unit' }}
                                            </th>
                                            <th style="width: 20%">{{ __('messages.notes') }}</th>
                                            <th style="width: 10%"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="item-row">
                                            <td>
                                                <select name="items[0][product_id]" class="form-select product-select"
                                                    required>
                                                    <option value="">{{ __('messages.select_product') }}</option>
                                                    @foreach(\App\Models\Product::with('units.measurementUnit')->get() as $product)
                                                        <option value="{{ $product->id }}"
                                                            data-units="{{ json_encode($product->units) }}">{{ $product->name }}
                                                            ({{ __('messages.stock') }}: {{ $product->available_stock }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-text p-0" style="width: 35%">
                                                        <select
                                                            class="form-select border-0 bg-transparent item-unit-dropdown"
                                                            name="items[0][measurement_unit_id]" required
                                                            style="box-shadow: none; cursor: pointer;">
                                                            <option value="">-</option>
                                                        </select>
                                                    </span>
                                                    <input type="number" name="items[0][quantity]" class="form-control"
                                                        step="0.001" min="0.001" required>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" name="items[0][notes]" class="form-control">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-outline-danger btn-sm"
                                                    onclick="removeRow(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card glassy mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('messages.notes') }}</h5>
                        </div>
                        <div class="card-body">
                            <textarea name="notes" class="form-control" rows="5"
                                placeholder="{{ __('messages.notes_placeholder') }}"></textarea>
                        </div>
                    </div>

                    <div class="card glassy">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save me-2"></i> {{ __('messages.save_issue_order') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            let rowCount = 1;
            function addRow() {
                const tbody = document.querySelector('#items-table tbody');
                const newRow = document.createElement('tr');
                newRow.className = 'item-row';
                newRow.innerHTML = `
                                                <td>
                                                    <select name="items[${rowCount}][product_id]" class="form-select product-select" required>
                                                        <option value="">{{ __('messages.select_product') }}</option>
                                                        @foreach(\App\Models\Product::with('units.measurementUnit')->get() as $product)
                                                            <option value="{{ $product->id }}" data-units="{{ json_encode($product->units) }}">{{ $product->name }} ({{ __('messages.stock') }}: {{ $product->available_stock }})</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <span class="input-group-text p-0" style="width: 35%">
                                                            <select class="form-select border-0 bg-transparent item-unit-dropdown" name="items[${rowCount}][measurement_unit_id]" required style="box-shadow: none; cursor: pointer;">
                                                                <option value="">-</option>
                                                            </select>
                                                        </span>
                                                        <input type="number" name="items[${rowCount}][quantity]" class="form-control" step="0.001" min="0.001" required>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" name="items[${rowCount}][notes]" class="form-control">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeRow(this)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            `;
                tbody.appendChild(newRow);
                if (window.initGlobalSelect2) window.initGlobalSelect2(newRow);
                rowCount++;
            }
            function removeRow(btn) {
                const rows = document.querySelectorAll('.item-row');
                if (rows.length > 1) btn.closest('tr').remove();
            }

            document.addEventListener('change', function (e) {
                if (e.target && e.target.classList.contains('product-select')) {
                    const selectedOption = e.target.options[e.target.selectedIndex];
                    const unitsStr = selectedOption.getAttribute('data-units');
                    const units = unitsStr ? JSON.parse(unitsStr) : [];
                    const stock = selectedOption.text.match(/Stock: ([\d.]+)/)?.[1] || 0;

                    const row = e.target.closest('tr');
                    row.dataset.availableStock = stock;
                    const unitDropdown = row.querySelector('.item-unit-dropdown');
                    unitDropdown.innerHTML = '';

                    if (units && units.length > 0) {
                        units.forEach(u => {
                            const unitName = u.measurement_unit ? u.measurement_unit.name : (u.name || (u.measurementUnit ? u.measurementUnit.name : ''));
                            const option = new Option(unitName, u.measurement_unit_id);
                            option.dataset.package = u.package;
                            unitDropdown.add(option);
                        });
                    } else {
                        unitDropdown.add(new Option('-', ''));
                    }
                    calculateRow(row);
                }

                if (e.target && e.target.classList.contains('item-unit-dropdown')) {
                    calculateRow(e.target.closest('tr'));
                }
            });

            document.addEventListener('input', function (e) {
                if (e.target && e.target.name && e.target.name.includes('[quantity]')) {
                    calculateRow(e.target.closest('tr'));
                }
            });

            function calculateRow(row) {
                const qEl = row.querySelector('input[name*="[quantity]"]');
                const qty = parseFloat(qEl.value) || 0;
                const availableStockInBaseUnit = parseFloat(row.dataset.availableStock) || 0;
                const unitEl = row.querySelector('.item-unit-dropdown');

                let packageMultiplier = 1;
                if (unitEl && unitEl.options[unitEl.selectedIndex]) {
                    packageMultiplier = parseFloat(unitEl.options[unitEl.selectedIndex].dataset.package) || 1;
                }

                const quantityInBaseUnit = qty * packageMultiplier;

                if (quantityInBaseUnit > availableStockInBaseUnit && availableStockInBaseUnit > 0) {
                    if (!row._isAlerting) {
                        row._isAlerting = true;
                        const availableInSelectedUnit = (availableStockInBaseUnit / packageMultiplier).toFixed(2);
                        Swal.fire({
                            icon: 'warning',
                            title: '{{ __("messages.stock_shortage") ?? "Stock Shortage" }}',
                            text: `{{ __('messages.quantity_exceeds_available_stock') ?? 'Quantity exceeds available stock' }} (${availableInSelectedUnit})`,
                            confirmButtonText: '{{ __("messages.ok") ?? "OK" }}'
                        }).then(() => {
                            row._isAlerting = false;
                        });
                        qEl.value = Math.floor(availableInSelectedUnit * 100) / 100;
                    }
                }
            }
        </script>
    @endpush
@endsection
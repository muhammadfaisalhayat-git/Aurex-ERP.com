@extends('layouts.app')

@section('title', __('messages.create_stock_receiving'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.create_stock_receiving') }}</h1>
            <a href="{{ route('inventory.stock-receiving.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> {{ __('messages.back') }}
            </a>
        </div>

        <form action="{{ route('inventory.stock-receiving.store') }}" method="POST" id="receiving-form">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card glassy mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('messages.basic_information') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('messages.date') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="receiving_date" class="form-control"
                                        value="{{ date('Y-m-d') }}" required>
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
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('messages.vendor') }} <span
                                            class="text-danger">*</span></label>
                                    <select name="vendor_id" class="form-select" required>
                                        <option value="">{{ __('messages.select_vendor') }}</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('messages.reference_number') }}</label>
                                    <input type="text" name="purchase_order_number" class="form-control"
                                        placeholder="PO-12345">
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
                                            <th style="width: 20%">{{ __('messages.quantity') }}</th>
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
                                                    @foreach(\App\Models\Product::withSum('stockBalances', 'available_quantity')->get() as $product)
                                                        <option value="{{ $product->id }}">{{ $product->name }} ({{ __('messages.stock') }}: {{ $product->available_stock }})
                                                            ({{ __('messages.stock') }}:
                                                            {{ $product->stock_balances_sum_available_quantity ?? 0 }})</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="items[0][quantity]" class="form-control"
                                                    step="0.001" min="0.001" required>
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
                            <h5 class="card-title mb-0">{{ __('messages.additional_notes') }}</h5>
                        </div>
                        <div class="card-body">
                            <textarea name="notes" class="form-control" rows="5"
                                placeholder="{{ __('messages.notes_placeholder') }}"></textarea>
                        </div>
                    </div>

                    <div class="card glassy">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-save me-2"></i> {{ __('messages.save_as_draft') }}
                            </button>
                            <a href="{{ route('inventory.stock-receiving.index') }}" class="btn btn-light w-100">
                                {{ __('messages.cancel') }}
                            </a>
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
                                            <select name="items[${rowCount}][product_id]" class="form-select" required>
                                                <option value="">{{ __('messages.select_product') }}</option>
                                                @foreach(\App\Models\Product::withSum('stockBalances', 'available_quantity')->get() as $product)
                                                    <option value="{{ $product->id }}">{{ $product->name }} ({{ __('messages.stock') }}: {{ $product->stock_balances_sum_available_quantity ?? 0 }})</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="items[${rowCount}][quantity]" class="form-control" step="0.001" min="0.001" required>
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
                if (rows.length > 1) {
                    btn.closest('tr').remove();
                } else {
                    alert('At least one item is required.');
                }
            }
        </script>
    @endpush
@endsection
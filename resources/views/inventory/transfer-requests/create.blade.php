@extends('layouts.app')

@section('title', __('messages.create_transfer_request'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.create_transfer_request') }}</h1>
            <a href="{{ route('inventory.transfer-requests.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> {{ __('messages.back') }}
            </a>
        </div>

        <form action="{{ route('inventory.transfer-requests.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card glassy mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('messages.request_details') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ __('messages.date') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="request_date" class="form-control" value="{{ date('Y-m-d') }}"
                                        required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ __('messages.from_warehouse') }} <span
                                            class="text-danger">*</span></label>
                                    <select name="from_warehouse_id" class="form-select" required>
                                        <option value="">{{ __('messages.select_warehouse') }}</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ __('messages.to_warehouse') }}
                                        ({{ __('messages.requesting_warehouse') }}) <span
                                            class="text-danger">*</span></label>
                                    <select name="to_warehouse_id" class="form-select" required>
                                        <option value="">{{ __('messages.select_warehouse') }}</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card glassy mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">{{ __('messages.requested_items') }}</h5>
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
                                                <select name="items[0][product_id]" class="form-select" required>
                                                    <option value="">{{ __('messages.select_product') }}</option>
                                                    @foreach(\App\Models\Product::all() as $product)
                                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
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
                                <i class="fas fa-paper-plane me-2"></i> {{ __('messages.send_request') }}
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
                                    <select name="items[${rowCount}][product_id]" class="form-select" required>
                                        <option value="">{{ __('messages.select_product') }}</option>
                                        @foreach(\App\Models\Product::all() as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
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
                if (rows.length > 1) btn.closest('tr').remove();
            }
        </script>
    @endpush
@endsection
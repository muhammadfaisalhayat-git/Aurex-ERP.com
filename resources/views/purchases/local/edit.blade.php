@extends('layouts.app')

@section('title', __('local_purchase.edit'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ __('local_purchase.edit') }}</h1>
        <a href="{{ route('local-purchases.show', $localPurchase) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('general.back') }}
        </a>
    </div>

    <form action="{{ route('local-purchases.update', $localPurchase) }}" method="POST" id="localPurchaseForm">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('local_purchase.supplier_info') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="supplier_name" class="form-label">{{ __('local_purchase.supplier_name') }} *</label>
                                <input type="text" class="form-control @error('supplier_name') is-invalid @enderror" 
                                       id="supplier_name" name="supplier_name" value="{{ old('supplier_name', $localPurchase->supplier_name) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="supplier_phone" class="form-label">{{ __('local_purchase.supplier_phone') }}</label>
                                <input type="text" class="form-control @error('supplier_phone') is-invalid @enderror" 
                                       id="supplier_phone" name="supplier_phone" value="{{ old('supplier_phone', $localPurchase->supplier_phone) }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="supplier_email" class="form-label">{{ __('local_purchase.supplier_email') }}</label>
                                <input type="email" class="form-control @error('supplier_email') is-invalid @enderror" 
                                       id="supplier_email" name="supplier_email" value="{{ old('supplier_email', $localPurchase->supplier_email) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="invoice_number" class="form-label">{{ __('local_purchase.invoice_number') }} *</label>
                                <input type="text" class="form-control @error('invoice_number') is-invalid @enderror" 
                                       id="invoice_number" name="invoice_number" value="{{ old('invoice_number', $localPurchase->invoice_number) }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="invoice_date" class="form-label">{{ __('local_purchase.invoice_date') }} *</label>
                                <input type="date" class="form-control @error('invoice_date') is-invalid @enderror" 
                                       id="invoice_date" name="invoice_date" value="{{ old('invoice_date', $localPurchase->invoice_date->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="supplier_address" class="form-label">{{ __('local_purchase.supplier_address') }}</label>
                                <textarea class="form-control @error('supplier_address') is-invalid @enderror" 
                                          id="supplier_address" name="supplier_address" rows="2">{{ old('supplier_address', $localPurchase->supplier_address) }}</textarea>
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
                                    @foreach($localPurchase->items as $index => $item)
                                    <tr class="item-row">
                                        <td>
                                            <select class="form-select item-select" name="items[{{ $index }}][item_id]" required>
                                                <option value="">{{ __('general.select_item') }}</option>
                                                @foreach(\App\Models\Item::where('is_active', true)->get() as $product)
                                                    <option value="{{ $product->id }}" data-price="{{ $product->purchase_price ?? $product->sale_price }}" 
                                                        {{ $item->item_id == $product->id ? 'selected' : '' }}>
                                                        {{ $product->code }} - {{ $product->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control quantity" name="items[{{ $index }}][quantity]" 
                                                   value="{{ $item->quantity }}" min="0.01" step="0.01" required>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control unit-price" name="items[{{ $index }}][unit_price]" 
                                                   value="{{ $item->unit_price }}" min="0" step="0.01" required>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control discount" name="items[{{ $index }}][discount_amount]" 
                                                   value="{{ $item->discount_amount }}" min="0" step="0.01">
                                        </td>
                                        <td>
                                            <select class="form-select tax-rate" name="items[{{ $index }}][tax_rate]">
                                                <option value="0" {{ $item->tax_rate == 0 ? 'selected' : '' }}>0%</option>
                                                <option value="5" {{ $item->tax_rate == 5 ? 'selected' : '' }}>5%</option>
                                                <option value="15" {{ $item->tax_rate == 15 ? 'selected' : '' }}>15%</option>
                                            </select>
                                        </td>
                                        <td class="item-total">{{ number_format($item->gross_amount, 2) }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger remove-item">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
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
                            <select class="form-select @error('branch_id') is-invalid @enderror" id="branch_id" name="branch_id" required>
                                <option value="">{{ __('general.select') }}</option>
                                @foreach(\App\Models\Branch::where('is_active', true)->get() as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id', $localPurchase->branch_id) == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="warehouse_id" class="form-label">{{ __('local_purchase.warehouse') }} *</label>
                            <select class="form-select @error('warehouse_id') is-invalid @enderror" id="warehouse_id" name="warehouse_id" required>
                                <option value="">{{ __('general.select') }}</option>
                                @foreach(\App\Models\Warehouse::where('is_active', true)->get() as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $localPurchase->warehouse_id) == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">{{ __('local_purchase.notes') }}</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes', $localPurchase->notes) }}</textarea>
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
                                <td class="text-end" id="subtotal">{{ number_format($localPurchase->items->sum(fn($i) => ($i->quantity * $i->unit_price)), 2) }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('local_purchase.discount') }}</td>
                                <td class="text-end" id="totalDiscount">{{ number_format($localPurchase->discount_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('local_purchase.tax') }}</td>
                                <td class="text-end" id="totalTax">{{ number_format($localPurchase->tax_amount, 2) }}</td>
                            </tr>
                            <tr class="fw-bold">
                                <td>{{ __('local_purchase.total') }}</td>
                                <td class="text-end" id="grandTotal">{{ number_format($localPurchase->gross_amount, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-save"></i> {{ __('general.update') }}
                </button>
            </div>
        </div>
    </form>
</div>

<template id="itemRowTemplate">
    <tr class="item-row">
        <td>
            <select class="form-select item-select" name="items[INDEX][item_id]" required>
                <option value="">{{ __('general.select_item') }}</option>
                @foreach(\App\Models\Item::where('is_active', true)->get() as $item)
                    <option value="{{ $item->id }}" data-price="{{ $item->purchase_price ?? $item->sale_price }}">
                        {{ $item->code }} - {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" class="form-control quantity" name="items[INDEX][quantity]" value="1" min="0.01" step="0.01" required>
        </td>
        <td>
            <input type="number" class="form-control unit-price" name="items[INDEX][unit_price]" value="0" min="0" step="0.01" required>
        </td>
        <td>
            <input type="number" class="form-control discount" name="items[INDEX][discount_amount]" value="0" min="0" step="0.01">
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
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = {{ $localPurchase->items->count() }};
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
        row.querySelector('.remove-item').addEventListener('click', function() {
            row.remove();
            calculateTotals();
        });
        
        row.querySelector('.item-select').addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            const price = option.dataset.price || 0;
            row.querySelector('.unit-price').value = price;
            calculateRowTotal(row);
        });
        
        ['quantity', 'unit-price', 'discount', 'tax-rate'].forEach(cls => {
            row.querySelector('.' + cls).addEventListener('input', function() {
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

    // Add listeners to existing rows
    document.querySelectorAll('.item-row').forEach(row => {
        row.querySelector('.remove-item').addEventListener('click', function() {
            row.remove();
            calculateTotals();
        });
        
        row.querySelector('.item-select').addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            const price = option.dataset.price || 0;
            row.querySelector('.unit-price').value = price;
            calculateRowTotal(row);
        });
        
        ['quantity', 'unit-price', 'discount', 'tax-rate'].forEach(cls => {
            row.querySelector('.' + cls).addEventListener('input', function() {
                calculateRowTotal(row);
            });
        });
    });

    addItemBtn.addEventListener('click', addItemRow);
});
</script>
@endpush

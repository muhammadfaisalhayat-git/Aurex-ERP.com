<div class="table-responsive item-units-grid">
    <table class="table table-bordered table-hover align-middle mb-0" id="itemUnitsTable">
        <thead class="table-light text-center small text-uppercase text-muted">
            <tr>
                <th style="min-width: 150px;">{{ __('messages.unit') }}</th>
                <th style="min-width: 100px;">{{ __('messages.package') }}</th>
                <th style="min-width: 120px;">{{ __('messages.price') }}</th>
                <th style="min-width: 150px;">{{ __('messages.barcode') }}</th>
                <th style="min-width: 200px;">{{ __('messages.description') }}</th>
                <th style="min-width: 200px;">{{ __('messages.foreign_description') }}</th>
                <th style="min-width: 80px;" title="{{ __('messages.is_purchase_unit') }}">{{ __('messages.pur') }}</th>
                <th style="min-width: 80px;" title="{{ __('messages.is_transfer_unit') }}">{{ __('messages.trn') }}</th>
                <th style="min-width: 80px;" title="{{ __('messages.is_stocktaking_unit') }}">{{ __('messages.stk') }}</th>
                <th style="min-width: 80px;" title="{{ __('messages.is_not_for_sale') }}">{{ __('messages.nfs') }}</th>
                <th style="min-width: 80px;" title="{{ __('messages.is_inactive') }}">{{ __('messages.ina') }}</th>
                <th style="min-width: 80px;" title="{{ __('messages.is_production_unit') }}">{{ __('messages.prd') }}</th>
                <th style="min-width: 80px;" title="{{ __('messages.is_store_unit') }}">{{ __('messages.str') }}</th>
                <th style="min-width: 80px;" title="{{ __('messages.is_customer_self_service') }}">{{ __('messages.css') }}</th>
                <th style="min-width: 80px;" title="{{ __('messages.excluded_from_discount') }}">{{ __('messages.edc') }}</th>
                <th style="min-width: 60px;">{{ __('messages.act') }}</th>
            </tr>
        </thead>
        <tbody id="itemUnitsBody">
            @if(isset($product) && $product->units->count() > 0)
                @foreach($product->units as $index => $unit)
                    @include('inventory.products.partials.item-unit-row', ['index' => $index, 'unit' => $unit])
                @endforeach
            @else
                @include('inventory.products.partials.item-unit-row', ['index' => 0, 'unit' => null])
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="16" class="bg-light text-start py-3">
                    <button type="button" class="btn btn-sm btn-outline-primary shadow-sm rounded-pill px-3"
                        id="addUnitBtn">
                        <i class="fas fa-plus me-1"></i> {{ __('messages.add_unit') }}
                    </button>
                    <small class="text-muted ms-2">{{ __('messages.add_unit_hint') }}</small>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<!-- Template for new row -->
<template id="itemUnitRowTemplate">
    @include('inventory.products.partials.item-unit-row', ['index' => '__INDEX__', 'unit' => null])
</template>

<style>
    .item-units-grid .form-control,
    .item-units-grid .form-select {
        font-size: 0.85rem;
        padding: 0.375rem 0.5rem;
        border-radius: 4px;
        min-width: 80px;
    }

    .item-units-grid .form-check-input {
        width: 1.25em;
        height: 1.25em;
        cursor: pointer;
    }

    .item-units-grid th {
        vertical-align: middle;
        font-weight: 600;
        white-space: nowrap;
    }

    .item-units-grid td {
        padding: 0.5rem;
        vertical-align: middle;
    }

    .remove-unit-btn {
        transition: all 0.2s;
    }

    .remove-unit-btn:hover {
        background-color: #dc3545;
        color: white;
    }
</style>

<script>
    document.addEventListener('turbo:load', function () {
        const addBtn = document.getElementById('addUnitBtn');
        const tbody = document.getElementById('itemUnitsBody');
        const template = document.getElementById('itemUnitRowTemplate');

        if (!addBtn || !tbody || !template) return;

        let rowIndex = tbody.querySelectorAll('tr').length;

        addBtn.addEventListener('click', function () {
            let html = template.innerHTML.replace(/__INDEX__/g, rowIndex);
            tbody.insertAdjacentHTML('beforeend', html);

            // Re-initialize any select2 if needed in the new row
            const newRow = tbody.lastElementChild;
            const newSelect = newRow.querySelector('.select2');
            if (newSelect && typeof jQuery !== 'undefined' && jQuery.fn.select2) {
                jQuery(newSelect).select2({ theme: 'bootstrap-5' });
            }

            rowIndex++;
        });

        tbody.addEventListener('click', function (e) {
            if (e.target.closest('.remove-unit-btn')) {
                const tr = e.target.closest('tr');
                if (tbody.querySelectorAll('tr').length > 1) {
                    tr.remove();
                } else {
                    // Just clear inputs if it's the last row
                    tr.querySelectorAll('input:not([type="checkbox"]), select').forEach(el => el.value = '');
                    tr.querySelectorAll('input[type="checkbox"]').forEach(el => el.checked = false);
                    tr.querySelector('input[type="number"]').value = '1'; // Default package
                    tr.querySelectorAll('input[type="number"]')[1].value = '0'; // Default price
                }
            }
        });
    });
</script>
<tr>
    <td>
        <select name="item_units[{{ $index }}][measurement_unit_id]" class="form-select select2" required>
            <option value="">{{ __('messages.select_unit') }}</option>
            @foreach($measurementUnits as $mu)
                <option value="{{ $mu->id }}" {{ isset($unit) && $unit->measurement_unit_id == $mu->id ? 'selected' : '' }}>
                    {{ $mu->name }} ({{ $mu->code }})
                </option>
            @endforeach
        </select>
    </td>
    <td>
        <input type="number" name="item_units[{{ $index }}][package]" class="form-control" step="0.0001" min="0.0001" 
               value="{{ isset($unit) ? $unit->package : '1' }}" required>
    </td>
    <td>
        <input type="number" name="item_units[{{ $index }}][price]" class="form-control" step="0.01" min="0" 
               value="{{ isset($unit) ? $unit->price : '0' }}">
    </td>
    <td>
        <input type="text" name="item_units[{{ $index }}][barcode]" class="form-control" 
               value="{{ isset($unit) ? $unit->barcode : '' }}">
    </td>
    <td>
        <input type="text" name="item_units[{{ $index }}][description]" class="form-control" 
               value="{{ isset($unit) ? $unit->description : '' }}">
    </td>
    <td>
        <input type="text" name="item_units[{{ $index }}][foreign_description]" class="form-control" dir="rtl" 
               value="{{ isset($unit) ? $unit->foreign_description : '' }}">
    </td>
    
    <!-- Flags -->
    <td class="text-center">
        <input type="checkbox" name="item_units[{{ $index }}][is_purchase_unit]" class="form-check-input" value="1"
               {{ isset($unit) && $unit->is_purchase_unit ? 'checked' : '' }}>
    </td>
    <td class="text-center">
        <input type="checkbox" name="item_units[{{ $index }}][is_transfer_unit]" class="form-check-input" value="1"
               {{ isset($unit) && $unit->is_transfer_unit ? 'checked' : '' }}>
    </td>
    <td class="text-center">
        <input type="checkbox" name="item_units[{{ $index }}][is_stocktaking_unit]" class="form-check-input" value="1"
               {{ isset($unit) && $unit->is_stocktaking_unit ? 'checked' : '' }}>
    </td>
    <td class="text-center">
        <input type="checkbox" name="item_units[{{ $index }}][is_not_for_sale]" class="form-check-input" value="1"
               {{ isset($unit) && $unit->is_not_for_sale ? 'checked' : '' }}>
    </td>
    <td class="text-center">
        <input type="checkbox" name="item_units[{{ $index }}][is_inactive]" class="form-check-input" value="1"
               {{ isset($unit) && $unit->is_inactive ? 'checked' : '' }}>
    </td>
    <td class="text-center">
        <input type="checkbox" name="item_units[{{ $index }}][is_production_unit]" class="form-check-input" value="1"
               {{ isset($unit) && $unit->is_production_unit ? 'checked' : '' }}>
    </td>
    <td class="text-center">
        <input type="checkbox" name="item_units[{{ $index }}][is_store_unit]" class="form-check-input" value="1"
               {{ isset($unit) && $unit->is_store_unit ? 'checked' : '' }}>
    </td>
    <td class="text-center">
        <input type="checkbox" name="item_units[{{ $index }}][is_customer_self_service]" class="form-check-input" value="1"
               {{ isset($unit) && $unit->is_customer_self_service ? 'checked' : '' }}>
    </td>
    <td class="text-center">
        <input type="checkbox" name="item_units[{{ $index }}][excluded_from_discount]" class="form-check-input" value="1"
               {{ isset($unit) && $unit->excluded_from_discount ? 'checked' : '' }}>
    </td>
    
    <!-- Actions -->
    <td class="text-center">
        <button type="button" class="btn btn-sm btn-outline-danger border-0 remove-unit-btn" title="{{ __('messages.remove_unit') }}">
            <i class="fas fa-times"></i>
        </button>
    </td>
</tr>

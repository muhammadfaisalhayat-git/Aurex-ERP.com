@extends('layouts.app')

@section('title', isset($product) ? __('messages.edit') . ' ' . $product->name : __('messages.create_product'))

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">{{ isset($product) ? __('messages.edit_product') : __('messages.create_product') }}</h1>
            <a href="{{ route('inventory.products.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> {{ __('messages.back') }}
            </a>
        </div>

        <form
            action="{{ isset($product) ? route('inventory.products.update', $product) : route('inventory.products.store') }}"
            method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($product)) @method('PUT') @endif

            <!-- Top Header Info -->
            <div class="card glassy mb-4 border-0 shadow-sm">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">{{ __('messages.type') }}</label>
                            <select name="type" class="form-select select2" required>
                                <option value="simple" {{ (old('type', $product->type ?? '') == 'simple') ? 'selected' : '' }}>{{ __('messages.simple') }}</option>
                                <option value="composite" {{ (old('type', $product->type ?? '') == 'composite') ? 'selected' : '' }}>{{ __('messages.composite') }}</option>
                                <option value="service" {{ (old('type', $product->type ?? '') == 'service') ? 'selected' : '' }}>{{ __('messages.service') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">{{ __('messages.category') }} <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select select2" required>
                                <option value="">{{ __('messages.select_category') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ (old('category_id', $product->category_id ?? '') == $category->id) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">{{ __('messages.code') }}</label>
                            <input type="text" name="code" class="form-control"
                                value="{{ old('code', $suggestedCode ?? '') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">{{ __('messages.barcode') }}</label>
                            <div class="input-group">
                                <input type="text" name="barcode" class="form-control"
                                    value="{{ old('barcode', $product->barcode ?? '') }}">
                                <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">{{ __('messages.name_en') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name_en" class="form-control"
                                value="{{ old('name_en', $product->name_en ?? '') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">{{ __('messages.name_ar') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name_ar" class="form-control"
                                value="{{ old('name_ar', $product->name_ar ?? '') }}" required dir="rtl">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="card tab-card-highlight shadow-sm overflow-hidden">
                <div class="card-header bg-transparent border-0 p-0">
                    <ul class="nav nav-tabs nav-justified" id="productTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="master-tab" data-bs-toggle="tab" href="#master" role="tab">
                                <i class="fas fa-info-circle me-1"></i> {{ __('messages.master_details') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="other-tab" data-bs-toggle="tab" href="#other" role="tab">
                                <i class="fas fa-database me-1"></i> {{ __('messages.other_data') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="units-tab" data-bs-toggle="tab" href="#units" role="tab">
                                <i class="fas fa-balance-scale me-1"></i> {{ __('messages.item_units') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="flags-tab" data-bs-toggle="tab" href="#flags" role="tab">
                                <i class="fas fa-check-square me-1"></i> {{ __('messages.flags') }}
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4">
                    <div class="tab-content" id="productTabsContent">
                        <!-- Master Details Tab -->
                        <div class="tab-pane fade show active" id="master" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-md-8">

                                    {{-- Identification Section --}}
                                    <div class="mb-4">
                                        <h6 class="text-muted small fw-bold text-uppercase border-bottom pb-2 mb-3">
                                            <i class="fas fa-fingerprint me-1"></i> {{ __('messages.section_identification') }}
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">{{ __('messages.gtin') }}</label>
                                                <input type="text" name="gtin" class="form-control"
                                                    value="{{ old('gtin', $product->gtin ?? '') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">{{ __('messages.hsn_code') }}</label>
                                                <input type="text" name="hsn_code" class="form-control"
                                                    value="{{ old('hsn_code', $product->hsn_code ?? '') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">{{ __('messages.manufacturer_code') }}</label>
                                                <input type="text" name="manufacturer_code" class="form-control"
                                                    value="{{ old('manufacturer_code', $product->manufacturer_code ?? '') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">{{ __('messages.sku') }}</label>
                                                <input type="text" name="sku" class="form-control"
                                                    value="{{ old('sku', $product->sku ?? '') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">{{ __('messages.ref_code') }}</label>
                                                <input type="text" name="ref_code" class="form-control"
                                                    value="{{ old('ref_code', $product->ref_code ?? '') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">{{ __('messages.unit_of_measure') }} <span class="text-danger">*</span></label>
                                                <select name="unit_of_measure" class="form-select select2" required>
                                                    <option value="">{{ __('messages.select_unit') }}</option>
                                                    @foreach($measurementUnits as $mu)
                                                        <option value="{{ $mu->name }}" {{ old('unit_of_measure', $product->unit_of_measure ?? '') == $mu->name ? 'selected' : '' }}>
                                                            {{ $mu->name }} ({{ $mu->code }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">{{ __('messages.default_unit') }}</label>
                                                <select name="default_unit" class="form-select select2">
                                                    <option value="">{{ __('messages.select_unit') }}</option>
                                                    @foreach($measurementUnits as $mu)
                                                        <option value="{{ $mu->name }}" {{ old('default_unit', $product->default_unit ?? '') == $mu->name ? 'selected' : '' }}>
                                                            {{ $mu->name }} ({{ $mu->code }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">{{ __('messages.decimals_count') }}</label>
                                                <input type="number" name="decimals_count" class="form-control" min="0" max="5"
                                                    value="{{ old('decimals_count', $product->decimals_count ?? '0') }}">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Pricing Section --}}
                                    <div class="mb-4">
                                        <h6 class="text-muted small fw-bold text-uppercase border-bottom pb-2 mb-3">
                                            <i class="fas fa-tags me-1"></i> {{ __('messages.section_pricing') }}
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">{{ __('messages.cost_price') }} <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="number" name="cost_price" class="form-control" step="0.01"
                                                        value="{{ old('cost_price', $product->cost_price ?? '0.00') }}" required>
                                                    <span class="input-group-text">SAR</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">{{ __('messages.primary_cost') }}</label>
                                                <div class="input-group">
                                                    <input type="number" name="primary_cost" class="form-control" step="0.01"
                                                        value="{{ old('primary_cost', $product->primary_cost ?? '0.00') }}">
                                                    <span class="input-group-text">SAR</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">{{ __('messages.sale_price') }} <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="number" name="sale_price" class="form-control" step="0.01"
                                                        value="{{ old('sale_price', $product->sale_price ?? '0.00') }}" required>
                                                    <span class="input-group-text">SAR</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">{{ __('messages.tax_rate') }}</label>
                                                <div class="input-group">
                                                    <input type="number" name="tax_rate" class="form-control" step="0.01"
                                                        value="{{ old('tax_rate', $product->tax_rate ?? '15.00') }}" required>
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Dimensions Section --}}
                                    <div class="mb-4">
                                        <h6 class="text-muted small fw-bold text-uppercase border-bottom pb-2 mb-3">
                                            <i class="fas fa-ruler-combined me-1"></i> {{ __('messages.section_dimensions_weight') }}
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">{{ __('messages.length') }}</label>
                                                <input type="number" name="length" class="form-control" step="0.01"
                                                    value="{{ old('length', $product->length ?? '0.00') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">{{ __('messages.width') }}</label>
                                                <input type="number" name="width" class="form-control" step="0.01"
                                                    value="{{ old('width', $product->width ?? '0.00') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">{{ __('messages.height') }}</label>
                                                <input type="number" name="height" class="form-control" step="0.01"
                                                    value="{{ old('height', $product->height ?? '0.00') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">{{ __('messages.area') }}</label>
                                                <input type="number" name="area" class="form-control" step="0.01"
                                                    value="{{ old('area', $product->area ?? '0.00') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">{{ __('messages.weight') }}</label>
                                                <input type="number" name="weight" class="form-control" step="0.01"
                                                    value="{{ old('weight', $product->weight ?? '0.00') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">{{ __('messages.volume') }}</label>
                                                <input type="number" name="volume" class="form-control" step="0.01"
                                                    value="{{ old('volume', $product->volume ?? '0.00') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">{{ __('messages.size_dimension') }}</label>
                                                <input type="number" name="size_dimension" class="form-control" step="0.01"
                                                    value="{{ old('size_dimension', $product->size_dimension ?? '0.00') }}">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Inventory Settings Section --}}
                                    <div class="mb-4">
                                        <h6 class="text-muted small fw-bold text-uppercase border-bottom pb-2 mb-3">
                                            <i class="fas fa-warehouse me-1"></i> {{ __('messages.section_inventory_settings') }}
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">{{ __('messages.reorder_level') }}</label>
                                                <input type="number" name="reorder_level" class="form-control" step="0.01"
                                                    value="{{ old('reorder_level', $product->reorder_level ?? '0.00') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">{{ __('messages.reorder_quantity') }}</label>
                                                <input type="number" name="reorder_quantity" class="form-control" step="0.01"
                                                    value="{{ old('reorder_quantity', $product->reorder_quantity ?? '0.00') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">{{ __('messages.return_period') }}</label>
                                                <input type="number" name="return_period" class="form-control" min="0"
                                                    value="{{ old('return_period', $product->return_period ?? '0') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">{{ __('messages.purchase_inv_no') }}</label>
                                                <input type="text" name="purchase_inv_no" class="form-control"
                                                    value="{{ old('purchase_inv_no', $product->purchase_inv_no ?? '') }}">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Notes Section --}}
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">{{ __('messages.description') }}</label>
                                            <textarea name="description" class="form-control"
                                                rows="3">{{ old('description', $product->description ?? '') }}</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">{{ __('messages.deactivation_reason') }}</label>
                                            <textarea name="deactivation_reason" class="form-control"
                                                rows="3">{{ old('deactivation_reason', $product->deactivation_reason ?? '') }}</textarea>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded-3 border">
                                        <h6 class="mb-3"><i class="fas fa-image me-1"></i> {{ __('messages.product_image') }}</h6>
                                        <div class="mb-3 text-center border p-4 bg-white rounded">
                                            <i class="fas fa-box-open fa-5x text-muted mb-2"></i>
                                            <div class="small text-muted">{{ __('messages.select_image_file') }}</div>
                                        </div>
                                        <input type="file" name="image" class="form-control">
                                        <div class="mt-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                                    {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                                                <label class="form-check-label small">{{ __('messages.is_active') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Other Data Tab -->
                        <div class="tab-pane fade" id="other" role="tabpanel">

                            {{-- Classification Section --}}
                            <div class="mb-4">
                                <h6 class="text-muted small fw-bold text-uppercase border-bottom pb-2 mb-3">
                                    <i class="fas fa-tag me-1"></i> {{ __('messages.section_product_classification') }}
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">{{ __('messages.item_activity') }}</label>
                                        <input type="text" name="item_activity" class="form-control"
                                            value="{{ old('item_activity', $product->item_activity ?? '') }}"
                                            placeholder="e.g. Fast Moving">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">{{ __('messages.brand') }}</label>
                                        <input type="text" name="brand" class="form-control"
                                            value="{{ old('brand', $product->brand ?? '') }}"
                                            placeholder="e.g. Samsung">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">{{ __('messages.manufacturer_company') }}</label>
                                        <input type="text" name="manufacturer_company" class="form-control"
                                            value="{{ old('manufacturer_company', $product->manufacturer_company ?? '') }}"
                                            placeholder="e.g. Samsung Electronics">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">{{ __('messages.level') }}</label>
                                        <input type="text" name="level" class="form-control"
                                            value="{{ old('level', $product->level ?? '') }}"
                                            placeholder="e.g. Premium">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">{{ __('messages.measure') }}</label>
                                        <input type="text" name="measure" class="form-control"
                                            value="{{ old('measure', $product->measure ?? '') }}"
                                            placeholder="e.g. Piece">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">{{ __('messages.weights_base') }}</label>
                                        <input type="text" name="weights_base" class="form-control"
                                            value="{{ old('weights_base', $product->weights_base ?? '') }}"
                                            placeholder="e.g. Kg">
                                    </div>
                                </div>
                            </div>

                            {{-- Physical Attributes Section --}}
                            <div class="mb-4">
                                <h6 class="text-muted small fw-bold text-uppercase border-bottom pb-2 mb-3">
                                    <i class="fas fa-palette me-1"></i> {{ __('messages.section_physical_attributes') }}
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">{{ __('messages.color') }}</label>
                                        <input type="text" name="color" class="form-control"
                                            value="{{ old('color', $product->color ?? '') }}"
                                            placeholder="e.g. Red">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">{{ __('messages.material') }}</label>
                                        <input type="text" name="material" class="form-control"
                                            value="{{ old('material', $product->material ?? '') }}"
                                            placeholder="e.g. Plastic">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">{{ __('messages.season') }}</label>
                                        <input type="text" name="season" class="form-control"
                                            value="{{ old('season', $product->season ?? '') }}"
                                            placeholder="e.g. Summer">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">{{ __('messages.country_of_origin') }}</label>
                                        <input type="text" name="country_of_origin" class="form-control"
                                            value="{{ old('country_of_origin', $product->country_of_origin ?? '') }}"
                                            placeholder="e.g. Saudi Arabia">
                                    </div>
                                </div>
                            </div>

                            {{-- Logistics Section --}}
                            <div class="mb-4">
                                <h6 class="text-muted small fw-bold text-uppercase border-bottom pb-2 mb-3">
                                    <i class="fas fa-boxes me-1"></i> {{ __('messages.section_logistics_lifecycle') }}
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">{{ __('messages.item_storage') }}</label>
                                        <input type="text" name="items_storage" class="form-control"
                                            value="{{ old('items_storage', $product->items_storage ?? '') }}"
                                            placeholder="e.g. Cold Storage, Rack A3">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">{{ __('messages.inactivation_date') }}</label>
                                        <input type="date" name="inactivation_date" class="form-control"
                                            value="{{ old('inactivation_date', $product->inactivation_date ?? '') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">{{ __('messages.no_of_printing_times') }}</label>
                                        <input type="number" name="no_of_printing_times" class="form-control" min="0"
                                            value="{{ old('no_of_printing_times', $product->no_of_printing_times ?? '0') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">{{ __('messages.no_of_modifications') }}</label>
                                        <input type="number" name="no_of_modifications" class="form-control" min="0"
                                            value="{{ old('no_of_modifications', $product->no_of_modifications ?? '0') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">{{ __('messages.return_period_before_expiry') }}</label>
                                        <input type="number" name="return_period_before_expiry" class="form-control" min="0"
                                            value="{{ old('return_period_before_expiry', $product->return_period_before_expiry ?? '0') }}">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Flags Tab -->
                        <div class="tab-pane fade" id="flags" role="tabpanel">
                            <div class="row g-2">
                                @php
                                    $flagFields = [
                                        'is_sellable',
                                        'is_purchasable',
                                        'is_not_for_sale',
                                        'is_controlled',
                                        'allow_fractions',
                                        'is_service',
                                        'sold_in_cash',
                                        'is_asset',
                                        'use_partition',
                                        'is_compound',
                                        'is_component',
                                        'is_non_returnable',
                                        'use_expiry_date',
                                        'is_requirement',
                                        'show_in_vss',
                                        'use_custodians',
                                        'use_in_crm',
                                        'has_alternatives',
                                        'item_code_as_serial',
                                        'show_in_css',
                                        'is_weighted',
                                        'is_reserved'
                                    ];
                                @endphp
                                @foreach($flagFields as $field)
                                    <div class="col-md-4">
                                        <div class="p-2 border rounded-2 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="{{ $field }}" value="1"
                                                    id="f_{{ $field }}" {{ old($field, $product->$field ?? false) ? 'checked' : '' }}>
                                                <label class="form-check-label small cursor-pointer" for="f_{{ $field }}">
                                                    {{ __('messages.' . $field) }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Item Units Tab -->
                        <div class="tab-pane fade" id="units" role="tabpanel">
                            <div class="mb-4">
                                <h6 class="text-muted small fw-bold text-uppercase border-bottom pb-2 mb-3">
                                    <i class="fas fa-balance-scale me-1"></i> {{ __('messages.item_units') }}
                                </h6>
                                @include('inventory.products.partials.item-units')
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light p-3">
                    <button type="submit" class="btn btn-success px-5 shadow-sm">
                        <i class="fas fa-save me-2"></i> {{ __('messages.save') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <style>
        .tab-card-highlight {
            border-radius: 12px;
            background: #fff;
            border: 2px solid #0d9488;
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.12), 0 4px 16px rgba(13, 148, 136, 0.10) !important;
        }

        .glassy {
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .nav-tabs .nav-link {
            border: none;
            padding: 1rem;
            color: #6c757d;
            border-bottom: 2px solid transparent;
            font-weight: 600;
        }

        .nav-tabs .nav-link.active {
            background-color: transparent;
            border-bottom: 2px solid var(--bs-primary);
            color: var(--bs-primary);
        }

        .tiny {
            font-size: 0.75rem;
        }
    </style>
@endsection
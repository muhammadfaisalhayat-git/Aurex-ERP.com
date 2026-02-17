@extends('layouts.app')

@section('title', __('messages.barcode_settings'))

@section('content')
    <div class="container-fluid p-4">
        <div class="page-header mb-4 d-flex justify-content-between align-items-center">
            <h1 class="page-title">{{ __('messages.barcode_settings') }}</h1>
            <div class="btn-group gap-2">
                <a href="{{ route('inventory.barcodes.print', ['items' => [['product_id' => 1, 'quantity' => 1]]]) }}" 
                   target="_blank" class="btn btn-primary btn-sm shadow-sm">
                    <i class="fas fa-print me-1"></i> {{ __('messages.print_test_label') ?? 'Print Test Label' }}
                </a>
                <a href="{{ route('inventory.barcodes.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('messages.back_to_generator') }}
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-2 font-20"></i>
                    <div>
                        <strong>{{ __('messages.success') }}!</strong> {{ session('success') }}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('inventory.barcodes.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-lg-8">
                    <!-- Desktop-style Tabbed Settings -->
                    <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                        <div class="card-header bg-white p-0 border-bottom">
                            <ul class="nav nav-tabs border-0" id="settingsTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active py-3 px-4 fw-bold border-0 rounded-0" id="general-tab"
                                        data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                                        <i class="fas fa-cog me-2"></i>{{ __('messages.tab_general') }}
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-3 px-4 fw-bold border-0 rounded-0" id="barcode-tab"
                                        data-bs-toggle="tab" data-bs-target="#barcode" type="button" role="tab">
                                        <i class="fas fa-barcode me-2"></i>{{ __('messages.tab_barcode') }}
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-3 px-4 fw-bold border-0 rounded-0" id="readable-tab"
                                        data-bs-toggle="tab" data-bs-target="#readable" type="button" role="tab">
                                        <i class="fas fa-font me-2"></i>{{ __('messages.tab_human_readable') }}
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-3 px-4 fw-bold border-0 rounded-0" id="designer-tab"
                                        data-bs-toggle="tab" data-bs-target="#designer" type="button" role="tab">
                                        <i
                                            class="fas fa-paint-brush me-2"></i>{{ __('messages.tab_designer') ?? 'Designer' }}
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body p-4 bg-light bg-opacity-50">
                            <div class="tab-content" id="settingsTabsContent">
                                <!-- Tab: General -->
                                <div class="tab-pane fade show active" id="general" role="tabpanel">
                                    <div class="row g-4">
                                        <div class="col-md-12">
                                            <div class="border rounded p-3 bg-white shadow-sm mb-2">
                                                <h6 class="fw-bold mb-3 border-bottom pb-2 text-muted small text-uppercase">
                                                    {{ __('messages.custom_text') }}</h6>
                                                <div class="row g-3">
                                                    <div class="col-md-12">
                                                        <label
                                                            class="form-label small mb-1">{{ __('messages.custom_text') }}</label>
                                                        <input type="text" name="custom_text"
                                                            class="form-control preview-trigger"
                                                            value="{{ $settings->custom_text }}"
                                                            placeholder="e.g. Aurex ERP">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">{{ __('messages.page_size') }}</label>
                                            <select name="page_size" class="form-select">
                                                <option value="A4" {{ $settings->page_size == 'A4' ? 'selected' : '' }}>A4
                                                    (Print Sheet)</option>
                                                <option value="Label" {{ $settings->page_size == 'Label' ? 'selected' : '' }}>
                                                    Single Label (Thermal)</option>
                                                <option value="Custom" {{ $settings->page_size == 'Custom' ? 'selected' : '' }}>Custom Size</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">{{ __('messages.labels_per_row') }}</label>
                                            <input type="number" name="labels_per_row" class="form-control"
                                                value="{{ $settings->labels_per_row }}">
                                        </div>

                                        <div class="col-md-12">
                                            <div class="border rounded p-3 bg-white shadow-sm mt-2">
                                                <h6 class="fw-bold mb-3 border-bottom pb-2 text-muted small text-uppercase">
                                                    {{ __('messages.margins') }} (mm)</h6>
                                                <div class="row g-3">
                                                    <div class="col-6 col-md-3">
                                                        <label
                                                            class="form-label small mb-1">{{ __('messages.top') }}</label>
                                                        <input type="number" name="margin_top" class="form-control"
                                                            value="{{ $settings->margin_top }}" step="0.1">
                                                    </div>
                                                    <div class="col-6 col-md-3">
                                                        <label
                                                            class="form-label small mb-1">{{ __('messages.bottom') }}</label>
                                                        <input type="number" name="margin_bottom" class="form-control"
                                                            value="{{ $settings->margin_bottom }}" step="0.1">
                                                    </div>
                                                    <div class="col-6 col-md-3">
                                                        <label
                                                            class="form-label small mb-1">{{ __('messages.left') }}</label>
                                                        <input type="number" name="margin_left" class="form-control"
                                                            value="{{ $settings->margin_left }}" step="0.1">
                                                    </div>
                                                    <div class="col-6 col-md-3">
                                                        <label
                                                            class="form-label small mb-1">{{ __('messages.right') }}</label>
                                                        <input type="number" name="margin_right" class="form-control"
                                                            value="{{ $settings->margin_right }}" step="0.1">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab: Bar Code -->
                                <div class="tab-pane fade" id="barcode" role="tabpanel">
                                    <div class="row g-4">
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">{{ __('messages.barcode_type') }}</label>
                                            <select name="barcode_type" class="form-select preview-trigger">
                                                <option value="C128" {{ $settings->barcode_type == 'C128' ? 'selected' : '' }}>Code 128</option>
                                                <option value="C39" {{ $settings->barcode_type == 'C39' ? 'selected' : '' }}>
                                                    Code 39</option>
                                                <option value="EAN13" {{ $settings->barcode_type == 'EAN13' ? 'selected' : '' }}>EAN-13</option>
                                                <option value="UPCA" {{ $settings->barcode_type == 'UPCA' ? 'selected' : '' }}>UPC-A</option>
                                            </select>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="border rounded p-3 bg-white shadow-sm">
                                                <h6 class="fw-bold mb-3 border-bottom pb-2 text-muted small text-uppercase">
                                                    {{ __('messages.dimensions') }}</h6>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label
                                                            class="form-label small mb-1">{{ __('messages.label_width') }}
                                                            (mm)</label>
                                                        <input type="number" name="label_width"
                                                            class="form-control preview-trigger"
                                                            value="{{ $settings->label_width }}" step="0.01">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label
                                                            class="form-label small mb-1">{{ __('messages.barcode_height') }}
                                                            (mm)</label>
                                                        <input type="number" name="label_height"
                                                            class="form-control preview-trigger"
                                                            value="{{ $settings->label_height }}" step="0.01">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label
                                                            class="form-label small mb-1">{{ __('messages.barcode_color') }}</label>
                                                        <div class="input-group">
                                                            <input type="color" name="barcode_color"
                                                                class="form-control form-control-color p-1 preview-trigger"
                                                                value="{{ $settings->barcode_color }}"
                                                                style="width: 45px; height: 38px;">
                                                            <input type="text" class="form-control color-text-input"
                                                                value="{{ $settings->barcode_color }}"
                                                                placeholder="#000000">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="border rounded p-3 bg-white shadow-sm">
                                                <h6 class="fw-bold mb-3 border-bottom pb-2 text-muted small text-uppercase">
                                                    {{ __('messages.symbology_options') }}</h6>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="check_digit"
                                                        id="checkDigit" {{ $settings->check_digit ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="checkDigit">{{ __('messages.check_digit') }}</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="ucc_ean_128"
                                                        id="ean128" {{ $settings->ucc_ean_128 ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="ean128">{{ __('messages.ucc_ean_128') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab: Human Readable -->
                                <div class="tab-pane fade" id="readable" role="tabpanel">
                                    <div class="row g-4">
                                        <div class="col-md-12">
                                            <div class="border rounded p-3 bg-white shadow-sm mb-4">
                                                <h6 class="fw-bold mb-3 border-bottom pb-2 text-muted small text-uppercase">
                                                    {{ __('messages.visibility') }}</h6>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <div class="form-check form-switch mb-2">
                                                            <input class="form-check-input preview-trigger" type="checkbox"
                                                                name="show_product_name" id="showName" {{ $settings->show_product_name ? 'checked' : '' }}>
                                                            <label class="form-check-label fw-bold"
                                                                for="showName">{{ __('messages.show_product_name') }}</label>
                                                        </div>
                                                        <div class="form-check form-switch mb-2">
                                                            <input class="form-check-input preview-trigger" type="checkbox"
                                                                name="show_product_code" id="showCode" {{ $settings->show_product_code ? 'checked' : '' }}>
                                                            <label class="form-check-label fw-bold"
                                                                for="showCode">{{ __('messages.show_product_code') }}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check form-switch mb-2">
                                                            <input class="form-check-input preview-trigger" type="checkbox"
                                                                name="show_product_price" id="showPrice" {{ $settings->show_product_price ? 'checked' : '' }}>
                                                            <label class="form-check-label fw-bold"
                                                                for="showPrice">{{ __('messages.show_product_price') }}</label>
                                                        </div>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input preview-trigger" type="checkbox"
                                                                name="show_custom_text" id="showCustom" {{ $settings->show_custom_text ? 'checked' : '' }}>
                                                            <label class="form-check-label fw-bold"
                                                                for="showCustom">{{ __('messages.show_custom_text') }}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 text-settings">
                                            <div class="border rounded p-3 bg-white shadow-sm mb-4">
                                                <h6 class="fw-bold mb-3 border-bottom pb-2 text-muted small text-uppercase">
                                                    {{ __('messages.tab_font') }}</h6>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label
                                                            class="form-label small mb-1">{{ __('messages.alignment') }}</label>
                                                        <select name="content_alignment"
                                                            class="form-select preview-trigger">
                                                            <option value="left" {{ $settings->content_alignment == 'left' ? 'selected' : '' }}>{{ __('messages.left') }}</option>
                                                            <option value="center" {{ $settings->content_alignment == 'center' ? 'selected' : '' }}>{{ __('messages.center') }}</option>
                                                            <option value="right" {{ $settings->content_alignment == 'right' ? 'selected' : '' }}>{{ __('messages.right') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label
                                                            class="form-label small mb-1">{{ __('messages.text_color') }}</label>
                                                        <div class="input-group">
                                                            <input type="color" name="text_color"
                                                                class="form-control form-control-color p-1 preview-trigger"
                                                                value="{{ $settings->text_color }}"
                                                                style="width: 45px; height: 38px;">
                                                            <input type="text" class="form-control color-text-input"
                                                                value="{{ $settings->text_color }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <hr>
                                                <label
                                                    class="form-label fw-bold small text-muted text-uppercase mb-3">{{ __('messages.template') }}</label>
                                                <select name="template" class="form-select preview-trigger mb-3"
                                                    id="templateSelect">
                                                    <option value="default" {{ $settings->template == 'default' ? 'selected' : '' }}>Standard</option>
                                                    <option value="modern" {{ $settings->template == 'modern' ? 'selected' : '' }}>Modern (Bold)</option>
                                                    <option value="compact" {{ $settings->template == 'compact' ? 'selected' : '' }}>Compact (Tiny)</option>
                                                    <option value="custom" {{ $settings->template == 'custom' ? 'selected' : '' }}>Custom</option>
                                                </select>

                                                <div id="customTemplateControls"
                                                    style="{{ $settings->template == 'custom' ? '' : 'display:none;' }}">
                                                    <div class="row g-2">
                                                        <div class="col-6 mb-2">
                                                            <label
                                                                class="form-label small mb-0">{{ __('messages.name_size') }}</label>
                                                            <input type="number" name="font_size_name"
                                                                class="form-control form-control-sm preview-trigger"
                                                                value="{{ $settings->font_size_name }}">
                                                        </div>
                                                        <div class="col-6 mb-2">
                                                            <label
                                                                class="form-label small mb-0">{{ __('messages.code_size') }}</label>
                                                            <input type="number" name="font_size_code"
                                                                class="form-control form-control-sm preview-trigger"
                                                                value="{{ $settings->font_size_code }}">
                                                        </div>
                                                        <div class="col-6">
                                                            <label
                                                                class="form-label small mb-0">{{ __('messages.price_size') }}</label>
                                                            <input type="number" name="font_size_price"
                                                                class="form-control form-control-sm preview-trigger"
                                                                value="{{ $settings->font_size_price }}">
                                                        </div>
                                                        <div class="col-6">
                                                            <label
                                                                class="form-label small mb-0">{{ __('messages.custom_text_size') }}</label>
                                                            <input type="number" name="font_size_custom"
                                                                class="form-control form-control-sm preview-trigger"
                                                                value="{{ $settings->font_size_custom }}">
                                                        </div>
                                                        <div class="col-12 mt-2">
                                                            <label
                                                                class="form-label small mb-0">{{ __('messages.barcode_scaling') }}</label>
                                                            <input type="number" name="font_size_barcode"
                                                                class="form-control form-control-sm preview-trigger"
                                                                value="{{ $settings->font_size_barcode }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab: Designer -->
                                <div class="tab-pane fade" id="designer" role="tabpanel">
                                    <div class="row g-4">
                                        <div class="col-md-12">
                                            <div class="designer-canvas-container bg-white border rounded p-4 shadow-sm text-center mb-2 overflow-auto"
                                                style="min-height: 400px; display: flex; align-items: center; justify-content: center; background-image: radial-gradient(#ddd 1px, transparent 1px); background-size: 20px 20px;">
                                                <div id="designerCanvas" class="mx-auto preview-label designer-mode shadow-lg"
                                                    style="position: relative; overflow: visible; background: #fff; border: 1px solid #000; transition: none; display: block;">
                                                    <div id="drag-name" class="draggable-element" data-element="name"
                                                        style="position: absolute; cursor: move; white-space: nowrap; user-select: none;">
                                                        {{ __('messages.product_name') }}</div>
                                                    <div id="drag-barcode" class="draggable-element preview-barcode"
                                                        data-element="barcode"
                                                        style="position: absolute; cursor: move; user-select: none; line-height: 1;">
                                                        12345678</div>
                                                    <div id="drag-code" class="draggable-element" data-element="code"
                                                        style="position: absolute; cursor: move; white-space: nowrap; user-select: none;">
                                                        PROD-123</div>
                                                    <div id="drag-price" class="draggable-element" data-element="price"
                                                        style="position: absolute; cursor: move; white-space: nowrap; user-select: none;">
                                                        99.99 SAR</div>
                                                    <div id="drag-custom" class="draggable-element" data-element="custom"
                                                        style="position: absolute; cursor: move; white-space: nowrap; user-select: none;">
                                                        {{ $settings->custom_text }}</div>
                                                </div>
                                            </div>
                                            <div class="alert alert-info py-2 small mb-4">
                                                <i class="fas fa-info-circle me-1"></i> Drag and drop elements on the label above
                                                to position them. Use Font Size controls in other tabs to resize.
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="border rounded p-3 bg-white shadow-sm">
                                                <h6 class="fw-bold mb-3 border-bottom pb-2 text-muted small text-uppercase">
                                                    {{ __('messages.coordinates') ?? 'Coordinates' }} (mm)</h6>
                                                <div class="row g-3">
                                                    @foreach (['name', 'barcode', 'code', 'price', 'custom'] as $el)
                                                        <div class="col-md-4 col-6">
                                                            <div class="p-2 border rounded bg-light">
                                                                <label
                                                                    class="form-label small fw-bold mb-1 text-primary text-uppercase">{{ __("messages.$el") ?? ucfirst($el) }}</label>
                                                                <div class="row g-1">
                                                                    <div class="col-6">
                                                                        <div class="input-group input-group-sm">
                                                                            <span class="input-group-text p-1"
                                                                                style="font-size: 10px;">X</span>
                                                                            <input type="number" step="0.1"
                                                                                name="pos_x_{{ $el }}"
                                                                                id="pos_x_{{ $el }}"
                                                                                class="form-control coord-trigger"
                                                                                value="{{ $settings->{"pos_x_$el"} }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <div class="input-group input-group-sm">
                                                                            <span class="input-group-text p-1"
                                                                                style="font-size: 10px;">Y</span>
                                                                            <input type="number" step="0.1"
                                                                                name="pos_y_{{ $el }}"
                                                                                id="pos_y_{{ $el }}"
                                                                                class="form-control coord-trigger"
                                                                                value="{{ $settings->{"pos_y_$el"} }}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white p-3 d-flex justify-content-between border-top">
                            <button type="button" id="reset-defaults-btn" class="btn btn-outline-danger px-4 fw-bold shadow-sm py-2">
                                <i class="fas fa-undo me-1"></i> {{ __('messages.reset_to_defaults') ?? 'Reset to Defaults' }}
                            </button>
                            <button type="submit" class="btn btn-success px-5 fw-bold shadow-sm py-2">
                                <i class="fas fa-save me-1"></i> {{ __('messages.save_settings') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="sticky-top" style="top: 20px;">
                        <div class="card mb-4 bg-white shadow-sm border-0">
                            <div class="card-header bg-primary text-white py-3">
                                <h5 class="card-title fw-bold mb-0">
                                    <i class="fas fa-eye me-2"></i>{{ __('messages.label_preview') }}
                                </h5>
                            </div>
                            <div
                                class="card-body d-flex justify-content-center align-items-center p-5 bg-secondary bg-opacity-10 rounded-bottom">
                                <div id="labelPreview" class="preview-label">
                                    <div class="preview-name" id="previewName">Sample Product Name</div>
                                    <div class="preview-barcode">12345678</div>
                                    <div class="preview-code" id="previewCode">PROD-123</div>
                                    <div class="preview-price" id="previewPrice">99.99 SAR</div>
                                    <div class="preview-custom" id="previewCustom">Custom Text</div>
                                </div>
                            </div>
                            <div class="card-footer bg-white text-center py-2 border-top">
                                <small class="text-muted"><i
                                        class="fas fa-info-circle me-1"></i>{{ __('messages.this_is_a_live_preview') }}</small>
                            </div>
                        </div>

                        <div class="card bg-info text-white shadow-sm border-0">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-lightbulb me-3 fa-2x"></i>
                                    <div>
                                        <h6 class="fw-bold">{{ __('messages.printer_tip_title') }}</h6>
                                        <p class="small mb-0 opacity-75">{{ __('messages.printer_tip_desc') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <style>
        .nav-tabs .nav-link {
            color: #6c757d;
            background: none;
            transition: all 0.2s ease;
        }

        .nav-tabs .nav-link:hover {
            background: #f8f9fa;
            color: #0d6efd;
        }

        .nav-tabs .nav-link.active {
            color: #0d6efd;
            background: #fff;
            border-bottom: 3px solid #0d6efd !important;
        }

        .preview-label {
            background: #fff;
            border: 1px solid #ddd;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 8px;
            text-align: center;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .preview-name {
            font-weight: 600;
            font-size: 11px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 2px;
        }

        .preview-barcode {
            font-family: 'Libre Barcode 128', cursive;
            font-size: 45px;
            line-height: 1;
            margin: 4px 0;
        }

        .preview-code {
            font-size: 9px;
            color: #666;
            margin-bottom: 2px;
        }

        .preview-price {
            font-weight: 700;
            font-size: 13px;
            color: #000;
        }

        .preview-custom {
            font-size: 9px;
            font-style: italic;
            color: #555;
            margin-top: 2px;
        }

        /* Template Overrides */
        .template-compact .preview-name {
            font-size: 9px;
        }

        .template-compact .preview-barcode {
            font-size: 32px;
        }

        .template-compact .preview-price {
            font-size: 11px;
        }

        .template-modern .preview-name {
            font-weight: 800;
            font-size: 12px;
        }
    /* Designer Styles */
        .designer-mode {
            box-shadow: 0 20px 50px rgba(0,0,0,0.15) !important;
            border: 1px solid #000 !important;
            background: #fff !important;
            cursor: crosshair;
        }
        .designer-mode .draggable-element {
            border: 1px dashed transparent;
            padding: 2px 4px;
            transition: border-color 0.2s;
        }
        .designer-mode .draggable-element:hover {
            border-color: #0d6efd;
            background: rgba(13, 110, 253, 0.05);
        }
        .designer-mode .draggable-element.dragging {
            opacity: 0.5;
            border-color: #0d6efd;
            z-index: 1000;
        }
    </style>

    <script>
        document.addEventListener('turbo:load', function() {
            // UI Interaction for color inputs
            document.querySelectorAll('.form-control-color').forEach(picker => {
                const textInput = picker.nextElementSibling;
                picker.addEventListener('input', () => {
                    textInput.value = picker.value.toUpperCase();
                    updatePreview();
                });
                textInput.addEventListener('input', () => {
                    if(/^#[0-9A-F]{6}$/i.test(textInput.value)) {
                        picker.value = textInput.value;
                        updatePreview();
                    }
                });
            });

            const previewLabel = document.getElementById('labelPreview');
            const widthInput = document.getElementsByName('label_width')[0];
            const heightInput = document.getElementsByName('label_height')[0];
            const showNameCheck = document.getElementById('showName');
            const showCodeCheck = document.getElementById('showCode');
            const showPriceCheck = document.getElementById('showPrice');
            const showCustomCheck = document.getElementById('showCustom');
            const customTextInput = document.getElementsByName('custom_text')[0];
            const templateSelect = document.getElementById('templateSelect');
            const alignmentSelect = document.getElementsByName('content_alignment')[0];
            const barcodeColorInput = document.getElementsByName('barcode_color')[0];
            const textColorInput = document.getElementsByName('text_color')[0];

            // Font Size Inputs
            const fontSizeNameInput = document.getElementsByName('font_size_name')[0];
            const fontSizeCodeInput = document.getElementsByName('font_size_code')[0];
            const fontSizePriceInput = document.getElementsByName('font_size_price')[0];
            const fontSizeCustomInput = document.getElementsByName('font_size_custom')[0];
            const fontSizeBarcodeInput = document.getElementsByName('font_size_barcode')[0];

            const nameDiv = document.getElementById('previewName');
            const codeDiv = document.getElementById('previewCode');
            const priceDiv = document.getElementById('previewPrice');
            const customDiv = document.getElementById('previewCustom');
            const barcodeDiv = document.querySelector('.preview-barcode');

            // Designer elements
            const designerCanvas = document.getElementById('designerCanvas');
            const draggables = document.querySelectorAll('.draggable-element');

            function updatePreview() {
                // Update dimensions (mm to px)
                const scale = 3.5; 
                const labelWidthMm = parseFloat(widthInput.value) || 50;
                const labelHeightMm = parseFloat(heightInput.value) || 30;
                const labelWidthPx = labelWidthMm * scale;
                const labelHeightPx = labelHeightMm * scale;

                // Apply to both previews
                [previewLabel, designerCanvas].forEach(canvas => {
                    if (!canvas) return;
                    canvas.style.width = labelWidthPx + 'px';
                    canvas.style.height = labelHeightPx + 'px';
                });

                // If template is NOT custom, we might use flex (stack)
                // But if user wants "effective templates" that act as presets for coordinates:
                // We'll apply coordinate presets when template changes.

                const isCustom = templateSelect.value === 'custom';

                // Visibility
                [nameDiv, codeDiv, priceDiv, customDiv].forEach(div => {
                    const el = div.id.replace('preview', '').toLowerCase();
                    const check = document.getElementById('show' + el.charAt(0).toUpperCase() + el.slice(1));
                    div.style.display = check.checked ? 'block' : 'none';

                    // Also update designer elements
                    const dragEl = document.getElementById('drag-' + el);
                    if (dragEl) dragEl.style.display = check.checked ? 'block' : 'none';
                });

                // Barcode always visible in designer
                document.getElementById('drag-barcode').style.display = 'block';

                // Colors & Fonts
                const applyStyles = (div, dragEl) => {
                    if (!div) return;
                    div.style.color = textColorInput.value;
                    if (dragEl) {
                        dragEl.style.color = textColorInput.value;
                        if (isCustom) {
                            div.style.fontSize = fontSizeNameInput.value + 'px'; // Defaulting to one font size for now or specific ones
                        }
                    }
                };

                // Specific Font Sizes
                nameDiv.style.fontSize = fontSizeNameInput.value + 'px';
                codeDiv.style.fontSize = fontSizeCodeInput.value + 'px';
                priceDiv.style.fontSize = fontSizePriceInput.value + 'px';
                customDiv.style.fontSize = fontSizeCustomInput.value + 'px';
                barcodeDiv.style.fontSize = fontSizeBarcodeInput.value + 'px';

                // Sync designer fonts
                document.getElementById('drag-name').style.fontSize = nameDiv.style.fontSize;
                document.getElementById('drag-code').style.fontSize = codeDiv.style.fontSize;
                document.getElementById('drag-price').style.fontSize = priceDiv.style.fontSize;
                document.getElementById('drag-custom').style.fontSize = customDiv.style.fontSize;
                document.getElementById('drag-barcode').style.fontSize = barcodeDiv.style.fontSize;

                // Position Designer Elements
                ['name', 'barcode', 'code', 'price', 'custom'].forEach(el => {
                    const dragEl = document.getElementById('drag-' + el);
                    if (dragEl) {
                        const xMm = parseFloat(document.getElementById('pos_x_' + el).value) || 0;
                        const yMm = parseFloat(document.getElementById('pos_y_' + el).value) || 0;

                        // Convert mm to px for canvas
                        dragEl.style.left = (xMm * scale) + 'px';
                        dragEl.style.top = (yMm * scale) + 'px';

                        // If we are in Designer mode (absolute), center the element horizontally based on its width if x is at center
                        // Actually, let's just use the X as the left edge, or if they want center-aligned:
                        dragEl.style.transform = 'translateX(-50%)'; // Assume X is center
                    }
                });

                // Update main preview to match designer if needed
                // For now, let's make the main preview also absolute to show exactly what's in designer
                previewLabel.style.position = 'relative';
                [nameDiv, codeDiv, priceDiv, customDiv, barcodeDiv].forEach(div => {
                    div.style.position = 'absolute';
                    const el = div.id ? div.id.replace('preview', '').toLowerCase() : 'barcode';
                    const xMm = parseFloat(document.getElementById('pos_x_' + el).value) || 0;
                    const yMm = parseFloat(document.getElementById('pos_y_' + el).value) || 0;
                    div.style.left = (xMm * scale) + 'px';
                    div.style.top = (yMm * scale) + 'px';
                    div.style.transform = 'translateX(-50%)';
                    div.style.width = '100%';
                    div.style.textAlign = 'center';
                });

                customDiv.textContent = customTextInput.value;
                document.getElementById('customTextInputContainer').style.display = showCustomCheck.checked ? 'block' : 'none';
                document.getElementById('customTemplateControls').style.display = isCustom ? 'block' : 'none';
            }

            // Drag and Drop Logic
            let activeElement = null;
            let startX, startY, initialX, initialY;

            draggables.forEach(el => {
                el.addEventListener('mousedown', function(e) {
                    activeElement = this;
                    const elementId = this.getAttribute('data-element');
                    const xInput = document.getElementById('pos_x_' + elementId);
                    const yInput = document.getElementById('pos_y_' + elementId);

                    const scale = 3.5;
                    initialX = parseFloat(xInput.value) * scale;
                    initialY = parseFloat(yInput.value) * scale;

                    startX = e.clientX;
                    startY = e.clientY;

                    this.classList.add('dragging');
                    e.preventDefault();
                });
            });

            document.addEventListener('mousemove', function(e) {
                if (!activeElement) return;

                const dx = e.clientX - startX;
                const dy = e.clientY - startY;

                const scale = 3.5;
                const newXPx = initialX + dx;
                const newYPx = initialY + dy;

                const elementId = activeElement.getAttribute('data-element');
                const xInput = document.getElementById('pos_x_' + elementId);
                const yInput = document.getElementById('pos_y_' + elementId);

                // mm = px / scale
                xInput.value = (newXPx / scale).toFixed(1);
                yInput.value = (newYPx / scale).toFixed(1);

                updatePreview();
            });

            document.addEventListener('mouseup', function() {
                if (activeElement) {
                    activeElement.classList.remove('dragging');
                    activeElement = null;
                }
            });

            // Template Presets
            templateSelect.addEventListener('change', function() {
                const val = this.value;
                const width = parseFloat(widthInput.value) || 50;
                const centerX = width / 2;

                if (val === 'default') {
                    updateCoords('name', centerX, 5, 10);
                    updateCoords('barcode', centerX, 12, 45);
                    updateCoords('code', centerX, 22, 8);
                    updateCoords('price', centerX, 25, 12);
                    updateCoords('custom', centerX, 28, 8);
                } else if (val === 'modern') {
                    updateCoords('name', centerX, 4, 12);
                    updateCoords('barcode', centerX, 10, 40);
                    updateCoords('code', centerX, 21, 9);
                    updateCoords('price', centerX, 25, 14);
                    updateCoords('custom', centerX, 28, 9);
                } else if (val === 'compact') {
                    updateCoords('name', centerX, 3, 9);
                    updateCoords('barcode', centerX, 8, 32);
                    updateCoords('code', centerX, 17, 7);
                    updateCoords('price', centerX, 21, 10);
                    updateCoords('custom', centerX, 24, 7);
                }
                updatePreview();
            });

            function updateCoords(el, x, y, fontSize) {
                document.getElementById('pos_x_' + el).value = x;
                document.getElementById('pos_y_' + el).value = y;
                const fsInput = document.getElementsByName('font_size_' + el)[0];
                if (fsInput) fsInput.value = fontSize;
            }

            // Reset to Defaults
            document.getElementById('reset-defaults-btn').addEventListener('click', function() {
                Swal.fire({
                    title: '{{ __("messages.are_you_sure") ?? "Are you sure?" }}',
                    text: '{{ __("messages.reset_barcode_warning") ?? "This will reset all barcode settings to their factory defaults." }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '{{ __("messages.yes_reset_it") ?? "Yes, reset it!" }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route("inventory.barcodes.settings.reset") }}';
                        
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        
                        form.appendChild(csrfToken);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });

            const triggers = document.querySelectorAll('.preview-trigger, .coord-trigger, [name^="label_"], [name^="font_size_"]');
            triggers.forEach(input => {
                input.addEventListener('input', updatePreview);
                input.addEventListener('change', updatePreview);
            });

            updatePreview();
        });
    </script>
@endsection
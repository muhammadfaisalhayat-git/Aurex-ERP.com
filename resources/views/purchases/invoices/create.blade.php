@extends('layouts.app')

@section('title', __('messages.create_purchase_invoice'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.create_purchase_invoice') }}</h1>
            <a href="{{ route('purchases.invoices.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
            </a>
        </div>

        <!-- OCR Upload Section -->
        <div class="card mb-4" id="ocr-section">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-camera"></i> Invoice Recognition
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div id="drop-zone" class="border border-2 border-dashed rounded p-4 text-center" style="cursor: pointer; min-height: 200px; position: relative;">
                            <input type="file" id="invoice-file-input" accept="image/*,.pdf" style="display: none; pointer-events: none;">
                            <div id="upload-prompt" style="pointer-events: none;">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <p class="mb-2">Drag and drop invoice image here or click to browse</p>
                                <small class="text-muted">Supported formats: JPG, PNG, PDF (Max 10MB)</small>
                            </div>
                            <div id="preview-container" style="display: none; pointer-events: none;">
                                <img id="invoice-preview" src="" alt="Invoice Preview" style="max-width: 100%; max-height: 400px;">
                                <div class="mt-2" style="pointer-events: all;">
                                    <button type="button" class="btn btn-sm btn-danger" id="clear-upload">
                                        <i class="fas fa-times"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success btn-lg" id="extract-btn" disabled>
                                <i class="fas fa-magic"></i> Extract Data
                            </button>
                            <div id="ocr-status" class="mt-3" style="display: none;">
                                <div class="alert alert-info" role="alert">
                                    <i class="fas fa-spinner fa-spin"></i> Processing invoice...
                                </div>
                            </div>
                            <div id="ocr-result" class="mt-3" style="display: none;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('purchases.invoices.store') }}" method="POST" id="invoice-form">
            @csrf
            <input type="hidden" name="ocr_temp_file" id="ocr_temp_file">
            <input type="hidden" name="vendor_tax_id" id="vendor_tax_id">
            <input type="hidden" name="vendor_address" id="vendor_address">
            <div class="row">
                <div class="col-md-9">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="vendor_id" class="form-label">{{ __('messages.vendor') }} <span class="text-danger">*</span></label>
                                    <select class="form-control select2 @error('vendor_id') is-invalid @enderror" id="vendor_id" name="vendor_id" required>
                                        <option value="">{{ __('messages.select_vendor') }}</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                                {{ $vendor->name_en }} / {{ $vendor->name_ar }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vendor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="invoice_number" class="form-label">{{ __('messages.invoice_number') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('invoice_number') is-invalid @enderror" id="invoice_number" name="invoice_number" value="{{ old('invoice_number') }}" required>
                                    @error('invoice_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="document_number" class="form-label">{{ __('messages.document_number') }}</label>
                                    <input type="text" class="form-control" id="document_number" value="{{ $nextDocumentNumber }}" readonly>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="invoice_date" class="form-label">{{ __('messages.invoice_date') }} <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('invoice_date') is-invalid @enderror" id="invoice_date" name="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                                    @error('invoice_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="due_date" class="form-label">{{ __('messages.due_date') }}</label>
                                    <input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date') }}">
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="payment_terms" class="form-label">{{ __('messages.payment_terms') }} <span class="text-danger">*</span></label>
                                    <select class="form-control @error('payment_terms') is-invalid @enderror" id="payment_terms" name="payment_terms" required>
                                        <option value="cash" {{ old('payment_terms') == 'cash' ? 'selected' : '' }}>{{ __('messages.cash') }}</option>
                                        <option value="credit" {{ old('payment_terms') == 'credit' ? 'selected' : '' }}>{{ __('messages.credit') }}</option>
                                        <option value="installment" {{ old('payment_terms') == 'installment' ? 'selected' : '' }}>{{ __('messages.installment') }}</option>
                                    </select>
                                    @error('payment_terms')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ __('messages.items') }}</h5>
                            <button type="button" class="btn btn-sm btn-success" id="add-item">
                                <i class="fas fa-plus"></i> {{ __('messages.add_item') }}
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0" id="items-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 48%">{{ __('messages.product') }}</th>
                                            <th style="width: 18%">{{ __('messages.quantity') }} / {{ __('messages.unit') ?? 'Unit' }}</th>
                                            <th style="width: 11%">{{ __('messages.unit_price') }}</th>
                                            <th style="width: 9%">{{ __('messages.tax') }} %</th>
                                            <th style="width: 12%">{{ __('messages.total') }}</th>
                                            <th style="width: 2%"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Item rows will be added here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <label for="notes" class="form-label">{{ __('messages.notes') }}</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('messages.location') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="branch_id" class="form-label">{{ __('messages.branch') }} <span class="text-danger">*</span></label>
                                <select class="form-control @error('branch_id') is-invalid @enderror" id="branch_id" name="branch_id" required>
                                    <option value="">{{ __('messages.select_branch') }}</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="warehouse_id" class="form-label">{{ __('messages.warehouse') }} <span class="text-danger">*</span></label>
                                <select class="form-control @error('warehouse_id') is-invalid @enderror" id="warehouse_id" name="warehouse_id" required>
                                    <option value="">{{ __('messages.select_warehouse') }}</option>
                                    <!-- Populated via AJAX -->
                                </select>
                                @error('warehouse_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('messages.summary') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ __('messages.subtotal') }}</span>
                                <span id="summary-subtotal">0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ __('messages.tax_amount') }}</span>
                                <span id="summary-tax">0.00</span>
                            </div>
                            <div class="mb-2">
                                <label for="discount_amount" class="form-label">{{ __('messages.discount') }}</label>
                                <input type="number" step="0.01" class="form-control form-control-sm" id="discount_amount" name="discount_amount" value="{{ old('discount_amount', 0) }}">
                            </div>
                            <div class="mb-2">
                                <label for="shipping_amount" class="form-label">{{ __('messages.shipping_amount') }}</label>
                                <input type="number" step="0.01" class="form-control form-control-sm" id="shipping_amount" name="shipping_amount" value="{{ old('shipping_amount', 0) }}">
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold h5">
                                <span>{{ __('messages.total') }}</span>
                                <span id="summary-total">0.00</span>
                                <input type="hidden" name="total_amount" id="total_amount_input">
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save"></i> {{ __('messages.save_invoice') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Row Template -->
    <template id="item-row-template">
        <tr class="item-row">
            <td>
                <select name="items[INDEX][product_id]" class="form-control product-select select2" required>
                    <option value="">{{ __('messages.select_product') }}</option>
                </select>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text p-0" style="width: 40%">
                        <select class="form-select border-0 bg-transparent item-unit-dropdown" name="items[INDEX][measurement_unit_id]" required style="box-shadow: none; cursor: pointer;">
                            <option value="">-</option>
                        </select>
                    </span>
                    <input type="number" name="items[INDEX][quantity]" class="form-control quantity" step="0.01" min="0.01" value="1" required>
                </div>
            </td>
            <td>
                <input type="number" name="items[INDEX][unit_price]" class="form-control unit-price" step="0.01" min="0" value="0" required>
            </td>
            <td>
                <input type="number" name="items[INDEX][tax_rate]" class="form-control tax-rate" step="0.01" min="0" value="15">
            </td>
            <td class="text-end">
                <span class="row-total">0.00</span>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-row">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>
    </template>
@endsection

@push('styles')
    <style>
        .select2-container .select2-selection--single {
            height: 38px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            let rowIndex = 0;

            function addItemRow() {
                const template = document.getElementById('item-row-template').innerHTML;
                const html = template.replace(/INDEX/g, rowIndex);
                $('#items-table tbody').append(html);

                const $row = $('#items-table tbody tr').last();
                
                // Initialize Select2 for product search
                $row.find('.product-select').select2({
                    ajax: {
                        url: "{{ route('ajax.products.search') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return { q: params.term };
                        },
                        processResults: function (data) {
                            return {
                                        results: data.map(function (item) {
                                    return {
                                        id: item.id,
                                        text: item.code + ' - ' + item.name_en,
                                        price: item.cost_price || item.sale_price,
                                        units: item.units || []
                                    };
                                })
                            };
                        }
                    }
                }).on('select2:select', function(e) {
                    const data = e.params.data;
                    const $row = $(this).closest('tr');
                    $row.find('.unit-price').val(data.price);
                    
                    const $unitDropdown = $row.find('.item-unit-dropdown');
                    $unitDropdown.empty();
                    if (data.units && data.units.length > 0) {
                        data.units.forEach(u => {
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
                        $row.find('.unit-price').val(price);
                        calculateTotals();
                    }
                });

                rowIndex++;
                calculateTotals();
            }

            // Add first row by default
            addItemRow();

            $('#add-item').click(function() {
                addItemRow();
            });

            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                calculateTotals();
            });

            $(document).on('input', '.quantity, .unit-price, .tax-rate, #discount_amount, #shipping_amount', function() {
                calculateTotals();
            });

            function calculateTotals() {
                let subtotal = 0;
                let totalTax = 0;

                $('.item-row').each(function() {
                    const qty = parseFloat($(this).find('.quantity').val()) || 0;
                    const price = parseFloat($(this).find('.unit-price').val()) || 0;
                    const taxRate = parseFloat($(this).find('.tax-rate').val()) || 0;

                    const rowSubtotal = qty * price;
                    const rowTax = rowSubtotal * (taxRate / 100);
                    const rowTotal = rowSubtotal + rowTax;

                    subtotal += rowSubtotal;
                    totalTax += rowTax;

                    $(this).find('.row-total').text(rowTotal.toFixed(2));
                });

                const discount = parseFloat($('#discount_amount').val()) || 0;
                const shipping = parseFloat($('#shipping_amount').val()) || 0;
                const total = subtotal + totalTax + shipping - discount;

                $('#summary-subtotal').text(subtotal.toFixed(2));
                $('#summary-tax').text(totalTax.toFixed(2));
                $('#summary-total').text(total.toFixed(2));
                $('#total_amount_input').val(total.toFixed(2));
            }

            // Branch to Warehouse AJAX
            $('#branch_id').change(function() {
                const branchId = $(this).val();
                const $warehouseSelect = $('#warehouse_id');
                
                $warehouseSelect.empty().append('<option value="">{{ __("messages.select_warehouse") }}</option>');
                
                if (branchId) {
                    $.get("{{ route('ajax.warehouses.by-branch') }}", { branch_id: branchId }, function(data) {
                        data.forEach(function(warehouse) {
                            $warehouseSelect.append(`<option value="${warehouse.id}">${warehouse.name_en}</option>`);
                        });
                    });
                }
            });

            // Trigger branch change to populate warehouses if old value exists
            if ($('#branch_id').val()) {
                $('#branch_id').trigger('change');
            }

            // ===== OCR FUNCTIONALITY =====
            let uploadedFile = null;

            // Drag and drop functionality
            const dropZone = document.getElementById('drop-zone');
            const fileInput = document.getElementById('invoice-file-input');
            const uploadPrompt = document.getElementById('upload-prompt');
            const previewContainer = document.getElementById('preview-container');
            const invoicePreview = document.getElementById('invoice-preview');
            const extractBtn = document.getElementById('extract-btn');
            const ocrStatus = document.getElementById('ocr-status');
            const ocrResult = document.getElementById('ocr-result');

            // Click to browse
            dropZone.addEventListener('click', (e) => {
                // Don't trigger if clicking remove button
                if (!e.target.closest('#clear-upload')) {
                    fileInput.click();
                }
            });

            // Handle file selection
            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    handleFileUpload(e.target.files[0]);
                }
            });

            // Prevent default drag behaviors
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            // Highlight drop zone when dragging
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => {
                    dropZone.classList.add('border-primary', 'bg-light');
                });
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => {
                    dropZone.classList.remove('border-primary', 'bg-light');
                });
            });

            // Handle dropped files
            dropZone.addEventListener('drop', (e) => {
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    handleFileUpload(files[0]);
                }
            });

            // Clear upload
            document.getElementById('clear-upload').addEventListener('click', () => {
                uploadedFile = null;
                fileInput.value = '';
                uploadPrompt.style.display = 'block';
                previewContainer.style.display = 'none';
                extractBtn.disabled = true;
                ocrResult.style.display = 'none';
            });

            // Handle file upload
            function handleFileUpload(file) {
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
                const maxSize = 10 * 1024 * 1024; // 10MB

                if (!allowedTypes.includes(file.type)) {
                    alert('Invalid file type. Please upload JPG, PNG, or PDF files.');
                    return;
                }

                if (file.size > maxSize) {
                    alert('File size exceeds 10MB limit.');
                    return;
                }

                uploadedFile = file;

                // Preview image (not for PDF)
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        invoicePreview.src = e.target.result;
                        uploadPrompt.style.display = 'none';
                        previewContainer.style.display = 'block';
                        extractBtn.disabled = false;
                    };
                    reader.readAsDataURL(file);
                } else {
                    // PDF - just show filename
                    uploadPrompt.innerHTML = `<i class="fas fa-file-pdf fa-3x text-danger mb-3"></i><p>${file.name}</p>`;
                    extractBtn.disabled = false;
                }
            }

            // Extract data button
            extractBtn.addEventListener('click', () => {
                if (!uploadedFile) return;

                const formData = new FormData();
                formData.append('invoice_image', uploadedFile);

                // Show loading
                ocrStatus.style.display = 'block';
                ocrResult.style.display = 'none';
                extractBtn.disabled = true;

                // Send to OCR endpoint
                fetch('{{ route("purchases.invoices.ocr.extract") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(result => {
                    console.log('OCR Result:', result); // Debug log
                    ocrStatus.style.display = 'none';
                    extractBtn.disabled = false;

                    if (result.success) {
                        console.log('Extracted data:', result.data); // Debug log
                        // Populate form with extracted data
                        populateForm(result.data, result.temp_file);
                    } else {
                        showOcrResult(false, result.error || 'Failed to extract data from invoice.');
                    }
                })
                .catch(error => {
                    console.error('OCR Error:', error);
                    ocrStatus.style.display = 'none';
                    extractBtn.disabled = false;
                    showOcrResult(false, 'An error occurred during extraction.');
                });
            });

            // Populate form with extracted data
            function populateForm(data, tempFile) {
                console.log('Populating form with data:', data);
                
                if (tempFile) {
                    document.getElementById('ocr_temp_file').value = tempFile;
                }
                
                let messages = [];
                
                // Invoice number
                if (data.invoice_number) {
                    const field = document.getElementById('invoice_number');
                    if (field) { field.value = data.invoice_number; }
                }

                // Invoice date
                if (data.invoice_date) {
                    const field = document.getElementById('invoice_date');
                    if (field) { field.value = data.invoice_date; }
                }

                // Due date
                if (data.due_date) {
                    const field = document.getElementById('due_date');
                    if (field) { field.value = data.due_date; }
                }

                // Vendor
                if (data.vendor) {
                    const $vendorSelect = $('#vendor_id');
                    if ($vendorSelect.length) {
                        if (data.vendor.matched && data.vendor.id) {
                            $vendorSelect.val(data.vendor.id).trigger('change');
                            messages.push(`✓ Vendor matched: ${data.vendor.name}`);
                        } else if (data.vendor.name) {
                            // Add as temporary option if not exists
                            if (!$vendorSelect.find(`option[value="new:${data.vendor.name}"]`).length) {
                                const newOption = new Option(`[NEW] ${data.vendor.name}`, `new:${data.vendor.name}`, true, true);
                                $vendorSelect.append(newOption).trigger('change');
                            }
                            
                            // Store extra vendor details for creation
                            if (data.vendor.tax_id) { document.getElementById('vendor_tax_id').value = data.vendor.tax_id; }
                            if (data.vendor.address) { document.getElementById('vendor_address').value = data.vendor.address; }

                            messages.push(`→ New vendor suggested: "${data.vendor.name}"`);
                        }
                    }
                }

                // Line items
                if (data.items && data.items.length > 0) {
                    $('#items-table tbody').empty();
                    rowIndex = 0;
                    
                    let matchedCount = 0;
                    let newCount = 0;
                    
                    data.items.forEach((item) => {
                        addItemRow();
                        const $row = $('#items-table tbody tr').last();
                        const $select = $row.find('.product-select');
                        
                        if (item.matched && item.product_id) {
                            // Matched product
                            const option = new Option(item.product_name, item.product_id, true, true);
                            $select.append(option).trigger('change');
                            matchedCount++;
                        } else if (item.product_name) {
                            // Suggested new product
                            const option = new Option(`[NEW] ${item.product_name}`, `new:${item.product_name}`, true, true);
                            $select.append(option).trigger('change');
                            newCount++;
                        }
                        
                        $row.find('.quantity').val(item.quantity || 1);
                        $row.find('.unit-price').val(item.unit_price || 0);
                        $row.find('.tax-rate').val(15);
                    });
                    
                    messages.push(`${data.items.length} items added (${matchedCount} matched, ${newCount} new)`);
                    calculateTotals();
                }

                // Set summary fields if they exist (though calculateTotals handles most)
                const discountField = document.getElementById('discount_amount');
                if (discountField && data.discount) { discountField.value = data.discount; }
                
                const shippingField = document.getElementById('shipping_amount');
                if (shippingField && data.shipping) { shippingField.value = data.shipping; }

                calculateTotals();

                // Show result
                if (messages.length > 0) {
                    const messageText = '<b>Extraction Complete:</b><br>' + messages.join('<br>');
                    showOcrResult(true, messageText);
                }
            }

            // Show OCR result message
            function showOcrResult(success, message) {
                const alertClass = success ? 'alert-success' : 'alert-danger';
                const icon = success ? 'fa-check-circle' : 'fa-exclamation-triangle';
                
                ocrResult.innerHTML = `
                    <div class="alert ${alertClass}" role="alert">
                        <i class="fas ${icon}"></i> ${message}
                    </div>
                `;
                ocrResult.style.display = 'block';

                // Hide after 5 seconds if successful
                if (success) {
                    setTimeout(() => {
                        ocrResult.style.display = 'none';
                    }, 5000);
                }
            }
        });
    </script>
@endpush

@extends('layouts.app')

@section('title', $employee->name)

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ $employee->name }}</h1>
            <div>
                <a href="{{ route('hr.employees.salary-slip', $employee) }}" class="btn btn-outline-danger me-2"
                <a href="{{ route('hr.employees.salary-slip', $employee) }}" class="btn btn-outline-danger me-2"
                    target="_blank">
                    <i class="fas fa-file-pdf me-2"></i> {{ __('messages.salary_slip') }}
                </a>
                <button type="button" class="btn btn-outline-info me-2" data-bs-toggle="modal" data-bs-target="#visitingCardModal">
                    <i class="fas fa-id-card me-2"></i> Create Visiting Card
                </button>
                <a href="{{ route('hr.employees.edit', $employee) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i> {{ __('messages.edit') }}
                </a>
                <a href="{{ route('hr.employees.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> {{ __('messages.back') }}
                </a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card glassy h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-user-circle fa-5x text-muted"></i>
                        </div>
                        <h4 class="mb-0">{{ $employee->name }}</h4>
                        @if($employee->first_name_ar || $employee->last_name_ar)
                            <p class="text-muted mb-0">{{ $employee->first_name_ar }} {{ $employee->last_name_ar }}</p>
                        @endif
                        <p class="text-muted">{{ $employee->designation->name ?? '-' }}</p>
                        <hr>
                        <div class="text-start">
                            <p><strong>{{ __('messages.employee_code') }}:</strong>
                                <code>{{ $employee->employee_code }}</code>
                            </p>
                            <p><strong>{{ __('messages.status') }}:</strong> <span
                                    class="badge bg-success">{{ __('messages.active') }}</span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card glassy h-100">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('messages.basic_information') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 200px;">{{ __('messages.first_name_ar') }}</th>
                                <td>{{ $employee->first_name_ar ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.last_name_ar') }}</th>
                                <td>{{ $employee->last_name_ar ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.email') }}</th>
                                <td>{{ $employee->email }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.phone') }}</th>
                                <td>{{ $employee->phone ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.nationality') }}</th>
                                <td>{{ $employee->nationality ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.department') }}</th>
                                <td>{{ $employee->department->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.designation') }}</th>
                                <td>{{ $employee->designation->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.joining_date') }}</th>
                                <td>{{ $employee->joining_date->format('Y-m-d') }}</td>
                            </tr>
                        </table>

                        <h5 class="mt-4 mb-3">{{ __('messages.salary_information') }}</h5>
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 200px;">{{ __('messages.basic_salary') }}</th>
                                <td>{{ number_format($employee->basic_salary, 2) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.house_rent_allowance') }}</th>
                                <td>{{ number_format($employee->house_rent_allowance, 2) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.conveyance_allowance') }}</th>
                                <td>{{ number_format($employee->conveyance_allowance, 2) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.dearness_allowance') }}</th>
                                <td>{{ number_format($employee->dearness_allowance, 2) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.overtime_allowance') }}</th>
                                <td>{{ number_format($employee->overtime_allowance, 2) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.other_allowance') }}</th>
                                <td>{{ number_format($employee->other_allowance, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Visiting Card Modal -->
    <div class="modal fade" id="visitingCardModal" tabindex="-1" aria-labelledby="visitingCardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="visitingCardModalLabel">Generate Employee Visiting Card</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('hr.employees.visiting-card', $employee) }}" method="POST" target="_blank" data-turbo="false">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row">
                            <!-- Left Column: Form -->
                            <div class="col-lg-6 border-end pe-lg-4">
                                <h6 class="mb-4 text-primary fw-bold"><i class="fas fa-edit me-2"></i>Card Details</h6>
                                <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Select Card Template</label>
                                <select name="template" class="form-select" required>
                                    <option value="template-1">Template 1 - Modern Minimalist</option>
                                    <option value="template-2">Template 2 - Elegant Dark</option>
                                    <option value="template-3">Template 3 - Corporate Split</option>
                                    <option value="template-4">Template 4 - Creative Accent</option>
                                    <option value="template-5">Template 5 - Classic Centered</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Name (English)</label>
                                <input type="text" name="name_en" class="form-control" value="{{ $employee->first_name_en }} {{ $employee->last_name_en }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Name (Arabic - Optional)</label>
                                <input type="text" name="name_ar" class="form-control text-end d-block" value="{{ $employee->first_name_ar }} {{ $employee->last_name_ar }}" dir="rtl">
                            </div>
                            
                            <div class="col-md-12">
                                <label class="form-label">Professional Designation <small class="text-muted">(Auto-filled from Profile)</small></label>
                                <input type="text" class="form-control" value="{{ $employee->designation->name ?? '' }}" readonly disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Direct Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="{{ $employee->phone }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mobile Number (Optional)</label>
                                <input type="text" name="mobile" class="form-control" value="">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" value="{{ $employee->email }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Branch Name (English)</label>
                                <input type="text" name="company_name_en" class="form-control" value="{{ $employee->user?->branch?->name_en ?? $employee->company?->name_en ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Branch Name (Arabic)</label>
                                <input type="text" name="company_name_ar" class="form-control text-end d-block" value="{{ $employee->user?->branch?->name_ar ?? $employee->company?->name_ar ?? '' }}" dir="rtl">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Corporate Website</label>
                                <input type="text" name="website" class="form-control" value="{{ $employee->company?->website ?? '' }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Office Address (English - Optional)</label>
                                <textarea name="address_en" class="form-control" rows="2">{{ $employee->user?->branch?->address ?? '' }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Office Address (Arabic - Optional)</label>
                                <textarea name="address_ar" class="form-control text-end d-block" rows="2" dir="rtl"></textarea>
                            </div>

                            <!-- Barcode Coordinates (Hidden) -->
                            <input type="hidden" name="barcode_x" id="barcode_x" value="2.5">
                            <input type="hidden" name="barcode_y" id="barcode_y" value="0.6">
                            <input type="hidden" name="barcode_width" id="barcode_width" value="0.8">
                                </div>
                            </div>

                            <!-- Right Column: Live Preview -->
                            <div class="col-lg-6 ps-lg-4 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 class="mb-0 text-primary fw-bold"><i class="fas fa-eye me-2"></i>Live Preview</h6>
                                    <span class="badge bg-secondary" id="previewStatus">Syncing...</span>
                                </div>
                                
                                <div class="preview-container flex-grow-1 d-flex justify-content-center align-items-center" style="background: #e8ecf0; border: 2px dashed #adb5bd; border-radius: 10px; min-height: 320px; overflow: hidden; position: relative;">
                                    <div style="transform: scale(1.6); transform-origin: center center; box-shadow: 0 12px 35px rgba(0,0,0,0.2);">
                                        <iframe id="visitingCardPreview" style="width: 3.5in; height: 2in; border: none; background: white; display: block;" src="about:blank"></iframe>
                                    </div>
                                </div>
                                <div class="text-center mt-3 text-muted small">
                                    <i class="fas fa-arrows-alt me-1"></i> <strong>Tip:</strong> Drag and resize the QR code directly in the preview!
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-print me-1"></i> Generate PDF Card
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    @push('scripts')
    <!-- Interact.js for drag and resize -->
    <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
    <script>
        (function() {
            var modal = document.getElementById('visitingCardModal');
            if (!modal) return;

            var form = modal.querySelector('form');
            var previewIframe = document.getElementById('visitingCardPreview');
            var statusBadge = document.getElementById('previewStatus');
            var previewUrl = "{{ route('hr.employees.visiting-card.preview', $employee) }}";
            var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            var previewTimeout = null;

            var barcodeX = document.getElementById('barcode_x');
            var barcodeY = document.getElementById('barcode_y');
            var barcodeWidth = document.getElementById('barcode_width');

            function setStatus(text, colorClass) {
                statusBadge.textContent = text;
                statusBadge.className = 'badge ' + colorClass;
            }

            function updateLivePreview() {
                setStatus('Syncing...', 'bg-warning text-dark');
                clearTimeout(previewTimeout);
                previewTimeout = setTimeout(function() {
                    var formData = new FormData(form);

                    fetch(previewUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Turbo-Frame': ''
                        }
                    })
                    .then(function(response) {
                        return response.text().then(function(html) {
                            return { ok: response.ok, html: html, status: response.status };
                        });
                    })
                    .then(function(result) {
                        if (!result.ok) {
                            setStatus('Error ' + result.status, 'bg-danger');
                            return;
                        }
                        var iframeDoc = previewIframe.contentDocument || previewIframe.contentWindow.document;
                        iframeDoc.open();
                        iframeDoc.write(result.html);
                        iframeDoc.close();
                        
                        // Setup interaction after content is written
                        setTimeout(setupInteraction, 150);
                        setStatus('Live', 'bg-success');
                    })
                    .catch(function(err) {
                        setStatus('Error', 'bg-danger');
                    });
                }, 600);
            }

            function setupInteraction() {
                var iframeDoc = previewIframe.contentDocument || previewIframe.contentWindow.document;
                var qrElement = iframeDoc.querySelector('.qr-code-interactive');
                if (!qrElement) return;

                // Load interact.js into iframe if not present
                if (!previewIframe.contentWindow.interact) {
                    var script = iframeDoc.createElement('script');
                    script.src = "https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js";
                    iframeDoc.head.appendChild(script);
                    script.onload = function() { initInteract(qrElement); };
                } else {
                    initInteract(qrElement);
                }
            }

            function initInteract(el) {
                var win = previewIframe.contentWindow;
                if (!win.interact) return;

                // Reset state
                win.interact(el).unset();

                win.interact(el)
                .draggable({
                    inertia: true,
                    modifiers: [
                        win.interact.modifiers.restrictRect({
                            restriction: 'parent',
                            endOnly: true
                        })
                    ],
                    listeners: {
                        move: function (event) {
                            var target = event.target;
                            var x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
                            var y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

                            target.style.transform = 'translate(' + x + 'px, ' + y + 'px)';
                            target.setAttribute('data-x', x);
                            target.setAttribute('data-y', y);
                            
                            updateCoordinates(target);
                        }
                    }
                })
                .resizable({
                    edges: { left: true, right: true, bottom: true, top: true },
                    modifiers: [
                        win.interact.modifiers.restrictEdges({
                            outer: 'parent'
                        }),
                        win.interact.modifiers.restrictSize({
                            min: { width: 30, height: 30 }
                        }),
                        win.interact.modifiers.aspectRatio({
                            ratio: 'equal',
                        })
                    ],
                    listeners: {
                        move: function (event) {
                            var target = event.target;
                            var x = (parseFloat(target.getAttribute('data-x')) || 0);
                            var y = (parseFloat(target.getAttribute('data-y')) || 0);

                            target.style.width = event.rect.width + 'px';
                            target.style.height = event.rect.height + 'px';

                            x += event.deltaRect.left;
                            y += event.deltaRect.top;

                            target.style.transform = 'translate(' + x + 'px,' + y + 'px)';
                            target.setAttribute('data-x', x);
                            target.setAttribute('data-y', y);
                            
                            updateCoordinates(target);
                        }
                    }
                });
                
                // Add visual handle for resizing
                if (!el.querySelector('.resize-handle')) {
                    var handle = iframeDoc.createElement('div');
                    handle.className = 'resize-handle';
                    handle.style.cssText = 'position:absolute;right:0;bottom:0;width:10px;height:10px;background:#0d6efd;cursor:nwse-resize;';
                    el.appendChild(handle);
                }
            }

            function updateCoordinates(el) {
                // Convert pixels to inches for the backend (DPI = 72)
                var rect = el.getBoundingClientRect();
                var parentRect = el.offsetParent.getBoundingClientRect();
                
                var topIn = (rect.top - parentRect.top) / 72;
                var leftIn = (rect.left - parentRect.left) / 72;
                var widthIn = rect.width / 72;

                barcodeX.value = leftIn.toFixed(2);
                barcodeY.value = topIn.toFixed(2);
                barcodeWidth.value = widthIn.toFixed(2);
            }

            // Standard inputs
            form.querySelectorAll('input:not([type="hidden"]), textarea').forEach(function(el) {
                el.addEventListener('input', updateLivePreview);
            });

            form.querySelectorAll('select').forEach(function(el) {
                el.addEventListener('change', updateLivePreview);
            });

            // Auto-trigger when modal opens
            modal.addEventListener('shown.bs.modal', function() {
                updateLivePreview();
            });
        })();
    </script>
    @endpush
@endsection
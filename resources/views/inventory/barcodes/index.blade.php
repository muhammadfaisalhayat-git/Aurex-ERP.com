@extends('layouts.app')

@section('title', __('messages.barcode_generator'))

@section('content')
    <div class="container-fluid">
        <div class="page-header mb-4">
            <h1 class="page-title">{{ __('messages.barcode_generator') }}</h1>
        </div>

        <div class="row g-4">
            {{-- Selection Section --}}
            <div class="col-lg-4">
                <div class="card glassy-form h-100">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fas fa-search me-2"></i>{{ __('messages.select_products') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4 position-relative">
                            <label class="form-label fw-bold">{{ __('messages.search_product') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-barcode text-primary"></i></span>
                                <input type="text" id="product_search" class="form-control"
                                    placeholder="{{ __('messages.search_by_code_or_name') }}" autocomplete="off">
                            </div>
                            <div id="search_results" class="search-results-container glassy"
                                style="display: none; position: absolute; width: 100%; top: 100%; left: 0;"></div>
                        </div>

                        <div id="selected_product_info" class="p-3 rounded bg-light border border-dashed mb-3"
                            style="display: none;">
                            <h6 class="fw-bold mb-1 text-primary" id="info_name"></h6>
                            <p class="small text-muted mb-2" id="info_code"></p>
                            <div class="row g-2 align-items-end">
                                <div class="col-8">
                                    <label class="form-label small mb-1">{{ __('messages.quantity') }}</label>
                                    <input type="number" id="label_qty" class="form-control form-control-sm" value="1"
                                        min="1">
                                </div>
                                <div class="col-4">
                                    <button type="button" id="add_to_batch" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info py-2 small">
                            <i class="fas fa-info-circle me-1"></i> {{ __('messages.barcode_tip') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Batch List Section --}}
            <div class="col-lg-8">
                <form action="{{ route('inventory.barcodes.print') }}" method="GET" target="_blank">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title"><i class="fas fa-list-ul me-2"></i>{{ __('messages.print_batch') }}</h5>
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-print me-1"></i> {{ __('messages.print_labels') }}
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" id="batch_table">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th>{{ __('messages.product') }}</th>
                                            <th>{{ __('messages.code') }}</th>
                                            <th style="width: 150px;">{{ __('messages.quantity') }}</th>
                                            <th style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="empty_row">
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                {{ __('messages.no_products_added_to_batch') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .border-dashed {
                border-style: dashed !important;
            }

            #batch_table td {
                vertical-align: middle;
            }

            .search-results-container.glassy {
                min-width: 100%;
                max-height: 300px;
                overflow-y: auto;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const productSearch = document.getElementById('product_search');
                const resultsContainer = document.getElementById('search_results');
                const infoBox = document.getElementById('selected_product_info');
                const batchTable = document.getElementById('batch_table').querySelector('tbody');
                const emptyRow = document.getElementById('empty_row');

                let selectedProduct = null;
                let batchIndex = 0;

                // Search logic
                let timeout = null;
                productSearch.addEventListener('input', function () {
                    clearTimeout(timeout);
                    const query = this.value.trim();

                    if (query.length < 2) {
                        resultsContainer.style.display = 'none';
                        return;
                    }

                    timeout = setTimeout(() => {
                        fetch(`{{ route('inventory.barcodes.search') }}?q=${encodeURIComponent(query)}`)
                            .then(res => res.json())
                            .then(data => {
                                resultsContainer.innerHTML = '';
                                if (data.length === 0) {
                                    resultsContainer.innerHTML = '<div class="p-3 text-muted small text-center">{{ __("messages.no_results") }}</div>';
                                } else {
                                    data.forEach(product => {
                                        const div = document.createElement('div');
                                        div.className = 'search-result-item';
                                        div.innerHTML = `
                                            <div class="item-title">${product.name}</div>
                                            <div class="item-subtitle">${product.product_code}</div>
                                        `;
                                        div.onclick = () => selectProduct(product);
                                        resultsContainer.appendChild(div);
                                    });
                                }
                                resultsContainer.style.display = 'block';
                            });
                    }, 300);
                });

                function selectProduct(product) {
                    selectedProduct = product;
                    document.getElementById('info_name').innerText = product.name;
                    document.getElementById('info_code').innerText = product.product_code;
                    infoBox.style.display = 'block';
                    resultsContainer.style.display = 'none';
                    productSearch.value = '';
                    document.getElementById('label_qty').focus();
                }

                // Add to batch
                document.getElementById('add_to_batch').onclick = function () {
                    if (!selectedProduct) return;

                    const qty = parseInt(document.getElementById('label_qty').value) || 1;

                    // Check if product already exists in batch
                    const existingRow = Array.from(batchTable.querySelectorAll('tr')).find(tr => tr.dataset.productId == selectedProduct.id);

                    if (existingRow) {
                        const qtyInput = existingRow.querySelector('.qty-input');
                        qtyInput.value = parseInt(qtyInput.value) + qty;
                    } else {
                        emptyRow.style.display = 'none';

                        const tr = document.createElement('tr');
                        tr.dataset.productId = selectedProduct.id;
                        tr.innerHTML = `
                            <td class="text-muted fw-bold">${++batchIndex}</td>
                            <td>
                                <span class="fw-bold">${selectedProduct.name}</span>
                                <input type="hidden" name="items[${batchIndex}][product_id]" value="${selectedProduct.id}">
                            </td>
                            <td><code>${selectedProduct.product_code}</code></td>
                            <td>
                                <input type="number" name="items[${batchIndex}][quantity]" class="form-control form-control-sm qty-input" value="${qty}" min="1">
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm text-danger remove-item">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        `;

                        tr.querySelector('.remove-item').onclick = function () {
                            tr.remove();
                            if (batchTable.querySelectorAll('tr').length <= 1) {
                                emptyRow.style.display = 'table-row';
                            }
                        };

                        batchTable.appendChild(tr);
                    }

                    // Reset
                    infoBox.style.display = 'none';
                    selectedProduct = null;
                    productSearch.focus();
                };

                // Close search results when clicking outside
                document.addEventListener('click', function (e) {
                    if (!productSearch.contains(e.target) && !resultsContainer.contains(e.target)) {
                        resultsContainer.style.display = 'none';
                    }
                });
            });
        </script>
    @endpush
@endsection
@extends('layouts.app')

@section('title', __('messages.create_customer_request') ?? 'Create Customer Request')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.create_customer_request') ?? 'Create Customer Request' }}</h1>
            <a href="{{ route('sales.customer-requests.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
            </a>
        </div>

        <form action="{{ route('sales.customer-requests.store') }}" method="POST" id="customerRequestForm">
            @csrf
            <div class="card mb-4 glassy">
                <div class="card-body">
                    <div class="row">
                        <!-- Header Information -->
                        <div class="col-md-4 mb-3">
                            <label for="document_number" class="form-label fw-bold">{{ __('messages.document_number') }}
                                <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-white @error('document_number') is-invalid @enderror"
                                id="document_number" name="document_number"
                                value="{{ old('document_number', $document_number) }}" required readonly>
                            @error('document_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="request_date" class="form-label fw-bold">{{ __('messages.date') }} <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control bg-white @error('request_date') is-invalid @enderror"
                                id="request_date" name="request_date" value="{{ old('request_date', date('Y-m-d')) }}"
                                required>
                            @error('request_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="needed_date"
                                class="form-label fw-bold">{{ __('messages.needed_date') ?? 'Needed By' }}</label>
                            <input type="date" class="form-control bg-white @error('needed_date') is-invalid @enderror"
                                id="needed_date" name="needed_date" value="{{ old('needed_date') }}">
                            @error('needed_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="customer_search" class="form-label fw-bold">{{ __('messages.customer') }} <span
                                    class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="text" id="customer_search"
                                    class="form-control bg-white @error('customer_id') is-invalid @enderror"
                                    placeholder="{{ __('messages.select_customer') }}" autocomplete="off">
                                <input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id') }}">
                                <div id="customer-results" class="search-results-container glassy"
                                    style="display: none; position: absolute; z-index: 1050; width: 100%;"></div>
                            </div>
                            @error('customer_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="branch_id" class="form-label fw-bold">{{ __('messages.branch') }} <span
                                    class="text-danger">*</span></label>
                            <select class="form-select bg-white @error('branch_id') is-invalid @enderror" id="branch_id"
                                name="branch_id" required>
                                <option value="">{{ __('messages.select_branch') }}</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name_en }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label fw-bold">{{ __('messages.status') }} <span
                                    class="text-danger">*</span></label>
                            <select class="form-select bg-white @error('status') is-invalid @enderror" id="status"
                                name="status" required>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>
                                    {{ __('messages.pending') ?? 'Pending' }}</option>
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>
                                    {{ __('messages.draft') }}</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>
                                    {{ __('messages.cancelled') ?? 'Cancelled' }}</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="notes" class="form-label fw-bold">{{ __('messages.notes') }}</label>
                            <textarea class="form-control bg-white" id="notes" name="notes"
                                rows="2">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="card mb-4 glassy">
                <div
                    class="card-header d-flex justify-content-between align-items-center bg-transparent border-bottom-0 pt-3">
                    <h5 class="mb-0 fw-bold">{{ __('messages.items') }}</h5>
                    <button type="button" class="btn btn-sm btn-success" id="addItemBtn">
                        <i class="fas fa-plus"></i> {{ __('messages.add_item') }}
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="itemsTable">
                            <thead>
                                <tr class="bg-light">
                                    <th style="width: 40%">{{ __('messages.product') }}</th>
                                    <th style="width: 15%">{{ __('messages.quantity') }}</th>
                                    <th style="width: 40%">{{ __('messages.notes') }}</th>
                                    <th style="width: 5%"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <!-- Items will be added here via JS -->
                            </tbody>
                        </table>
                    </div>
                    @error('items')
                        <div class="alert alert-danger m-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end mb-4">
                <button type="submit" class="btn btn-primary px-4 py-2">
                    <i class="fas fa-save me-1"></i> {{ __('messages.save') }}
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const addItemBtn = document.getElementById('addItemBtn');
                const itemsBody = document.getElementById('itemsBody');
                let itemIndex = 0;

                const products = @json($products);
                const customers = @json($customers->map(function ($c) {
                return ['id' => $c->id, 'name' => $c->name_en, 'code' => $c->code]; }));

                // Customer Search Logic
                const customerSearch = document.getElementById('customer_search');
                const customerId = document.getElementById('customer_id');
                const customerResults = document.getElementById('customer-results');
                let currentCustomerIndex = -1;

                customerSearch.addEventListener('input', function () {
                    customerId.value = '';
                    performCustomerSearch(this.value);
                });

                customerSearch.addEventListener('focus', function () {
                    performCustomerSearch(this.value);
                });

                function performCustomerSearch(query) {
                    const results = customers.filter(c =>
                        c.name.toLowerCase().includes(query.toLowerCase()) ||
                        (c.code && c.code.toLowerCase().includes(query.toLowerCase()))
                    ).slice(0, 10);

                    if (results.length > 0) {
                        customerResults.innerHTML = results.map(c => `
                        <div class="search-result-item p-2 border-bottom" data-id="${c.id}" style="cursor: pointer;">
                            <div class="fw-bold">${c.name}</div>
                            <small class="text-muted">${c.code || ''}</small>
                        </div>
                    `).join('');
                        customerResults.style.display = 'block';
                    } else {
                        customerResults.innerHTML = '<div class="p-2 text-muted">No customer found</div>';
                        customerResults.style.display = 'block';
                    }
                }

                customerResults.addEventListener('click', function (e) {
                    const item = e.target.closest('.search-result-item');
                    if (item) {
                        customerSearch.value = item.querySelector('.fw-bold').textContent;
                        customerId.value = item.dataset.id;
                        customerResults.style.display = 'none';
                    }
                });

                document.addEventListener('click', function (e) {
                    if (!customerSearch.contains(e.target) && !customerResults.contains(e.target)) {
                        customerResults.style.display = 'none';
                    }
                });

                // Items Logic
                function addItem(data = null) {
                    const index = itemIndex++;
                    const tr = document.createElement('tr');

                    let productOptions = '<option value="">{{ __("messages.select_product") }}</option>';
                    products.forEach(p => {
                        const selected = data && data.product_id == p.id ? 'selected' : '';
                        productOptions += `<option value="${p.id}" ${selected}>${p.name_en}</option>`;
                    });

                    tr.innerHTML = `
                    <td>
                        <select class="form-select form-select-sm bg-white product-select" name="items[${index}][product_id]" required>
                            ${productOptions}
                        </select>
                    </td>
                    <td>
                        <input type="number" step="0.001" class="form-control form-control-sm bg-white" name="items[${index}][quantity]" value="${data ? data.quantity : 1}" required min="0.001">
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm bg-white" name="items[${index}][notes]" value="${data ? data.notes || '' : ''}">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-link text-danger remove-item p-0">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;

                    itemsBody.appendChild(tr);

                    tr.querySelector('.remove-item').addEventListener('click', function () {
                        tr.remove();
                        if (itemsBody.children.length === 0) addItem();
                    });
                }

                addItemBtn.addEventListener('click', () => addItem());

                // Add initial row if empty
                @if(old('items'))
                    @foreach(old('items') as $item)
                        addItem(@json($item));
                    @endforeach
                @else
                    addItem();
                @endif
        });
        </script>

        <style>
            .search-results-container {
                background: white;
                border: 1px solid #ddd;
                border-radius: 4px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                max-height: 250px;
                overflow-y: auto;
            }

            .search-result-item:hover {
                background-color: #f8f9fa;
            }

            .glassy {
                background: rgba(255, 255, 255, 0.8) !important;
                backdrop-filter: blur(10px);
            }
        </style>
    @endpush
@endsection
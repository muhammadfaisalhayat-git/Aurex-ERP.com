@extends('layouts.app')

@section('title', __('messages.customer_requests') ?? 'Customer Requests')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.customer_requests') ?? 'Customer Requests' }}</h1>
            @can('create customer requests')
                <a href="{{ route('sales.customer-requests.create') }}" class="btn btn-primary d-flex align-items-center">
                    <i class="fas fa-plus-circle me-2"></i> {{ __('messages.create') }}
                </a>
            @endcan
        </div>

        <div class="card mb-4 glassy">
            <div class="card-body">
                <form action="{{ route('sales.customer-requests.index') }}" method="GET" class="row g-4">
                    <div class="col-md-3">
                        <label for="document_number"
                            class="form-label fw-bold">{{ __('messages.document_number') ?? 'Document #' }}</label>
                        <input type="text" name="document_number" id="document_number" class="form-control bg-white"
                            value="{{ request('document_number') }}" placeholder="Search Document #">
                    </div>

                    <div class="col-md-3">
                        <label for="customer_search" class="form-label fw-bold">{{ __('messages.customer') }}</label>
                        <div class="position-relative">
                            <input type="text" id="customer_search" name="customer_name"
                                class="form-control bg-white shadow-none" placeholder="{{ __('messages.all_customers') }}"
                                value="{{ request('customer_id') ? ($customers->find(request('customer_id'))->name_en ?? '') : request('customer_name') }}"
                                autocomplete="off">
                            <input type="hidden" name="customer_id" id="customer_id" value="{{ request('customer_id') }}">
                            <div id="customer-results" class="search-results-container glassy" style="display: none;">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="status" class="form-label fw-bold">{{ __('messages.status') }}</label>
                        <select name="status" id="status" class="form-select bg-white">
                            <option value="">{{ __('messages.all_statuses') }}</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converted
                            </option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> {{ __('messages.search') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('messages.document_number') ?? 'Document #' }}</th>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.customer') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customerRequests as $request)
                                <tr>
                                    <td>{{ $request->document_number }}</td>
                                    <td>{{ $request->request_date->format('Y-m-d') }}</td>
                                    <td>{{ $request->customer->name ?? '-' }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $request->status == 'pending' ? 'warning' : ($request->status == 'converted' ? 'success' : 'secondary') }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('sales.customer-requests.show', $request) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('sales.customer-requests.edit', $request) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('sales.customer-requests.destroy', $request) }}"
                                                method="POST" style="display:inline"
                                                onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            <a href="{{ route('sales.customer-requests.pdf', $request) }}"
                                                class="btn btn-sm btn-success" title="Download PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                            @if($request->status == 'pending')
                                                <form action="{{ route('sales.customer-requests.convert', $request) }}"
                                                    method="POST" style="display:inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success"
                                                        title="Convert to Quotation">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">{{ __('messages.no_records_found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $customerRequests->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const customerSearch = document.getElementById('customer_search');
            const customerId = document.getElementById('customer_id');
            const customerResults = document.getElementById('customer-results');

            const customerData = @json($customers->map(function ($c) {
                return ['id' => $c->id, 'name' => $c->name_en, 'code' => $c->code];
            }));

            let currentIndex = -1;

            customerSearch.addEventListener('focus', function () {
                performSearch(this.value);
            });

            customerSearch.addEventListener('input', function () {
                customerId.value = '';
                performSearch(this.value);
            });

            function performSearch(query) {
                const results = customerData.filter(item =>
                    item.name.toLowerCase().includes(query.toLowerCase()) ||
                    (item.code && item.code.toLowerCase().includes(query.toLowerCase()))
                ).slice(0, 10);

                renderResults(results);
            }

            function renderResults(data) {
                if (data.length > 0) {
                    customerResults.innerHTML = data.map(item => `
                                        <div class="search-result-item" data-id="${item.id}">
                                            <div class="item-title">${item.name}</div>
                                            ${item.code ? `<div class="item-subtitle">${item.code}</div>` : ''}
                                        </div>
                                    `).join('');
                    customerResults.style.display = 'block';
                } else {
                    customerResults.innerHTML = '<div class="p-2 text-muted">No request found</div>';
                    customerResults.style.display = 'block';
                }
                currentIndex = -1;
            }

            customerResults.addEventListener('click', function (e) {
                const item = e.target.closest('.search-result-item');
                if (item) {
                    selectCustomer(item.dataset.id, item.querySelector('.item-title').textContent);
                }
            });

            function selectCustomer(id, name) {
                customerSearch.value = name;
                customerId.value = id;
                customerResults.style.display = 'none';
            }

            document.addEventListener('click', function (e) {
                if (!customerSearch.contains(e.target) && !customerResults.contains(e.target)) {
                    customerResults.style.display = 'none';
                }
            });

            customerSearch.addEventListener('keydown', function (e) {
                const items = customerResults.querySelectorAll('.search-result-item');

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    currentIndex = Math.min(currentIndex + 1, items.length - 1);
                    updateActiveItem(items);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    currentIndex = Math.max(currentIndex - 1, -1);
                    updateActiveItem(items);
                } else if (e.key === 'Enter') {
                    if (currentIndex > -1) {
                        e.preventDefault();
                        const selectedItem = items[currentIndex];
                        selectCustomer(selectedItem.dataset.id, selectedItem.querySelector('.item-title').textContent);
                    }
                }
            });

            function updateActiveItem(items) {
                items.forEach((item, index) => {
                    if (index === currentIndex) {
                        item.classList.add('active');
                        item.scrollIntoView({ block: 'nearest' });
                    } else {
                        item.classList.remove('active');
                    }
                });
            }
        });
    </script>
@endpush
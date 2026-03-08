@extends('layouts.app')

@section('title', __('messages.transport') . ' - ' . __('messages.add_order'))

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('messages.add_order') }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('transport.orders.index') }}">{{ __('messages.transport_orders') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('messages.add_new') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('transport.orders.store') }}" method="POST">
                @csrf

                <div class="row g-4">
                    <!-- Header Info -->
                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.document_number') }} <span
                                class="text-danger">*</span></label>
                        <input type="text" name="document_number"
                            class="form-control @error('document_number') is-invalid @enderror"
                            value="{{ old('document_number') }}" required>
                        @error('document_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.order_date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="order_date" class="form-control @error('order_date') is-invalid @enderror"
                            value="{{ old('order_date', date('Y-m-d')) }}" required>
                        @error('order_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.branch') }} <span class="text-danger">*</span></label>
                        <select name="branch_id" class="form-select @error('branch_id') is-invalid @enderror" required>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.scheduled_date') }} <span
                                class="text-danger">*</span></label>
                        <input type="date" name="scheduled_date"
                            class="form-control @error('scheduled_date') is-invalid @enderror"
                            value="{{ old('scheduled_date') }}" required>
                    </div>

                    <!-- Assignment -->
                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.driver') }} <span class="text-danger">*</span></label>
                        <select name="driver_id" class="form-select @error('driver_id') is-invalid @enderror" required>
                            <option value="">{{ __('messages.select_driver') }}</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id ? 'selected' : '' }}>
                                    {{ $driver->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.trailer') }}</label>
                        <select name="trailer_id" class="form-select">
                            <option value="">{{ __('messages.none') }}</option>
                            @foreach($trailers as $trailer)
                                <option value="{{ $trailer->id }}" {{ old('trailer_id') == $trailer->id ? 'selected' : '' }}>
                                    {{ $trailer->plate_number }} ({{ $trailer->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.vehicle') }}</label>
                        <select name="delivery_vehicle_id" class="form-select">
                            <option value="">{{ __('messages.none') }}</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ old('delivery_vehicle_id') == $vehicle->id ? 'selected' : '' }}>{{ $vehicle->plate_number }} ({{ $vehicle->brand }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Route -->
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.route_from') }} <span class="text-danger">*</span></label>
                        <input type="text" name="route_from" class="form-control @error('route_from') is-invalid @enderror"
                            value="{{ old('route_from') }}" required placeholder="{{ __('messages.origin_placeholder') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.route_to') }} <span class="text-danger">*</span></label>
                        <input type="text" name="route_to" class="form-control @error('route_to') is-invalid @enderror"
                            value="{{ old('route_to') }}" required
                            placeholder="{{ __('messages.destination_placeholder') }}">
                    </div>

                    <!-- Items -->
                    <div class="col-12 mt-5">
                        <h5 class="mb-3">{{ __('messages.order_items') }}</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle" id="items-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 40%">{{ __('messages.product') }}</th>
                                        <th style="width: 20%">{{ __('messages.quantity') }}</th>
                                        <th>{{ __('messages.notes') }}</th>
                                        <th style="width: 50px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <select name="items[0][product_id]" class="form-select select2" required>
                                                <option value="">{{ __('messages.select_product') }}</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}">{{ $product->name }} ({{ __('messages.stock') }}: {{ $product->available_stock }})</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" step="0.001" name="items[0][quantity]" class="form-control"
                                                required>
                                        </td>
                                        <td>
                                            <input type="text" name="items[0][notes]" class="form-control">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-row"><i
                                                    class="fas fa-times"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add-item">
                            <i class="fas fa-plus me-1"></i>{{ __('messages.add_item') }}
                        </button>
                    </div>

                    <div class="col-12">
                        <hr class="my-4">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('transport.orders.index') }}"
                                class="btn btn-outline-secondary px-4">{{ __('messages.cancel') }}</a>
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="fas fa-save me-2"></i>{{ __('messages.save_order') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let itemCount = 1;
                const itemsTable = document.querySelector('#items-table tbody');
                const addButton = document.querySelector('#add-item');

                addButton.addEventListener('click', function () {
                    const newRow = document.createElement('tr');
                    newRow.classList.add('item-row');
                    newRow.innerHTML = `
                        <td>
                            <select name="items[${itemCount}][product_id]" class="form-select select2" required>
                                <option value="">{{ __('messages.select_product') }}</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} ({{ __('messages.stock') }}: {{ $product->available_stock }})</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" step="0.001" name="items[${itemCount}][quantity]" class="form-control" required>
                        </td>
                        <td>
                            <input type="text" name="items[${itemCount}][notes]" class="form-control">
                        </td>
                        <td>
                            <button type="button" class="btn btn-outline-danger btn-sm remove-row"><i class="fas fa-times"></i></button>
                        </td>
                    `;
                    itemsTable.appendChild(newRow);
                    itemCount++;

                    // Re-initialize Select2 if needed
                    if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
                        $(newRow).find('.select2').select2({
                            theme: 'bootstrap-5'
                        });
                    }
                });

                itemsTable.addEventListener('click', function (e) {
                    if (e.target.closest('.remove-row')) {
                        const rows = itemsTable.querySelectorAll('tr');
                        if (rows.length > 1) {
                            e.target.closest('tr').remove();
                        } else {
                            alert('{{ __('messages.must_have_one_item') }}');
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection
@extends('layouts.app')

@section('title', __('messages.transport') . ' - ' . __('messages.edit_order'))

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('messages.edit_order') }}: {{ $order->document_number }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('transport.orders.index') }}">{{ __('messages.transport_orders') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('messages.edit') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('transport.orders.update', $order) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.document_number') }}</label>
                        <input type="text" class="form-control" value="{{ $order->document_number }}" readonly>
                        <input type="hidden" name="document_number" value="{{ $order->document_number }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.order_date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="order_date" class="form-control"
                            value="{{ old('order_date', $order->order_date->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.branch') }} <span class="text-danger">*</span></label>
                        <select name="branch_id" class="form-select" required>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id', $order->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.scheduled_date') }} <span
                                class="text-danger">*</span></label>
                        <input type="date" name="scheduled_date" class="form-control"
                            value="{{ old('scheduled_date', $order->scheduled_date->format('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.driver') }} <span class="text-danger">*</span></label>
                        <select name="driver_id" class="form-select" required>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}" {{ old('driver_id', $order->driver_id) == $driver->id ? 'selected' : '' }}>{{ $driver->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.trailer') }}</label>
                        <select name="trailer_id" class="form-select">
                            <option value="">{{ __('messages.none') }}</option>
                            @foreach($trailers as $trailer)
                                <option value="{{ $trailer->id }}" {{ old('trailer_id', $order->trailer_id) == $trailer->id ? 'selected' : '' }}>{{ $trailer->plate_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.vehicle') }}</label>
                        <select name="delivery_vehicle_id" class="form-select">
                            <option value="">{{ __('messages.none') }}</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ old('delivery_vehicle_id', $order->delivery_vehicle_id) == $vehicle->id ? 'selected' : '' }}>{{ $vehicle->plate_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.route_from') }} <span class="text-danger">*</span></label>
                        <input type="text" name="route_from" class="form-control"
                            value="{{ old('route_from', $order->route_from) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.route_to') }} <span class="text-danger">*</span></label>
                        <input type="text" name="route_to" class="form-control"
                            value="{{ old('route_to', $order->route_to) }}" required>
                    </div>

                    <div class="col-12 mt-5">
                        <h5>{{ __('messages.order_items') }}</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="items-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.product') }}</th>
                                        <th>{{ __('messages.quantity') }}</th>
                                        <th>{{ __('messages.notes') }}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $index => $item)
                                        <tr class="item-row">
                                            <td>
                                                <select name="items[{{ $index }}][product_id]" class="form-select select2"
                                                    required>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" step="0.001" name="items[{{ $index }}][quantity]"
                                                    class="form-control" value="{{ $item->quantity }}" required>
                                            </td>
                                            <td>
                                                <input type="text" name="items[{{ $index }}][notes]" class="form-control"
                                                    value="{{ $item->notes }}">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-row"><i
                                                        class="fas fa-times"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-12 mt-4 d-flex justify-content-between">
                        <a href="{{ route('transport.orders.index') }}"
                            class="btn btn-outline-secondary">{{ __('messages.cancel') }}</a>
                        <button type="submit" class="btn btn-primary px-5">{{ __('messages.update_order') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@extends('layouts.app')

@section('title', __('messages.view_sales_order') ?? 'View Sales Order')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.view_sales_order') ?? 'View Sales Order' }}: {{ $salesOrder->document_number }}
            </h1>
            <a href="{{ route('sales.sales-orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="row">
            <div class="col-md-9">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between">
                        <span>{{ __('messages.details') }}</span>
                        <span><strong>{{ __('messages.date') }}:</strong>
                            {{ $salesOrder->order_date->format('Y-m-d') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-sm-6">
                                <h6 class="fw-bold">{{ __('messages.customer_details') }}</h6>
                                <p class="mb-0">
                                    <strong>{{ $salesOrder->customer?->name ?? __('messages.walking_customer') }}</strong>
                                </p>
                                <p class="mb-0"><strong>{{ __('messages.address') }}:</strong>
                                    {{ $salesOrder->customer->address ?? '-' }}</p>
                                <p class="mb-0"><strong>{{ __('messages.city') }}:</strong>
                                    {{ $salesOrder->customer->city ?? '-' }}</p>
                                <p class="mb-0"><strong>{{ __('messages.country') }}:</strong>
                                    {{ $salesOrder->customer->country ?? '-' }}</p>
                                <p class="mb-0"><strong>{{ __('messages.phone') }}:</strong>
                                    {{ $salesOrder->customer->phone ?? '-' }}</p>
                                <p class="mb-0"><strong>{{ __('messages.email') }}:</strong>
                                    {{ $salesOrder->customer->email ?? '-' }}</p>
                            </div>
                            <div class="col-sm-6 text-sm-end">
                                <h6 class="fw-bold text-muted text-uppercase small">
                                    {{ __('messages.order_details') ?? 'Order Details' }}
                                </h6>
                                <p class="mb-1"><strong>{{ __('messages.order_number') ?? 'Order Number' }}:</strong>
                                    {{ $salesOrder->order_number }}</p>
                                <p class="mb-1">
                                    <strong>{{ __('messages.expected_delivery_date') ?? 'Expected Delivery' }}:</strong>
                                    {{ $salesOrder->expected_delivery_date ? $salesOrder->expected_delivery_date->format('Y-m-d') : '-' }}
                                </p>
                                <p class="mb-0"><strong>{{ __('messages.status') }}:</strong>
                                    <span class="badge rounded-pill bg-info px-3">{{ $salesOrder->status }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th>{{ __('messages.product') }}</th>
                                        <th>{{ __('messages.quantity') }} / {{ __('messages.unit') ?? 'Unit' }}</th>
                                        <th>{{ __('messages.unit_price') }}</th>
                                        <th>{{ __('messages.tax') }} (%)</th>
                                        <th>{{ __('messages.tax_amount') }}</th>
                                        <th>{{ __('messages.net_amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salesOrder->items as $item)
                                        <tr>
                                            <td>{{ $item->product ? $item->product->name_en : '-' }}</td>
                                            <td>
                                                {{ number_format($item->quantity, 2) }}
                                                @if($item->measurementUnit)
                                                    <small class="text-muted">({{ $item->measurementUnit->name }})</small>
                                                @endif
                                            </td>
                                            <td>{{ number_format($item->unit_price, 2) }}</td>
                                            <td>{{ number_format($item->tax_rate, 2) }}</td>
                                            <td>{{ number_format($item->tax_amount, 2) }}</td>
                                            <td>{{ number_format($item->net_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold">{{ __('messages.subtotal') }}</td>
                                        <td class="fw-bold">{{ number_format($salesOrder->subtotal, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold">{{ __('messages.tax_amount') }}</td>
                                        <td class="fw-bold">{{ number_format($salesOrder->tax_amount, 2) }}</td>
                                    </tr>
                                    @if($salesOrder->shipping_amount > 0)
                                        <tr>
                                            <td colspan="5" class="text-end fw-bold">{{ __('messages.shipping') }}</td>
                                            <td class="fw-bold">{{ number_format($salesOrder->shipping_amount, 2) }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold">{{ __('messages.grand_total') }}</td>
                                        <td class="fw-bold">{{ number_format($salesOrder->total_amount, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @if($salesOrder->terms_conditions)
                            <div class="mt-4">
                                <h6 class="fw-bold">{{ __('messages.terms_conditions') }}</h6>
                                <p class="text-muted">{{ $salesOrder->terms_conditions }}</p>
                            </div>
                        @endif

                        @if($salesOrder->notes)
                            <div class="mt-3">
                                <h6 class="fw-bold">{{ __('messages.notes') }}</h6>
                                <p class="text-muted">{{ $salesOrder->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-header">{{ __('messages.actions') }}</div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('sales.sales-orders.whatsapp', $salesOrder) }}" target="_blank"
                                class="btn btn-outline-success">
                                <i class="fab fa-whatsapp"></i> {{ __('messages.send_whatsapp') ?? 'Send via WhatsApp' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
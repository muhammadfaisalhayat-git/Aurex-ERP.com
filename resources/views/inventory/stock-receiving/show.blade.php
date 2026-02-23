@extends('layouts.app')

@section('title', __('messages.stock_receiving_details'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.stock_receiving_details') }}</h1>
            <a href="{{ route('inventory.stock-receiving.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card glassy mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">#{{ $receiving->document_number }}</h5>
                        <span class="badge badge-{{ $receiving->status === 'received' ? 'paid' : 'draft' }}">
                            {{ __('messages.' . $receiving->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-sm-6">
                                <h6 class="text-muted mb-2">{{ __('messages.warehouse') }}</h6>
                                <p class="fw-bold">{{ $receiving->warehouse->name }}</p>
                            </div>
                            <div class="col-sm-6">
                                <h6 class="text-muted mb-2">{{ __('messages.vendor') }}</h6>
                                <p class="fw-bold">{{ $receiving->vendor->name }}</p>
                            </div>
                            <div class="col-sm-6">
                                <h6 class="text-muted mb-2">{{ __('messages.date') }}</h6>
                                <p class="fw-bold">{{ $receiving->receiving_date->format('Y-m-d') }}</p>
                            </div>
                            <div class="col-sm-6">
                                <h6 class="text-muted mb-2">{{ __('messages.reference_number') }}</h6>
                                <p class="fw-bold">{{ $receiving->purchase_order_number ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('messages.product') }}</th>
                                        <th class="text-end">{{ __('messages.ordered_quantity') }}</th>
                                        <th class="text-end">{{ __('messages.received_quantity') }}</th>
                                        <th>{{ __('messages.notes') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($receiving->items as $item)
                                        <tr>
                                            <td>{{ $item->product->name }}</td>
                                            <td class="text-end">{{ number_format($item->ordered_quantity, 3) }}</td>
                                            <td class="text-end">{{ number_format($item->received_quantity, 3) }}</td>
                                            <td>{{ $item->notes ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @if($receiving->notes)
                    <div class="card glassy mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('messages.notes') }}</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $receiving->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card glassy mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('messages.actions') }}</h5>
                    </div>
                    <div class="card-body">
                        @if($receiving->status === 'pending')
                            <form action="{{ route('inventory.stock-receiving.receive', $receiving) }}" method="POST"
                                class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-check-circle me-2"></i> {{ __('messages.receive_stock') }}
                                </button>
                            </form>
                        @endif

                        <div class="d-flex gap-2">
                            <a href="{{ route('inventory.stock-receiving.pdf', $receiving) }}"
                                class="btn btn-outline-primary flex-grow-1" target="_blank">
                                <i class="fas fa-file-pdf me-2"></i> {{ __('messages.pdf') }}
                            </a>
                            <a href="{{ route('inventory.stock-receiving.whatsapp', $receiving) }}"
                                class="btn btn-outline-success flex-grow-1">
                                <i class="fab fa-whatsapp me-2"></i> {{ __('messages.whatsapp') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card glassy">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('messages.audit_info') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="text-muted small mb-1">{{ __('messages.created_by') }}</h6>
                            <p class="mb-0 small">{{ $receiving->creator->name ?? 'System' }}</p>
                            <p class="text-muted small">{{ $receiving->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                        @if($receiving->received_by)
                            <div class="mb-0">
                                <h6 class="text-muted small mb-1">{{ __('messages.received_by') }}</h6>
                                <p class="mb-0 small">{{ $receiving->receiver->name ?? 'System' }}</p>
                                <p class="text-muted small">{{ $receiving->received_at->format('Y-m-d H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
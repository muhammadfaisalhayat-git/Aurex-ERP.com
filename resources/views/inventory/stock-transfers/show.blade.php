@extends('layouts.app')

@section('title', __('messages.stock_transfer_details'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.stock_transfer_details') }}</h1>
            <a href="{{ route('inventory.stock-transfers.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card glassy mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">#{{ $transfer->document_number }}</h5>
                        @php
                            $badgeClass = match ($transfer->status) {
                                'received' => 'paid',
                                'approved' => 'posted',
                                'pending' => 'draft',
                                default => 'secondary'
                            };
                        @endphp
                        <span class="badge badge-{{ $badgeClass }}">
                            {{ __('messages.' . $transfer->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4 text-center">
                            <div class="col-md-5">
                                <h6 class="text-muted mb-2">{{ __('messages.from_warehouse') }}</h6>
                                <p class="fw-bold fs-5 mb-0">{{ $transfer->fromWarehouse->name }}</p>
                            </div>
                            <div class="col-md-2 d-flex align-items-center justify-content-center">
                                <i class="fas fa-long-arrow-alt-right fa-2x text-primary"></i>
                            </div>
                            <div class="col-md-5">
                                <h6 class="text-muted mb-2">{{ __('messages.to_warehouse') }}</h6>
                                <p class="fw-bold fs-5 mb-0">{{ $transfer->toWarehouse->name }}</p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('messages.product') }}</th>
                                        <th class="text-center">{{ __('messages.quantity') }} /
                                            {{ __('messages.unit') ?? 'Unit' }}</th>
                                        <th>{{ __('messages.notes') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transfer->items as $item)
                                        <tr>
                                            <td>{{ $item->product->name }}</td>
                                            <td class="text-center">{{ number_format($item->quantity, 3) }}
                                                {{ $item->measurementUnit->name ?? '' }}</td>
                                            <td>{{ $item->notes ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card glassy mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('messages.actions') }}</h5>
                    </div>
                    <div class="card-body">
                        @if($transfer->status === 'pending')
                            <form action="{{ route('inventory.stock-transfers.approve', $transfer) }}" method="POST"
                                class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-check-circle me-2"></i> {{ __('messages.approve_transfer') }}
                                </button>
                            </form>
                        @endif

                        @if($transfer->status === 'approved')
                            <form action="{{ route('inventory.stock-transfers.receive', $transfer) }}" method="POST"
                                class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-download me-2"></i> {{ __('messages.mark_as_received') }}
                                </button>
                            </form>
                        @endif

                        <div class="d-flex gap-2">
                            <a href="{{ route('inventory.stock-transfers.pdf', $transfer) }}"
                                class="btn btn-outline-primary flex-grow-1" target="_blank">
                                <i class="fas fa-file-pdf me-2"></i> {{ __('messages.pdf') }}
                            </a>
                            <a href="{{ route('inventory.stock-transfers.whatsapp', $transfer) }}"
                                class="btn btn-outline-success flex-grow-1">
                                <i class="fab fa-whatsapp me-2"></i> {{ __('messages.whatsapp') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card glassy">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('messages.transfer_info') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="text-muted small mb-1">{{ __('messages.requested_by') }}</h6>
                            <p class="mb-0 small">{{ $transfer->requestedBy->name ?? 'N/A' }}</p>
                            <p class="text-muted small">{{ $transfer->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                        @if($transfer->approved_by)
                            <div class="mb-3">
                                <h6 class="text-muted small mb-1">{{ __('messages.approved_by') }}</h6>
                                <p class="mb-0 small">{{ $transfer->approvedBy->name ?? 'N/A' }}</p>
                                <p class="text-muted small">{{ $transfer->approved_at->format('Y-m-d H:i') }}</p>
                            </div>
                        @endif
                        @if($transfer->received_by)
                            <div class="mb-0">
                                <h6 class="text-muted small mb-1">{{ __('messages.received_by') }}</h6>
                                <p class="mb-0 small">{{ $transfer->receivedBy->name ?? 'N/A' }}</p>
                                <p class="text-muted small">{{ $transfer->received_at->format('Y-m-d H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
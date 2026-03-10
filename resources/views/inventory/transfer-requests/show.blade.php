@extends('layouts.app')

@section('title', __('messages.transfer_request_details'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.transfer_request_details') }}</h1>
            <a href="{{ route('inventory.transfer-requests.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card glassy mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">#{{ $request->document_number }}</h5>
                        @php
                            $badgeClass = match ($request->status) {
                                'executed' => 'paid',
                                'approved' => 'posted',
                                'pending' => 'draft',
                                'rejected' => 'void',
                                default => 'secondary'
                            };
                        @endphp
                        <span class="badge badge-{{ $badgeClass }}">
                            {{ __('messages.' . $request->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4 text-center">
                            <div class="col-md-5">
                                <h6 class="text-muted mb-2">{{ __('messages.request_from') }}</h6>
                                <p class="fw-bold fs-5 mb-0">{{ $request->fromWarehouse->name }}</p>
                            </div>
                            <div class="col-md-2 d-flex align-items-center justify-content-center">
                                <i class="fas fa-long-arrow-alt-left fa-2x text-primary"></i>
                            </div>
                            <div class="col-md-5">
                                <h6 class="text-muted mb-2">{{ __('messages.requesting_warehouse') }}</h6>
                                <p class="fw-bold fs-5 mb-0">{{ $request->toWarehouse->name }}</p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('messages.product') }}</th>
                                        <th class="text-center">{{ __('messages.requested_quantity') }} /
                                            {{ __('messages.unit') ?? 'Unit' }}</th>
                                        <th>{{ __('messages.notes') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($request->items as $item)
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
                        @if($request->status === 'pending')
                            <div class="d-flex gap-2 mb-2">
                                <form action="{{ route('inventory.transfer-requests.approve', $request) }}" method="POST"
                                    class="flex-grow-1">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-check me-2"></i> {{ __('messages.approve') }}
                                    </button>
                                </form>
                                <form action="{{ route('inventory.transfer-requests.reject', $request) }}" method="POST"
                                    class="flex-grow-1">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="fas fa-times me-2"></i> {{ __('messages.reject') }}
                                    </button>
                                </form>
                            </div>
                        @endif

                        @if($request->status === 'approved')
                            <form action="{{ route('inventory.transfer-requests.execute', $request) }}" method="POST"
                                class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-shipping-fast me-2"></i> {{ __('messages.execute_transfer') }}
                                </button>
                            </form>
                        @endif

                        <div class="d-flex gap-2">
                            <a href="{{ route('inventory.transfer-requests.pdf', $request) }}"
                                class="btn btn-outline-primary flex-grow-1" target="_blank">
                                <i class="fas fa-file-pdf me-2"></i> {{ __('messages.pdf') }}
                            </a>
                            <a href="{{ route('inventory.transfer-requests.whatsapp', $request) }}"
                                class="btn btn-outline-success flex-grow-1">
                                <i class="fab fa-whatsapp me-2"></i> {{ __('messages.whatsapp') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card glassy">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('messages.request_info') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="text-muted small mb-1">{{ __('messages.requested_by') }}</h6>
                            <p class="mb-0 small">{{ $request->requestedBy->name ?? 'N/A' }}</p>
                            <p class="text-muted small">{{ $request->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                        @if($request->approved_by)
                            <div class="mb-0">
                                <h6 class="text-muted small mb-1">{{ __('messages.approved_by') }}</h6>
                                <p class="mb-0 small">{{ $request->approvedBy->name ?? 'N/A' }}</p>
                                <p class="text-muted small">{{ $request->approved_at->format('Y-m-d H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
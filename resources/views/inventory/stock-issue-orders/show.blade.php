@extends('layouts.app')

@section('title', __('messages.stock_issue_order_details'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.stock_issue_order_details') }}</h1>
            <a href="{{ route('inventory.issue-orders.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card glassy mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">#{{ $issueOrder->document_number }}</h5>
                        <span class="badge badge-{{ $issueOrder->isPosted() ? 'paid' : 'draft' }}">
                            {{ __('messages.' . ($issueOrder->status ?? 'draft')) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-sm-6">
                                <h6 class="text-muted mb-2">{{ __('messages.warehouse') }}</h6>
                                <p class="fw-bold">{{ $issueOrder->warehouse->name }}</p>
                            </div>
                            <div class="col-sm-6">
                                <h6 class="text-muted mb-2">{{ __('messages.issue_type') }}</h6>
                                <p class="fw-bold text-capitalize">{{ __('messages.' . $issueOrder->issue_type) }}</p>
                            </div>
                            <div class="col-sm-6">
                                <h6 class="text-muted mb-2">{{ __('messages.date') }}</h6>
                                <p class="fw-bold">{{ $issueOrder->issue_date->format('Y-m-d') }}</p>
                            </div>
                            <div class="col-sm-6">
                                <h6 class="text-muted mb-2">{{ __('messages.entity') }}</h6>
                                <p class="fw-bold">
                                    {{ $issueOrder->customer->name ?? ($issueOrder->vendor->name ?? '-') }}
                                </p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('messages.product') }}</th>
                                        <th class="text-end">{{ __('messages.quantity') }} /
                                            {{ __('messages.unit') ?? 'Unit' }}</th>
                                        <th>{{ __('messages.notes') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($issueOrder->items as $item)
                                        <tr>
                                            <td>{{ $item->product->name }}</td>
                                            <td class="text-end">{{ number_format($item->quantity, 3) }}
                                                {{ $item->measurementUnit->name ?? '' }}</td>
                                            <td>{{ $item->notes ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @if($issueOrder->notes)
                    <div class="card glassy mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('messages.notes') }}</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $issueOrder->notes }}</p>
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
                        @if(!$issueOrder->isPosted())
                            <form action="{{ route('inventory.issue-orders.post', $issueOrder) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-check-circle me-2"></i> {{ __('messages.post_to_ledger') }}
                                </button>
                            </form>
                        @else
                            <form action="{{ route('inventory.issue-orders.unpost', $issueOrder) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="fas fa-undo me-2"></i> {{ __('messages.unpost') }}
                                </button>
                            </form>
                        @endif

                        <div class="d-flex gap-2">
                            <a href="{{ route('inventory.issue-orders.pdf', $issueOrder) }}"
                                class="btn btn-outline-primary flex-grow-1" target="_blank">
                                <i class="fas fa-file-pdf me-2"></i> {{ __('messages.pdf') }}
                            </a>
                            <a href="{{ route('inventory.issue-orders.whatsapp', $issueOrder) }}"
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
                            <p class="mb-0 small">{{ $issueOrder->creator->name ?? 'System' }}</p>
                            <p class="text-muted small">{{ $issueOrder->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                        @if($issueOrder->posted_by)
                            <div class="mb-0">
                                <h6 class="text-muted small mb-1">{{ __('messages.posted_by') }}</h6>
                                <p class="mb-0 small">{{ $issueOrder->poster->name ?? 'System' }}</p>
                                <p class="text-muted small">{{ $issueOrder->posted_at->format('Y-m-d H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
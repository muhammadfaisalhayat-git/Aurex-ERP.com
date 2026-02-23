@extends('layouts.app')

@section('title', __('messages.stock_supply_details'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.stock_supply_details') }}</h1>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card glassy">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">{{ __('messages.stock_supply_details') }} #{{ $supply->document_number }}
                        </h3>
                        <div>
                            @if($supply->status === 'draft')
                                <form action="{{ route('inventory.stock-supply.post', $supply) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success shadow-sm me-2">
                                        <i class="fas fa-check-circle me-1"></i> {{ __('messages.post_to_ledger') }}
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('inventory.stock-supply.pdf', $supply) }}"
                                class="btn btn-outline-primary me-2" target="_blank">
                                <i class="fas fa-file-pdf me-1"></i> {{ __('messages.pdf') }}
                            </a>
                            <a href="{{ route('inventory.stock-supply.whatsapp', $supply) }}"
                                class="btn btn-outline-success me-2">
                                <i class="fab fa-whatsapp me-1"></i> {{ __('messages.whatsapp') }}
                            </a>
                            <a href="{{ route('inventory.stock-supply.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> {{ __('messages.back') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label class="fw-bold">{{ __('messages.document_number') }}:</label>
                                <p>{{ $supply->document_number }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="fw-bold">{{ __('messages.date') }}:</label>
                                <p>{{ $supply->supply_date->format('Y-m-d') }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="fw-bold">{{ __('messages.warehouse') }}:</label>
                                <p>{{ $supply->warehouse->name ?? '-' }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="fw-bold">{{ __('messages.vendor') }}:</label>
                                <p>{{ $supply->vendor->name ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label class="fw-bold">{{ __('messages.status') }}:</label>
                                <p>
                                    <span
                                        class="badge bg-{{ $supply->status === 'posted' ? 'success' : ($supply->status === 'draft' ? 'secondary' : 'info') }}">
                                        {{ ucfirst($supply->status) }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-3">
                                <label class="fw-bold">{{ __('messages.reference_number') }}:</label>
                                <p>{{ $supply->reference_number ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold">{{ __('messages.notes') }}:</label>
                                <p>{{ $supply->notes ?? '-' }}</p>
                            </div>
                        </div>

                        <h4 class="mb-3">{{ __('messages.items') }}</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('messages.product') }}</th>
                                        <th class="text-end">{{ __('messages.quantity') }}</th>
                                        <th class="text-end">{{ __('messages.unit_cost') }}</th>
                                        <th class="text-end">{{ __('messages.total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($supply->items as $item)
                                        <tr>
                                            <td>{{ $item->product->name }} ({{ $item->product->code }})</td>
                                            <td class="text-end">{{ number_format($item->quantity, 3) }}</td>
                                            <td class="text-end">{{ number_format($item->unit_cost, 2) }}</td>
                                            <td class="text-end">{{ number_format($item->total_cost, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">{{ __('messages.grand_total') }}</td>
                                        <td class="text-end fw-bold text-primary">
                                            {{ number_format($supply->total_amount, 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="mt-4 row">
                            <div class="col-md-6 text-muted small">
                                <p>{{ __('messages.created_by') }}: {{ $supply->creator->name ?? '-' }}
                                    ({{ $supply->created_at }})</p>
                                @if($supply->poster)
                                    <p>{{ __('messages.posted_by') }}: {{ $supply->poster->name }} ({{ $supply->posted_at }})
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
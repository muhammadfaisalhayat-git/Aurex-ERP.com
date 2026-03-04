@extends('layouts.app')

@section('title', __('messages.view_contract') ?? 'View Contract')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.view_contract') ?? 'View Contract' }}: {{ $contract->document_number }}</h1>
            <a href="{{ route('sales.contracts.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="row">
            <div class="col-md-9">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between">
                        <span>{{ __('messages.details') }}</span>
                        <span><strong>{{ __('messages.date') }}:</strong>
                            {{ $contract->contract_date->format('Y-m-d') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-sm-6">
                                <h6 class="fw-bold">{{ __('messages.customer_details') }}</h6>
                                <p class="mb-0">
                                    <strong>{{ $contract->customer?->name ?? __('messages.walking_customer') }}</strong></p>
                                <p class="mb-0"><strong>{{ __('messages.address') }}:</strong>
                                    {{ $contract->customer->address ?? '-' }}</p>
                                <p class="mb-0"><strong>{{ __('messages.city') }}:</strong>
                                    {{ $contract->customer->city ?? '-' }}</p>
                                <p class="mb-0"><strong>{{ __('messages.country') }}:</strong>
                                    {{ $contract->customer->country ?? '-' }}</p>
                                <p class="mb-0"><strong>{{ __('messages.phone') }}:</strong>
                                    {{ $contract->customer->phone ?? '-' }}</p>
                                <p class="mb-0"><strong>{{ __('messages.email') }}:</strong>
                                    {{ $contract->customer->email ?? '-' }}</p>
                            </div>
                            <div class="col-sm-6 text-sm-end">
                                <h6 class="fw-bold text-muted text-uppercase small">
                                    {{ __('messages.contract_details') ?? 'Contract Details' }}
                                </h6>
                                <p class="mb-1"><strong>{{ __('messages.contract_number') ?? 'Contract Number' }}:</strong>
                                    {{ $contract->contract_number }}</p>
                                <p class="mb-1"><strong>{{ __('messages.start_date') ?? 'Start Date' }}:</strong>
                                    {{ $contract->start_date ? $contract->start_date->format('Y-m-d') : '-' }}</p>
                                <p class="mb-1"><strong>{{ __('messages.end_date') ?? 'End Date' }}:</strong>
                                    {{ $contract->end_date ? $contract->end_date->format('Y-m-d') : '-' }}</p>
                                <p class="mb-0"><strong>{{ __('messages.status') }}:</strong>
                                    <span class="badge rounded-pill bg-info px-3">{{ $contract->status }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th>{{ __('messages.product') }}</th>
                                        <th>{{ __('messages.quantity') }}</th>
                                        <th>{{ __('messages.unit_price') }}</th>
                                        <th>{{ __('messages.total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contract->items as $item)
                                        <tr>
                                            <td>{{ $item->product ? $item->product->name_en : '-' }}</td>
                                            <td>{{ number_format($item->quantity, 2) }}</td>
                                            <td>{{ number_format($item->unit_price, 2) }}</td>
                                            <td>{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">{{ __('messages.grand_total') }}</td>
                                        <td class="fw-bold">{{ number_format($contract->contract_value, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @if($contract->terms_conditions)
                            <div class="mt-4">
                                <h6 class="fw-bold">{{ __('messages.terms_conditions') }}</h6>
                                <p class="text-muted">{{ $contract->terms_conditions }}</p>
                            </div>
                        @endif

                        @if($contract->notes)
                            <div class="mt-3">
                                <h6 class="fw-bold">{{ __('messages.notes') }}</h6>
                                <p class="text-muted">{{ $contract->notes }}</p>
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
                            <a href="{{ route('sales.contracts.whatsapp', $contract) }}" target="_blank"
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
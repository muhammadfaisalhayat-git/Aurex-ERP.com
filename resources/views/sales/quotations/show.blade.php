@extends('layouts.app')

@section('title', __('messages.view_quotation'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.view_quotation') }}: {{ $quotation->document_number }}</h1>
            <a href="{{ route('sales.quotations.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="row">
            <div class="col-md-9">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between">
                        <span>{{ __('messages.details') }}</span>
                        <span><strong>{{ __('messages.date') }}:</strong>
                            {{ $quotation->quotation_date->format('Y-m-d') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-sm-6">
                                <h6 class="fw-bold">{{ __('messages.customer_details') }}</h6>
                                <p class="mb-0">{{ $quotation->customer->name }}</p>
                                <p class="mb-0">{{ $quotation->customer->address ?? '' }}</p>
                                <p class="mb-0">{{ $quotation->customer->phone ?? '' }}</p>
                                <p class="mb-0">{{ $quotation->customer->email ?? '' }}</p>
                            </div>
                            <div class="col-sm-6 text-sm-end">
                                <h6 class="fw-bold text-muted text-uppercase small">{{ __('messages.quotation_details') }}</h6>
                                <p class="mb-1"><strong>{{ __('messages.quotation_number') }}:</strong>
                                    {{ $quotation->document_number }}</p>
                                @if($quotation->expiry_date)
                                    <p class="mb-1"><strong>{{ __('messages.expiry_date') }}:</strong>
                                        {{ $quotation->expiry_date->format('Y-m-d') }}</p>
                                @endif
                                <p class="mb-0"><strong>{{ __('messages.status') }}:</strong>
                                    @php
                                        $statusClass = match ($quotation->status) {
                                            'draft' => 'secondary',
                                            'sent' => 'info',
                                            'accepted' => 'success',
                                            'rejected' => 'danger',
                                            'expired' => 'warning',
                                            default => 'light'
                                        };
                                    @endphp
                                    <span class="badge rounded-pill bg-{{ $statusClass }} px-3">{{ __('messages.' . $quotation->status) }}</span>
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
                                        <th>{{ __('messages.tax') }} (%)</th>
                                        <th>{{ __('messages.tax_amount') }}</th>
                                        <th>{{ __('messages.net_amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quotation->items as $item)
                                        <tr>
                                            <td>{{ $item->product ? $item->product->name_en : '-' }}</td>
                                            <td>{{ number_format($item->quantity, 2) }}</td>
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
                                        <td class="fw-bold">{{ number_format($quotation->subtotal, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold">{{ __('messages.tax_amount') }}</td>
                                        <td class="fw-bold">{{ number_format($quotation->tax_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold">{{ __('messages.grand_total') }}</td>
                                        <td class="fw-bold">{{ number_format($quotation->total_amount, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @if($quotation->terms_conditions)
                            <div class="mt-4">
                                <h6 class="fw-bold">{{ __('messages.terms_conditions') }}</h6>
                                <p class="text-muted">{{ $quotation->terms_conditions }}</p>
                            </div>
                        @endif

                        @if($quotation->notes)
                            <div class="mt-3">
                                <h6 class="fw-bold">{{ __('messages.notes') }}</h6>
                                <p class="text-muted">{{ $quotation->notes }}</p>
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
                            @can('edit quotations')
                                <a href="{{ route('sales.quotations.edit', $quotation) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> {{ __('messages.edit_quotation') }}
                                </a>
                            @endcan

                            @can('delete quotations')
                                <form action="{{ route('sales.quotations.destroy', $quotation) }}" method="POST"
                                    onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="fas fa-trash"></i> {{ __('messages.delete_quotation') }}
                                    </button>
                                </form>
                            @endcan

                            <a href="{{ route('sales.quotations.pdf', $quotation) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-print"></i> {{ __('messages.print') }}
                            </a>

                            <a href="{{ route('sales.quotations.whatsapp', $quotation) }}" target="_blank"
                                class="btn btn-outline-success">
                                <i class="fab fa-whatsapp"></i> Send via WhatsApp
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">{{ __('messages.info') }}</div>
                    <div class="card-body">
                        <div class="mb-2">
                            <strong>{{ __('messages.branch') }}:</strong><br>
                            {{ $quotation->branch->name ?? '-' }}
                        </div>
                        <div class="mb-2">
                            <strong>{{ __('messages.warehouse') }}:</strong><br>
                            {{ $quotation->warehouse->name ?? '-' }}
                        </div>
                        <div class="mb-2">
                            <strong>{{ __('messages.salesman') }}:</strong><br>
                            {{ $quotation->salesman->name ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
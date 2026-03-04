@extends('layouts.app')

@section('title', __('messages.return_details') ?? 'Return Details')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">{{ __('messages.return_details') ?? 'Return Details' }}
                            #{{ $salesReturn->return_number }}</h3>
                        <div>
                            @if($salesReturn->status === 'draft')
                                <form action="{{ route('sales.returns.post', $salesReturn) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-primary me-2 shadow-sm">
                                        <i class="fas fa-check-circle me-1"></i> {{ __('messages.post') ?? 'Post' }}
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('sales.returns.print', $salesReturn) }}" target="_blank"
                                class="btn btn-outline-info me-2">
                                <i class="fas fa-print me-1"></i> {{ __('messages.print') ?? 'Print' }}
                            </a>
                            <a href="{{ route('sales.returns.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> {{ __('messages.back') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <h5>{{ __('messages.return_info') ?? 'Return Information' }}</h5>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="150">{{ __('messages.document_number') }}:</th>
                                        <td>{{ $salesReturn->document_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('messages.return_number') }}:</th>
                                        <td>{{ $salesReturn->return_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('messages.date') }}:</th>
                                        <td>{{ $salesReturn->return_date->format('Y-m-d') }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('messages.status') }}:</th>
                                        <td>
                                            <span
                                                class="badge bg-{{ $salesReturn->status === 'posted' ? 'success' : ($salesReturn->status === 'draft' ? 'secondary' : 'danger') }}">
                                                {{ ucfirst($salesReturn->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @if($salesReturn->salesInvoice)
                                        <tr>
                                            <th>{{ __('messages.sales_invoice') }}:</th>
                                            <td>
                                                <a href="{{ route('sales.invoices.show', $salesReturn->salesInvoice) }}">
                                                    {{ $salesReturn->salesInvoice->invoice_number }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th>{{ __('messages.return_type') ?? 'Return Type' }}:</th>
                                        <td>
                                            <span
                                                class="badge bg-info text-dark">{{ ucfirst($salesReturn->return_type) }}</span>
                                        </td>
                                    </tr>
                                    @if($salesReturn->return_type === 'cash' && $salesReturn->bankAccount)
                                        <tr>
                                            <th>{{ __('messages.bank_account') ?? 'Bank Account' }}:</th>
                                            <td>{{ $salesReturn->bankAccount->name }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-4">
                                <h5>{{ __('messages.customer_info') }}</h5>
                                <p>
                                    <strong>{{ $salesReturn->customer?->name ?? __('messages.walking_customer') }}</strong><br>
                                        <strong>{{ __('messages.address') }}:</strong>
                                        {{ $salesReturn->customer->address ?? '-' }}<br>
                                        <strong>{{ __('messages.city') }}:</strong>
                                        {{ $salesReturn->customer->city ?? '-' }}<br>
                                        <strong>{{ __('messages.country') }}:</strong>
                                        {{ $salesReturn->customer->country ?? '-' }}<br>
                                        <strong>{{ __('messages.phone') }}:</strong>
                                        {{ $salesReturn->customer->phone ?? '-' }}<br>
                                        <strong>{{ __('messages.email') }}:</strong> {{ $salesReturn->customer->email ?? '-' }}
                                    @else
                                        <span
                                            class="text-muted">{{ __('messages.walking_customer') ?? 'Walking Customer' }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4">
                                <h5>{{ __('messages.other_info') ?? 'Other Information' }}</h5>
                                <p>
                                    <strong>{{ __('messages.branch') }}:</strong>
                                    {{ $salesReturn->branch->name ?? '-' }}<br>
                                    <strong>{{ __('messages.warehouse') }}:</strong>
                                    {{ $salesReturn->warehouse->name ?? '-' }}
                                </p>
                                <p>
                                    <strong>{{ __('messages.return_reason') }}:</strong>
                                    {{ $salesReturn->return_reason }}<br>
                                    {{ $salesReturn->reason_description }}
                                </p>
                            </div>
                        </div>

                        <h4 class="mt-4">{{ __('messages.items') }}</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.product') }}</th>
                                        <th class="text-end">{{ __('messages.quantity') }}</th>
                                        <th class="text-end">{{ __('messages.unit_price') }}</th>
                                        <th class="text-end">{{ __('messages.tax') }}</th>
                                        <th class="text-end">{{ __('messages.total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salesReturn->items as $item)
                                        <tr>
                                            <td>{{ $item->product->name }}</td>
                                            <td class="text-end">{{ number_format($item->quantity, 3) }}</td>
                                            <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                                            <td class="text-end">{{ number_format($item->tax_amount, 2) }}</td>
                                            <td class="text-end">{{ number_format($item->total_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">{{ __('messages.subtotal') }}</td>
                                        <td class="text-end">{{ number_format($salesReturn->subtotal, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">{{ __('messages.tax') }}</td>
                                        <td class="text-end">{{ number_format($salesReturn->tax_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">{{ __('messages.total') }}</td>
                                        <td class="text-end fw-bold">{{ number_format($salesReturn->total_amount, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @if($salesReturn->notes)
                            <div class="mt-4">
                                <h5>{{ __('messages.notes') }}</h5>
                                <p class="text-muted">{{ $salesReturn->notes }}</p>
                            </div>
                        @endif

                        <div class="mt-4 text-muted small">
                            <p>
                                {{ __('messages.created_by') }}: {{ $salesReturn->creator->name ?? '-' }}
                                {{ __('messages.at') }}
                                {{ $salesReturn->created_at }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
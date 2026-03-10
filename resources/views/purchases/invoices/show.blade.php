@extends('layouts.app')

@section('title', __('messages.view_purchase_invoice') . ': ' . $invoice->invoice_number)

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">{{ __('messages.purchase_invoice') }}: {{ $invoice->invoice_number }}</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a
                                href="{{ route('purchases.invoices.index') }}">{{ __('messages.invoices') }}</a></li>
                        <li class="breadcrumb-item active">{{ $invoice->invoice_number }}</li>
                    </ol>
                </nav>
            </div>
            <div class="btn-group">
                <a href="{{ route('purchases.invoices.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                </a>
                @if(!$invoice->isPosted())
                    <a href="{{ route('purchases.invoices.edit', $invoice->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                    </a>
                    <form action="{{ route('purchases.invoices.post', $invoice->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success"
                            onclick="return confirm('{{ __('messages.confirm_post_invoice') }}')">
                            <i class="fas fa-check-circle"></i> {{ __('messages.post_invoice') }}
                        </button>
                    </form>
                @endif
                <a href="#" class="btn btn-info">
                    <i class="fas fa-print"></i> {{ __('messages.print') }}
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-9">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="mb-0">{{ __('messages.invoice_details') }}</h5>
                        <span
                            class="badge bg-{{ $invoice->status == 'posted' ? 'success' : ($invoice->status == 'draft' ? 'secondary' : 'warning') }}">
                            {{ strtoupper($invoice->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th style="width: 40%">{{ __('messages.vendor') }}:</th>
                                        <td>{{ $invoice->vendor->name_en }} / {{ $invoice->vendor->name_ar }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('messages.invoice_number') }}:</th>
                                        <td>{{ $invoice->invoice_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('messages.document_number') }}:</th>
                                        <td>{{ $invoice->document_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('messages.invoice_date') }}:</th>
                                        <td>{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th style="width: 40%">{{ __('messages.branch') }}:</th>
                                        <td>{{ $invoice->branch->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('messages.warehouse') }}:</th>
                                        <td>{{ $invoice->warehouse->name_en }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('messages.payment_terms') }}:</th>
                                        <td>{{ strtoupper($invoice->payment_terms) }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('messages.due_date') }}:</th>
                                        <td>{{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="table-responsive mt-4">
                            <table class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('messages.product') }}</th>
                                        <th class="text-end">{{ __('messages.quantity') }} /
                                            {{ __('messages.unit') ?? 'Unit' }}</th>
                                        <th class="text-end">{{ __('messages.unit_price') }}</th>
                                        <th class="text-end">{{ __('messages.tax') }} %</th>
                                        <th class="text-end">{{ __('messages.tax_amount') }}</th>
                                        <th class="text-end">{{ __('messages.total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoice->items as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $item->product->code }}</strong><br>
                                                {{ $item->product->name_en }}
                                            </td>
                                            <td class="text-end">
                                                {{ number_format($item->quantity, 2) }}
                                                <small class="text-muted">{{ $item->measurementUnit->name ?? '' }}</small>
                                            </td>
                                            <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                                            <td class="text-end">{{ number_format($item->tax_rate, 2) }}%</td>
                                            <td class="text-end">{{ number_format($item->tax_amount, 2) }}</td>
                                            <td class="text-end">{{ number_format($item->total_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($invoice->notes)
                            <div class="mt-4">
                                <h6>{{ __('messages.notes') }}:</h6>
                                <p class="mb-0 text-muted">{{ $invoice->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('messages.financial_summary') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('messages.subtotal') }}:</span>
                            <span class="fw-bold">{{ number_format($invoice->subtotal, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('messages.tax_amount') }}:</span>
                            <span class="fw-bold">{{ number_format($invoice->tax_amount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-danger">
                            <span>{{ __('messages.discount') }}:</span>
                            <span class="fw-bold">-{{ number_format($invoice->discount_amount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('messages.shipping') }}:</span>
                            <span class="fw-bold">{{ number_format($invoice->shipping_amount, 2) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between h5 fw-bold mb-0">
                            <span>{{ __('messages.total') }}:</span>
                            <span>{{ number_format($invoice->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('messages.audit_info') }}</h5>
                    </div>
                    <div class="card-body small">
                        <div class="mb-2">
                            <span class="text-muted d-block">{{ __('messages.created_by') }}:</span>
                            <span>{{ $invoice->creator->name ?? 'System' }}</span>
                            <span class="text-muted d-block">{{ $invoice->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                        @if($invoice->posted_at)
                            <div>
                                <span class="text-muted d-block">{{ __('messages.posted_by') }}:</span>
                                <span>{{ $invoice->poster->name ?? 'System' }}</span>
                                <span class="text-muted d-block">{{ $invoice->posted_at->format('Y-m-d H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@extends('layouts.app')

@section('content')
    <div class="container-fluid" id="print-area">
        <div class="row mb-4 d-print-none">
            <div class="col">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fas fa-print me-1"></i> {{ __('sales.print') }}
                </button>
                <a href="{{ route('sales.invoices.show', $invoice) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('common.back') }}
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-5">
                <div class="row mb-4">
                    <div class="col-6">
                        <h2 class="mb-4">Aurex ERP</h2>
                        <div>
                            123 Business Street<br>
                            Riyadh, Saudi Arabia<br>
                            Phone: +966 12 345 6789<br>
                            Email: info@aurex-erp.com
                        </div>
                    </div>
                    <div class="col-6 text-end">
                        <h1 class="text-primary mb-3">{{ __('sales.invoice') }}</h1>
                        <h4 class="mb-1">#{{ $invoice->invoice_number }}</h4>
                        <div class="text-muted">
                            {{ __('sales.date') }}: {{ $invoice->invoice_date->format('Y-m-d') }}<br>
                            {{ __('sales.due_date') }}: {{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '-' }}
                        </div>
                        <div class="mt-2">
                            <span
                                class="badge bg-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'posted' ? 'primary' : 'secondary') }} fs-6">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row mb-5">
                    <div class="col-6">
                        <h5 class="text-muted mb-3">{{ __('sales.bill_to') }}:</h5>
                        <h4 class="mb-2">{{ $invoice->customer->company_name }}</h4>
                        <div>
                            {{ $invoice->customer->address }}<br>
                            {{ $invoice->customer->city }}, {{ $invoice->customer->country }}<br>
                            {{ __('sales.tax_number') }}: {{ $invoice->customer->tax_number }}
                        </div>
                    </div>
                </div>

                <table class="table table-striped table-bordered mb-4">
                    <thead class="bg-light">
                        <tr>
                            <th>{{ __('sales.item') }}</th>
                            <th class="text-end" style="width: 100px;">{{ __('sales.quantity') }}</th>
                            <th class="text-end" style="width: 150px;">{{ __('sales.unit_price') }}</th>
                            <th class="text-end" style="width: 150px;">{{ __('sales.tax') }}</th>
                            <th class="text-end" style="width: 150px;">{{ __('sales.total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->product->name }}</strong>
                                    @if($item->description)
                                        <br><small class="text-muted">{{ $item->description }}</small>
                                    @endif
                                </td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end">{{ number_format($item->tax_amount, 2) }}</td>
                                <td class="text-end">{{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="row justify-content-end">
                    <div class="col-5">
                        <table class="table table-borderless">
                            <tr>
                                <td>{{ __('sales.subtotal') }}:</td>
                                <td class="text-end fw-bold">{{ number_format($invoice->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('sales.tax') }}:</td>
                                <td class="text-end fw-bold">{{ number_format($invoice->tax_amount, 2) }}</td>
                            </tr>
                            @if($invoice->discount_amount > 0)
                                <tr>
                                    <td>{{ __('sales.discount') }}:</td>
                                    <td class="text-end text-danger">-{{ number_format($invoice->discount_amount, 2) }}</td>
                                </tr>
                            @endif
                            <tr class="border-top">
                                <td class="fs-5">{{ __('sales.grand_total') }}:</td>
                                <td class="text-end fs-5 fw-bold">{{ number_format($invoice->grand_total, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($invoice->notes)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>{{ __('sales.notes') }}:</h5>
                            <p class="text-muted">{{ $invoice->notes }}</p>
                        </div>
                    </div>
                @endif

                @if($invoice->terms)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>{{ __('sales.terms_conditions') }}:</h5>
                            <p class="text-muted">{{ $invoice->terms }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #print-area,
            #print-area * {
                visibility: visible;
            }

            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .d-print-none {
                display: none !important;
            }
        }
    </style>
@endsection
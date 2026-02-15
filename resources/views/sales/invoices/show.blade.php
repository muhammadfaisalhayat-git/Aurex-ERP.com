@extends('layouts.app')

@section('title', __('sales.invoice_details'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">{{ __('sales.invoice_details') }} #{{ $invoice->invoice_number }}</h3>
                        <div>
                            <a href="{{ route('sales.invoices.index') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-arrow-left"></i> {{ __('common.back') }}
                            </a>

                            @if($invoice->status === 'draft')
                                <a href="{{ route('sales.invoices.edit', $invoice) }}" class="btn btn-warning me-2">
                                    <i class="fas fa-edit"></i> {{ __('common.edit') }}
                                </a>

                                @can('post invoices')
                                    <form action="{{ route('sales.invoices.post', $invoice) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success me-2"
                                            onclick="return confirm('{{ __('common.confirm_action') }}')">
                                            <i class="fas fa-check"></i> {{ __('sales.post') }}
                                        </button>
                                    </form>
                                @endcan
                            @elseif($invoice->status === 'posted')
                                @can('post invoices')
                                    <form action="{{ route('sales.invoices.unpost', $invoice) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger me-2"
                                            onclick="return confirm('{{ __('common.confirm_action') }}')">
                                            <i class="fas fa-undo"></i> {{ __('sales.unpost') }}
                                        </button>
                                    </form>
                                @endcan
                            @endif

                            <a href="{{ route('sales.invoices.print', $invoice) }}" target="_blank"
                                class="btn btn-info me-2">
                                <i class="fas fa-print"></i> {{ __('sales.print') }}
                            </a>

                            <a href="{{ route('sales.invoices.pdf', $invoice) }}" class="btn btn-primary me-2">
                                <i class="fas fa-file-pdf"></i> {{ __('sales.pdf') }}
                            </a>

                            <a href="{{ route('sales.invoices.whatsapp', $invoice) }}" target="_blank"
                                class="btn btn-success me-2">
                                <i class="fab fa-whatsapp"></i> {{ __('messages.send_whatsapp') ?? 'WhatsApp' }}
                            </a>

                            @if($invoice->status === 'draft')
                                <form action="{{ route('sales.invoices.destroy', $invoice) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('{{ __('common.confirm_delete') }}')">
                                        <i class="fas fa-trash"></i> {{ __('common.delete') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <h5>{{ __('sales.invoice_info') }}</h5>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="150">{{ __('sales.document_number') }}:</th>
                                        <td>{{ $invoice->document_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('sales.invoice_number') }}:</th>
                                        <td>{{ $invoice->invoice_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('sales.date') }}:</th>
                                        <td>{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('sales.due_date') }}:</th>
                                        <td>{{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('sales.status') }}:</th>
                                        <td>
                                            <span
                                                class="badge bg-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'posted' ? 'primary' : 'secondary') }}">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('sales.payment_terms') }}:</th>
                                        <td>{{ ucfirst($invoice->payment_terms) }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('sales.reference_number') }}:</th>
                                        <td>{{ $invoice->reference_number ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <h5>{{ __('sales.customer_info') }}</h5>
                                <p>
                                    <strong>{{ $invoice->customer->company_name ?? __('sales.cash_customer') }}</strong><br>
                                    {{ $invoice->customer->address ?? '-' }}<br>
                                    {{ $invoice->customer->city ?? '-' }}, {{ $invoice->customer->country ?? '-' }}<br>
                                    <abbr title="{{ __('sales.phone') }}">P:</abbr>
                                    {{ $invoice->customer->phone ?? '-' }}<br>
                                    <abbr title="{{ __('sales.email') }}">E:</abbr> {{ $invoice->customer->email ?? '-' }}
                                </p>
                            </div>
                            <div class="col-md-4">
                                <h5>{{ __('sales.branch_info') }}</h5>
                                <p>
                                    <strong>{{ $invoice->branch->name_en }}</strong><br>
                                    {{ $invoice->warehouse->name_en }}
                                </p>
                                @if($invoice->salesman)
                                    <p><strong>{{ __('sales.salesman') }}:</strong> {{ $invoice->salesman->name }}</p>
                                @endif
                            </div>
                        </div>

                        <h4 class="mt-4">{{ __('sales.items') }}</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('sales.product') }}</th>
                                        <th class="text-end">{{ __('sales.quantity') }}</th>
                                        <th class="text-end">{{ __('sales.unit_price') }}</th>
                                        <th class="text-end">{{ __('sales.discount') }}</th>
                                        <th class="text-end">{{ __('sales.tax') }}</th>
                                        <th class="text-end">{{ __('sales.total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoice->items as $item)
                                        <tr>
                                            <td>
                                                {{ $item->product->name }}
                                                @if($item->description && $item->description !== $item->product->name)
                                                    <div class="small text-muted">{{ $item->description }}</div>
                                                @endif
                                            </td>
                                            <td class="text-end">{{ number_format($item->quantity, 3) }}</td>
                                            <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                                            <td class="text-end">
                                                {{ number_format($item->discount_amount, 2) }}
                                                @if($item->discount_percentage > 0)
                                                    <small class="text-muted">({{ $item->discount_percentage }}%)</small>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                {{ number_format($item->tax_amount, 2) }}
                                                <small class="text-muted">({{ $item->tax_rate }}%)</small>
                                            </td>
                                            <td class="text-end">{{ number_format($item->gross_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold">{{ __('sales.subtotal') }}</td>
                                        <td class="text-end">{{ number_format($invoice->total_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold">{{ __('sales.tax') }}</td>
                                        <td class="text-end">{{ number_format($invoice->tax_amount, 2) }}</td>
                                    </tr>
                                    @if($invoice->discount_amount > 0)
                                        <tr>
                                            <td colspan="5" class="text-end fw-bold">{{ __('sales.discount') }}</td>
                                            <td class="text-end">{{ number_format($invoice->discount_amount, 2) }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold">{{ __('sales.grand_total') }}</td>
                                        <td class="text-end fw-bold">{{ number_format($invoice->subtotal, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @if($invoice->notes)
                            <div class="mt-4">
                                <h5>{{ __('sales.notes') }}</h5>
                                <p class="text-muted">{{ $invoice->notes }}</p>
                            </div>
                        @endif

                        <div class="mt-4 text-muted small">
                            <p>
                                {{ __('common.created_by') }}: {{ $invoice->creator->name ?? '-' }} {{ __('common.at') }}
                                {{ $invoice->created_at }}
                                @if($invoice->poster)
                                    <br>
                                    {{ __('sales.posted_by') }}: {{ $invoice->poster->name }} {{ __('common.at') }}
                                    {{ $invoice->posted_at }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
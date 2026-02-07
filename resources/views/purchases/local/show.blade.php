@extends('layouts.app')

@section('title', __('local_purchase.view'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ __('local_purchase.view') }}</h1>
        <div>
            <a href="{{ route('local-purchases.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('general.back') }}
            </a>
            @if($localPurchase->status === 'draft')
                @can('local_purchase.edit')
                <a href="{{ route('local-purchases.edit', $localPurchase) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> {{ __('general.edit') }}
                </a>
                @endcan
                @can('local_purchase.post')
                <form action="{{ route('local-purchases.post', $localPurchase) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success" onclick="return confirm('{{ __('local_purchase.confirm_post') }}')">
                        <i class="fas fa-check"></i> {{ __('local_purchase.post') }}
                    </button>
                </form>
                @endcan
                @can('local_purchase.delete')
                <form action="{{ route('local-purchases.destroy', $localPurchase) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('{{ __('general.confirm_delete') }}')">
                        <i class="fas fa-trash"></i> {{ __('general.delete') }}
                    </button>
                </form>
                @endcan
            @elseif($localPurchase->status === 'posted')
                @can('local_purchase.post')
                <form action="{{ route('local-purchases.unpost', $localPurchase) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-warning" onclick="return confirm('{{ __('local_purchase.confirm_unpost') }}')">
                        <i class="fas fa-undo"></i> {{ __('local_purchase.unpost') }}
                    </button>
                </form>
                @endcan
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $localPurchase->document_number }}</h5>
                    <span class="badge bg-{{ $localPurchase->status === 'posted' ? 'success' : ($localPurchase->status === 'draft' ? 'warning' : 'secondary') }}">
                        {{ __('local_purchase.status_' . $localPurchase->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>{{ __('local_purchase.supplier_info') }}</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="fw-bold">{{ __('local_purchase.supplier_name') }}</td>
                                    <td>{{ $localPurchase->supplier_name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">{{ __('local_purchase.supplier_phone') }}</td>
                                    <td>{{ $localPurchase->supplier_phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">{{ __('local_purchase.supplier_email') }}</td>
                                    <td>{{ $localPurchase->supplier_email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">{{ __('local_purchase.supplier_address') }}</td>
                                    <td>{{ $localPurchase->supplier_address ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>{{ __('local_purchase.document_info') }}</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="fw-bold">{{ __('local_purchase.invoice_number') }}</td>
                                    <td>{{ $localPurchase->invoice_number }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">{{ __('local_purchase.invoice_date') }}</td>
                                    <td>{{ $localPurchase->invoice_date->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">{{ __('local_purchase.branch') }}</td>
                                    <td>{{ $localPurchase->branch->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">{{ __('local_purchase.warehouse') }}</td>
                                    <td>{{ $localPurchase->warehouse->name }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <h6>{{ __('local_purchase.items') }}</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('local_purchase.item') }}</th>
                                    <th class="text-end">{{ __('local_purchase.quantity') }}</th>
                                    <th class="text-end">{{ __('local_purchase.unit_price') }}</th>
                                    <th class="text-end">{{ __('local_purchase.discount') }}</th>
                                    <th class="text-end">{{ __('local_purchase.tax') }}</th>
                                    <th class="text-end">{{ __('local_purchase.total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($localPurchase->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->item->code }} - {{ $item->item->name }}</td>
                                    <td class="text-end">{{ number_format($item->quantity, 2) }}</td>
                                    <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end">{{ number_format($item->discount_amount, 2) }}</td>
                                    <td class="text-end">{{ number_format($item->tax_amount, 2) }} ({{ $item->tax_rate }}%)</td>
                                    <td class="text-end">{{ number_format($item->gross_amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-end fw-bold">{{ __('local_purchase.subtotal') }}</td>
                                    <td class="text-end">{{ number_format($localPurchase->items->sum(fn($i) => ($i->quantity * $i->unit_price)), 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-end fw-bold">{{ __('local_purchase.discount') }}</td>
                                    <td class="text-end">{{ number_format($localPurchase->discount_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-end fw-bold">{{ __('local_purchase.tax') }}</td>
                                    <td class="text-end">{{ number_format($localPurchase->tax_amount, 2) }}</td>
                                </tr>
                                <tr class="table-primary">
                                    <td colspan="6" class="text-end fw-bold">{{ __('local_purchase.total') }}</td>
                                    <td class="text-end fw-bold">{{ number_format($localPurchase->gross_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($localPurchase->notes)
                    <div class="mt-4">
                        <h6>{{ __('local_purchase.notes') }}</h6>
                        <p>{{ $localPurchase->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('local_purchase.audit_info') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="fw-bold">{{ __('local_purchase.created_by') }}</td>
                            <td>{{ $localPurchase->creator->name }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('local_purchase.created_at') }}</td>
                            <td>{{ $localPurchase->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        @if($localPurchase->posted_by)
                        <tr>
                            <td class="fw-bold">{{ __('local_purchase.posted_by') }}</td>
                            <td>{{ $localPurchase->postedBy->name }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('local_purchase.posted_at') }}</td>
                            <td>{{ $localPurchase->posted_at?->format('Y-m-d H:i') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

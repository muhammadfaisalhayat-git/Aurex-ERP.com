@extends('layouts.app')

@section('title', __('local_purchase.title'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ __('local_purchase.title') }}</h1>
        @can('local_purchase.create')
        <a href="{{ route('local-purchases.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> {{ __('local_purchase.create') }}
        </a>
        @endcan
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('local_purchase.document_number') }}</th>
                            <th>{{ __('local_purchase.invoice_number') }}</th>
                            <th>{{ __('local_purchase.invoice_date') }}</th>
                            <th>{{ __('local_purchase.supplier_name') }}</th>
                            <th>{{ __('local_purchase.warehouse') }}</th>
                            <th>{{ __('local_purchase.gross_amount') }}</th>
                            <th>{{ __('local_purchase.status') }}</th>
                            <th>{{ __('general.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $purchase)
                        <tr>
                            <td>
                                <a href="{{ route('local-purchases.show', $purchase) }}">
                                    {{ $purchase->document_number }}
                                </a>
                            </td>
                            <td>{{ $purchase->invoice_number }}</td>
                            <td>{{ $purchase->invoice_date->format('Y-m-d') }}</td>
                            <td>{{ $purchase->supplier_name }}</td>
                            <td>{{ $purchase->warehouse->name }}</td>
                            <td>{{ number_format($purchase->gross_amount, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $purchase->status === 'posted' ? 'success' : ($purchase->status === 'draft' ? 'warning' : 'secondary') }}">
                                    {{ __('local_purchase.status_' . $purchase->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('local-purchases.show', $purchase) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($purchase->status === 'draft')
                                        @can('local_purchase.edit')
                                        <a href="{{ route('local-purchases.edit', $purchase) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        @can('local_purchase.post')
                                        <form action="{{ route('local-purchases.post', $purchase) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('{{ __('local_purchase.confirm_post') }}')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    @elseif($purchase->status === 'posted')
                                        @can('local_purchase.post')
                                        <form action="{{ route('local-purchases.unpost', $purchase) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('{{ __('local_purchase.confirm_unpost') }}')">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">{{ __('local_purchase.no_records') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $purchases->links() }}
        </div>
    </div>
</div>
@endsection

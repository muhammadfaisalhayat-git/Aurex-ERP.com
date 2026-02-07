@extends('layouts.app')

@section('title', __('messages.sales_invoices'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.sales_invoices') }}</h1>
            @can('create sales invoices')
                <a href="{{ route('sales.invoices.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('messages.create') }}
                </a>
            @endcan
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('messages.invoice_number') }}</th>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.customer') }}</th>
                                <th>{{ __('messages.total') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $invoice)
                                <tr>
                                    <td>
                                        <a href="{{ route('sales.invoices.show', $invoice) }}">
                                            {{ $invoice->invoice_number }}
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ $invoice->document_number }}</small>
                                    </td>
                                    <td>{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                                    <td>{{ $invoice->customer->name ?? '-' }}</td>
                                    <td>{{ number_format($invoice->total_amount, 2) }}</td>
                                    <td>
                                        @php
                                            $statusClass = [
                                                'draft' => 'secondary',
                                                'posted' => 'info',
                                                'paid' => 'success',
                                                'partial' => 'primary',
                                                'overdue' => 'danger',
                                                'cancelled' => 'dark',
                                            ][$invoice->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">
                                            {{ __('messages.' . $invoice->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('sales.invoices.show', $invoice) }}" class="btn btn-sm btn-info"
                                                title="{{ __('messages.view') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($invoice->isEditable())
                                                @can('edit sales invoices')
                                                    <a href="{{ route('sales.invoices.edit', $invoice) }}"
                                                        class="btn btn-sm btn-primary" title="{{ __('messages.edit') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                            @endif
                                            <a href="{{ route('sales.invoices.pdf', $invoice) }}"
                                                class="btn btn-sm btn-secondary" title="{{ __('messages.download_pdf') }}">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">{{ __('messages.no_records_found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $invoices->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
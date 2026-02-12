@extends('layouts.app')

@section('title', __('messages.view_customer'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.view_customer') }}: {{ $customer->name }}</h1>
            <a href="{{ route('sales.customers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">{{ $customer->name }}</h5>
                        <p class="text-muted">{{ $customer->code }}</p>
                        <div class="mb-3">
                            @php
                                $statusClass = $customer->status == 'active' ? 'success' : ($customer->status == 'blocked' ? 'danger' : 'secondary');
                            @endphp
                            <span class="badge bg-{{ $statusClass }}">
                                {{ __('messages.' . $customer->status) }}
                            </span>
                        </div>
                        <p class="mb-1"><strong>{{ __('messages.current_balance') }}:</strong>
                            {{ number_format($customer->current_balance, 2) }}</p>
                        <p class="mb-1"><strong>{{ __('messages.credit_limit') }}:</strong>
                            {{ number_format($customer->credit_limit, 2) }}</p>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">{{ __('messages.actions') }}</div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @can('edit customers')
                                <a href="{{ route('sales.customers.edit', $customer) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> {{ __('messages.edit_customer') }}
                                </a>
                            @endcan

                            @can('delete customers')
                                <form action="{{ route('sales.customers.destroy', $customer) }}" method="POST"
                                    onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="fas fa-trash"></i> {{ __('messages.delete_customer') }}
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <ul class="nav nav-tabs mb-3" id="customerTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details"
                            type="button" role="tab">{{ __('messages.details') }}</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions"
                            type="button" role="tab">{{ __('messages.transactions') }}</button>
                    </li>
                </ul>

                <div class="tab-content" id="customerTabsContent">
                    <div class="tab-pane fade show active" id="details" role="tabpanel">
                        <div class="card mb-4">
                            <div class="card-header">{{ __('messages.basic_information') }}</div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">{{ __('messages.customer_group') }}</div>
                                    <div class="col-sm-8">{{ $customer->group->name ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">{{ __('messages.branch') }}</div>
                                    <div class="col-sm-8">{{ $customer->branch->name ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">{{ __('messages.salesman') }}</div>
                                    <div class="col-sm-8">{{ $customer->salesman->name ?? '-' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">{{ __('messages.contact_information') }}</div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">{{ __('messages.contact_person') }}</div>
                                    <div class="col-sm-8">{{ $customer->contact_person ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">{{ __('messages.phone') }}</div>
                                    <div class="col-sm-8">{{ $customer->phone ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">{{ __('messages.mobile') }}</div>
                                    <div class="col-sm-8">{{ $customer->mobile ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">{{ __('messages.email') }}</div>
                                    <div class="col-sm-8">{{ $customer->email ?? '-' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">{{ __('messages.address_information') }}</div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">{{ __('messages.address') }}</div>
                                    <div class="col-sm-8">{{ $customer->address ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">{{ __('messages.city') }}</div>
                                    <div class="col-sm-8">{{ $customer->city ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">{{ __('messages.region') }}</div>
                                    <div class="col-sm-8">{{ $customer->region ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">{{ __('messages.postal_code') }}</div>
                                    <div class="col-sm-8">{{ $customer->postal_code ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="transactions" role="tabpanel">
                        <div class="card">
                            <div class="card-header">{{ __('messages.recent_transactions') }}</div>
                            <div class="card-body">
                                @php
                                    $transactions = collect();

                                    foreach ($customer->customerRequests as $request) {
                                        $transactions->push([
                                            'id' => $request->id,
                                            'date' => $request->request_date,
                                            'number' => $request->document_number,
                                            'type' => 'request',
                                            'total' => '-',
                                            'status' => $request->status,
                                            'route' => route('sales.customer-requests.show', $request),
                                        ]);
                                    }

                                    foreach ($customer->quotations as $quotation) {
                                        $transactions->push([
                                            'id' => $quotation->id,
                                            'date' => $quotation->quotation_date,
                                            'number' => $quotation->document_number,
                                            'type' => 'quotation',
                                            'total' => number_format($quotation->total_amount, 2),
                                            'status' => $quotation->status,
                                            'route' => route('sales.quotations.show', $quotation),
                                        ]);
                                    }

                                    foreach ($customer->salesInvoices as $invoice) {
                                        $transactions->push([
                                            'id' => $invoice->id,
                                            'date' => $invoice->invoice_date,
                                            'number' => $invoice->document_number,
                                            'type' => 'invoice',
                                            'total' => number_format($invoice->total_amount, 2),
                                            'status' => $invoice->status,
                                            'route' => route('sales.invoices.show', $invoice),
                                        ]);
                                    }

                                    $transactions = $transactions->sortByDesc('date');
                                @endphp

                                @if ($transactions->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('messages.date') }}</th>
                                                    <th>{{ __('messages.document_number') }}</th>
                                                    <th>{{ __('messages.document_type') }}</th>
                                                    <th>{{ __('messages.amount') }}</th>
                                                    <th>{{ __('messages.status') }}</th>
                                                    <th>{{ __('messages.actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($transactions as $transaction)
                                                    <tr>
                                                        <td>{{ $transaction['date']->format('Y-m-d') }}</td>
                                                        <td>{{ $transaction['number'] }}</td>
                                                        <td>
                                                            <span class="badge bg-secondary">
                                                                {{ __('messages.' . $transaction['type']) }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $transaction['total'] }}</td>
                                                        <td>
                                                            @php
                                                                $statusClass = match ($transaction['status']) {
                                                                    'draft', 'pending' => 'warning',
                                                                    'posted', 'approved', 'paid', 'converted' => 'success',
                                                                    'cancelled', 'rejected' => 'danger',
                                                                    default => 'secondary',
                                                                };
                                                            @endphp
                                                            <span class="badge bg-{{ $statusClass }}">
                                                                {{ __('messages.' . $transaction['status']) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="{{ $transaction['route'] }}"
                                                                class="btn btn-sm btn-info text-white">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted text-center pt-3">{{ __('messages.no_records_found') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
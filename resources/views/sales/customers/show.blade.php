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

                            @if($customer->mobile || $customer->phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->mobile ?? $customer->phone) }}" 
                                   target="_blank" class="btn btn-success">
                                    <i class="fab fa-whatsapp"></i> {{ __('messages.send_whatsapp') }}
                                </a>
                            @endif

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
                        <button class="nav-link {{ !request('tab') || request('tab') == 'details' ? 'active' : '' }}" id="details-tab" data-bs-toggle="tab" data-bs-target="#details"
                            type="button" role="tab">{{ __('messages.details') }}</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ request('tab') == 'transactions' ? 'active' : '' }}" id="transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions"
                            type="button" role="tab">{{ __('messages.transactions') }}</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ request('tab') == 'statement' ? 'active' : '' }}" id="statement-tab" data-bs-toggle="tab" data-bs-target="#statement"
                            type="button" role="tab">{{ __('messages.statement') ?? 'Statement' }}</button>
                    </li>
                </ul>

                <div class="tab-content" id="customerTabsContent">
                    <div class="tab-pane fade {{ !request('tab') || request('tab') == 'details' ? 'show active' : '' }}" id="details" role="tabpanel">
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

                    <div class="tab-pane fade {{ request('tab') == 'transactions' ? 'show active' : '' }}" id="transactions" role="tabpanel">
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

                    <div class="tab-pane fade {{ request('tab') == 'statement' ? 'show active' : '' }}" id="statement" role="tabpanel">
                        <div class="card shadow-sm mb-4 border-0">
                            <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white py-3">
                                <h6 class="mb-0 fw-bold"><i class="fas fa-file-invoice-dollar me-2"></i>{{ __('messages.balance_statement') ?? 'Balance Statement' }}</h6>
                                <div class="btn-group">
                                    <a href="{{ route('sales.customers.statement', [$customer, 'export' => 'excel'] + request()->all()) }}" class="btn btn-sm btn-light border fw-bold text-success">
                                        <i class="fas fa-file-excel me-1"></i> Excel
                                    </a>
                                    <a href="{{ route('sales.customers.statement', [$customer, 'export' => 'pdf'] + request()->all()) }}" class="btn btn-sm btn-light border fw-bold text-danger">
                                        <i class="fas fa-file-pdf me-1"></i> PDF
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('sales.customers.show', $customer) }}" method="GET" class="row g-3 mb-4 p-3 bg-light rounded border mx-0">
                                    <input type="hidden" name="tab" value="statement">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">{{ __('messages.from_date') }}</label>
                                        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">{{ __('messages.to_date') }}</label>
                                        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">{{ __('messages.item_search') ?? 'Search Item' }}</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                            <input type="text" name="item_search" class="form-control" placeholder="Product name or code..." value="{{ request('item_search') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                                            <i class="fas fa-filter me-1"></i> {{ __('messages.filter') }}
                                        </button>
                                    </div>
                                </form>

                                <div class="table-responsive">
                                    <table class="table table-sm table-hover align-middle border">
                                        <thead class="bg-dark text-white">
                                            <tr>
                                                <th class="ps-3">{{ __('messages.date') }}</th>
                                                <th>{{ __('messages.reference') ?? 'Reference' }}</th>
                                                <th>{{ __('messages.description') }}</th>
                                                <th class="text-end">{{ __('messages.debit') }}</th>
                                                <th class="text-end">{{ __('messages.credit') }}</th>
                                                <th class="text-end pe-3">{{ __('messages.balance') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $entriesList = $entries ?? collect();
                                                $runningBalance = $openingBalance ?? 0;
                                                $totalDebit = 0;
                                                $totalCredit = 0;
                                            @endphp
                                            <tr class="table-info fw-bold">
                                                <td colspan="2"></td>
                                                <td><i class="fas fa-history me-2 text-primary"></i>{{ __('messages.opening_balance') }}</td>
                                                <td colspan="2"></td>
                                                <td class="text-end pe-3">{{ number_format($runningBalance, 2) }}</td>
                                            </tr>
                                            @forelse ($entriesList as $entry)
                                                @php
                                                    $runningBalance += ($entry->debit - $entry->credit);
                                                    $totalDebit += $entry->debit;
                                                    $totalCredit += $entry->credit;
                                                @endphp
                                                <tr>
                                                    <td class="ps-3 small text-muted">{{ $entry->transaction_date->format('Y-m-d') }}</td>
                                                    <td>
                                                        <span class="fw-bold small text-primary">{{ $entry->reference_number }}</span>
                                                        <br>
                                                        <span class="badge bg-secondary" style="font-size: 0.6rem;">{{ strtoupper(class_basename($entry->reference_type)) }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="fw-semibold text-dark">{{ $entry->description }}</div>
                                                        @if($entry->reference instanceof \App\Models\SalesInvoice)
                                                            @php $refItems = $entry->reference->items()->with('product')->get(); @endphp
                                                            <div class="mt-1 d-flex flex-wrap gap-1">
                                                                @foreach($refItems as $item)
                                                                    <span class="badge bg-light text-dark border fw-normal" style="font-size: 0.65rem;">
                                                                        <i class="fas fa-box text-muted me-1"></i>{{ $item->product->name_en ?? $item->description }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="text-end text-danger fw-semibold">{{ $entry->debit > 0 ? number_format($entry->debit, 2) : '-' }}</td>
                                                    <td class="text-end text-success fw-semibold">{{ $entry->credit > 0 ? number_format($entry->credit, 2) : '-' }}</td>
                                                    <td class="text-end pe-3 fw-bold {{ $runningBalance >= 0 ? 'text-dark' : 'text-danger' }}">
                                                        {{ number_format($runningBalance, 2) }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-5 text-muted">
                                                        <i class="fas fa-folder-open fa-3x mb-2 opacity-25"></i>
                                                        <br>
                                                        {{ __('messages.no_records_found') }}
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        @if(isset($entries) && $entries->count() > 0)
                                            <tfoot class="table-light">
                                                <tr class="fw-bold border-top-2">
                                                    <td colspan="3" class="text-end text-uppercase pe-3" style="font-size: 0.75rem;">{{ __('messages.total') ?? 'Total' }}</td>
                                                    <td class="text-end text-danger py-2">{{ number_format($totalDebit, 2) }}</td>
                                                    <td class="text-end text-success py-2">{{ number_format($totalCredit, 2) }}</td>
                                                    <td class="text-end pe-3 py-2 bg-dark text-white">{{ number_format($runningBalance, 2) }}</td>
                                                </tr>
                                            </tfoot>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
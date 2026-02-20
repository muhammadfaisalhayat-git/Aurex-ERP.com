@extends('layouts.app')

@section('title', __('messages.customers'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.customers') }}</h1>
            <div class="btn-group">
                @can('create customer_registration')
                    <a href="{{ route('sales.customer-registrations.create') }}" class="btn btn-success">
                        <i class="fas fa-user-plus"></i> {{ __('customer_registration.create') }}
                    </a>
                @endcan
            </div>
        </div>

        <turbo-frame id="customers_frame" data-turbo-action="advance">
            <div class="card mb-3">
                <div class="card-body py-2">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <span class="me-3 text-muted"><i class="fas fa-filter"></i> {{ __('messages.filter_by_status') }}:</span>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('sales.customers.index', ['search' => request('search')]) }}" 
                                    class="btn btn-outline-secondary {{ !request('status') ? 'active' : '' }}">
                                    {{ __('messages.all') }}
                                    </a>
                                    <a href="{{ route('sales.customers.index', ['status' => 'active', 'search' => request('search')]) }}" 
                                    class="btn btn-outline-success {{ request('status') == 'active' ? 'active' : '' }}">
                                    {{ __('customer_registration.status_active') }}
                                    </a>
                                    <a href="{{ route('sales.customers.index', ['status' => 'pending', 'search' => request('search')]) }}" 
                                    class="btn btn-outline-warning {{ request('status') == 'pending' ? 'active' : '' }}">
                                    {{ __('customer_registration.status_pending') }}
                                    </a>
                                    <a href="{{ route('sales.customers.index', ['status' => 'under_review', 'search' => request('search')]) }}" 
                                    class="btn btn-outline-info {{ request('status') == 'under_review' ? 'active' : '' }}">
                                    {{ __('customer_registration.status_under_review') }}
                                    </a>
                                    <a href="{{ route('sales.customers.index', ['status' => 'rejected', 'search' => request('search')]) }}" 
                                    class="btn btn-outline-danger {{ request('status') == 'rejected' ? 'active' : '' }}">
                                    {{ __('customer_registration.status_rejected') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <form action="{{ route('sales.customers.index') }}" method="GET" data-turbo-frame="customers_frame" id="search-form">
                                @if(request('status'))
                                    <input type="hidden" name="status" value="{{ request('status') }}">
                                @endif
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control border-start-0" 
                                        id="customer-search"
                                        placeholder="{{ __('messages.search') }}..." 
                                        value="{{ request('search') }}"
                                        autocomplete="off"
                                        oninput="if(this.value.length >= 0) { clearTimeout(window.searchTimeout); window.searchTimeout = setTimeout(() => { this.form.requestSubmit(); }, 300); }">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.code') }}</th>
                                    <th>{{ __('messages.name') }}</th>
                                    <th>{{ __('messages.email') }}</th>
                                    <th>{{ __('messages.phone') }}</th>
                                    <th>{{ __('messages.balance') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                    <tr>
                                        <td>
                                            <a href="{{ route('sales.customers.show', $customer) }}" data-turbo-frame="main-frame">
                                                {{ $customer->code }}
                                            </a>
                                        </td>
                                        <td>{{ $customer->name }}</td>
                                        <td>{{ $customer->email }}</td>
                                        <td>{{ $customer->phone }}</td>
                                        <td>{{ number_format($customer->current_balance, 2) }}</td>
                                        <td>
                                            @php
                                                $statusClass = match($customer->status) {
                                                    'active' => 'success',
                                                    'pending' => 'warning',
                                                    'under_review' => 'info',
                                                    'rejected' => 'danger',
                                                    'inactive' => 'secondary',
                                                    'blocked' => 'dark',
                                                    default => 'primary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">
                                                {{ __('customer_registration.status_' . $customer->status) ?? __('messages.' . $customer->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                @can('view customers')
                                                    <a href="{{ route('sales.customers.show', $customer) }}" class="btn btn-sm btn-info"
                                                        title="{{ __('messages.view') }}" data-turbo-frame="main-frame">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('edit customers')
                                                    <a href="{{ route('sales.customers.edit', $customer) }}"
                                                        class="btn btn-sm btn-primary" title="{{ __('messages.edit') }}" data-turbo-frame="main-frame">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('view customers')
                                                    <a href="{{ route('sales.customers.statement', $customer) }}"
                                                        class="btn btn-sm btn-secondary" title="{{ __('messages.view_statement') }}" data-turbo-frame="main-frame">
                                                        <i class="fas fa-file-invoice-dollar"></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">{{ __('messages.no_records_found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $customers->links() }}
                    </div>
                </div>
            </div>
        </turbo-frame>
    </div>
@endsection
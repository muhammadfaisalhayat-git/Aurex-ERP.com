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
                                <p class="text-muted text-center pt-3">{{ __('messages.feature_coming_soon') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
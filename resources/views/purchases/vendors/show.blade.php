@extends('layouts.app')

@section('title', __('messages.view_vendor'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.view_vendor') }}</h1>
            <div class="d-flex gap-2">
                <a href="{{ route('purchases.vendors.edit', $vendor) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                </a>
                <a href="{{ route('purchases.vendors.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <div class="avatar avatar-xl mb-3">
                            <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                {{ substr($vendor->name_en, 0, 1) }}
                            </span>
                        </div>
                        <h5 class="card-title">{{ $vendor->name }}</h5>
                        <p class="text-muted mb-1">{{ $vendor->code }}</p>
                        <span
                            class="badge bg-{{ $vendor->status === 'active' ? 'success' : ($vendor->status === 'blocked' ? 'danger' : 'warning') }}">
                            {{ __('messages.' . $vendor->status) }}
                        </span>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('messages.financial_summary') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('messages.opening_balance') }}</span>
                            <span class="fw-bold">{{ number_format($vendor->opening_balance, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('messages.current_balance') }}</span>
                            <span class="fw-bold text-{{ $vendor->current_balance > 0 ? 'danger' : 'success' }}">
                                {{ number_format($vendor->current_balance, 2) }}
                            </span>
                        </div>
                        <hr>
                        <a href="{{ route('purchases.vendors.statement', $vendor) }}"
                            class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-file-invoice-dollar"></i> {{ __('messages.view_statement') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('messages.vendor_details') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted d-block small text-uppercase">{{ __('messages.name_en') }}</label>
                                <span>{{ $vendor->name_en }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted d-block small text-uppercase">{{ __('messages.name_ar') }}</label>
                                <span>{{ $vendor->name_ar }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted d-block small text-uppercase">{{ __('messages.branch') }}</label>
                                <span>{{ $vendor->branch ? $vendor->branch->name : 'N/A' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label
                                    class="text-muted d-block small text-uppercase">{{ __('messages.tax_number') }}</label>
                                <span>{{ $vendor->tax_number ?: 'N/A' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label
                                    class="text-muted d-block small text-uppercase">{{ __('messages.commercial_registration') }}</label>
                                <span>{{ $vendor->commercial_registration ?: 'N/A' }}</span>
                            </div>
                        </div>

                        <h6 class="border-bottom pb-2 mt-4">{{ __('messages.contact_information') }}</h6>
                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <label
                                    class="text-muted d-block small text-uppercase">{{ __('messages.contact_person') }}</label>
                                <span>{{ $vendor->contact_person ?: 'N/A' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted d-block small text-uppercase">{{ __('messages.email') }}</label>
                                <span>{{ $vendor->email ?: 'N/A' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted d-block small text-uppercase">{{ __('messages.phone') }}</label>
                                <span>{{ $vendor->phone ?: 'N/A' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted d-block small text-uppercase">{{ __('messages.mobile') }}</label>
                                <span>{{ $vendor->mobile ?: 'N/A' }}</span>
                            </div>
                        </div>

                        <h6 class="border-bottom pb-2 mt-4">{{ __('messages.address_information') }}</h6>
                        <div class="row mt-3">
                            <div class="col-md-12 mb-3">
                                <label class="text-muted d-block small text-uppercase">{{ __('messages.address') }}</label>
                                <span>{{ $vendor->address ?: 'N/A' }}</span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted d-block small text-uppercase">{{ __('messages.city') }}</label>
                                <span>{{ $vendor->city ?: 'N/A' }}</span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted d-block small text-uppercase">{{ __('messages.region') }}</label>
                                <span>{{ $vendor->region ?: 'N/A' }}</span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label
                                    class="text-muted d-block small text-uppercase">{{ __('messages.postal_code') }}</label>
                                <span>{{ $vendor->postal_code ?: 'N/A' }}</span>
                            </div>
                        </div>

                        @if($vendor->notes)
                            <h6 class="border-bottom pb-2 mt-4">{{ __('messages.notes') }}</h6>
                            <div class="mt-3">
                                <p>{{ $vendor->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
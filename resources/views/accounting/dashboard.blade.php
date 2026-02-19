@extends('layouts.app')

@section('title', __('messages.accounting_system') . ' - ' . __('messages.dashboard'))

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0">{{ __('messages.accounting_system') }} {{ __('messages.dashboard') }}</h2>
            <div class="text-muted">{{ __('messages.today') }}: {{ date('Y-m-d') }}</div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary">
                                <i class="fas fa-arrow-down fa-lg"></i>
                            </div>
                            <span class="text-muted small">{{ __('messages.today') }}</span>
                        </div>
                        <h6 class="text-muted mb-1">{{ __('messages.total_debit') }}</h6>
                        <h3 class="mb-0 fw-bold">{{ number_format($stats->total_debit ?? 0, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="bg-danger bg-opacity-10 p-3 rounded-3 text-danger">
                                <i class="fas fa-arrow-up fa-lg"></i>
                            </div>
                            <span class="text-muted small">{{ __('messages.today') }}</span>
                        </div>
                        <h6 class="text-muted mb-1">{{ __('messages.total_credit') }}</h6>
                        <h3 class="mb-0 fw-bold">{{ number_format($stats->total_credit ?? 0, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success">
                                <i class="fas fa-sync fa-lg"></i>
                            </div>
                            <span class="text-muted small">{{ __('messages.today') }}</span>
                        </div>
                        <h6 class="text-muted mb-1">{{ __('messages.net_movement') }}</h6>
                        <h3 class="mb-0 fw-bold {{ $netMovement >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($netMovement, 2) }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Top Accounts -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="card-title mb-0 fw-bold">
                            <i class="fas fa-list-ul me-2 text-primary"></i>
                            {{ __('messages.top_active_accounts') }}
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-3">{{ __('messages.account') }}</th>
                                        <th class="text-end pe-3">{{ __('messages.amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topAccounts as $account)
                                        <tr>
                                            <td class="ps-3">
                                                <div class="fw-semibold">{{ $account->chartOfAccount->name }}</div>
                                                <div class="small text-muted">{{ $account->chartOfAccount->code }}</div>
                                            </td>
                                            <td class="text-end pe-3 fw-bold">{{ number_format($account->volume, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center py-4">{{ __('messages.no_data_found') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Customer Movements -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="card-title mb-0 fw-bold">
                            <i class="fas fa-users me-2 text-success"></i>
                            {{ __('messages.top_customers_movement') }}
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-3">{{ __('messages.customer') }}</th>
                                        <th class="text-end pe-3">{{ __('messages.net_movement') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topCustomers as $customer)
                                        <tr>
                                            <td class="ps-3 fw-semibold">{{ $customer->customer->name }}</td>
                                            <td
                                                class="text-end pe-3 fw-bold {{ $customer->movement >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($customer->movement, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center py-4">{{ __('messages.no_data_found') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Supplier Movements -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="card-title mb-0 fw-bold">
                            <i class="fas fa-truck-moving me-2 text-warning"></i>
                            {{ __('messages.top_vendors_movement') }}
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-3">{{ __('messages.vendor') }}</th>
                                        <th class="text-end pe-3">{{ __('messages.net_movement') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topSuppliers as $supplier)
                                        <tr>
                                            <td class="ps-3 fw-semibold">{{ $supplier->vendor->name }}</td>
                                            <td
                                                class="text-end pe-3 fw-bold {{ $supplier->movement >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($supplier->movement, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center py-4">{{ __('messages.no_data_found') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
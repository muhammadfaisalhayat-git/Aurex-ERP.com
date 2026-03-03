@extends('layouts.app')

@section('title', __('messages.transport') . ' - ' . __('messages.contract_details'))

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('messages.contract_details') }}: {{ $contract->contract_number }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('transport.contracts.index') }}">{{ __('messages.transport_contracts') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('messages.details') }}</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <a href="{{ route('transport.contracts.edit', $contract) }}" class="btn btn-outline-primary">
                <i class="fas fa-edit me-2"></i>{{ __('messages.edit') }}
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title">{{ __('messages.contractor_info') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="small text-muted mb-1">{{ __('messages.name') }}</label>
                        <div class="fw-bold">{{ $contract->contractor_name }}</div>
                    </div>
                    <div class="mb-0">
                        <label class="small text-muted mb-1">{{ __('messages.phone') }}</label>
                        <div class="fw-bold">{{ $contract->contractor_phone ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title">{{ __('messages.contract_timeline') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="small text-muted mb-1">{{ __('messages.start_date') }}</label>
                        <div class="fw-bold text-success">{{ $contract->start_date->format('Y-m-d') }}</div>
                    </div>
                    <div class="mb-0">
                        <label class="small text-muted mb-1">{{ __('messages.end_date') }}</label>
                        <div class="fw-bold text-danger">{{ $contract->end_date->format('Y-m-d') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header text-white bg-primary">
                    <h5 class="card-title mb-0">{{ __('messages.financial_summary') }}</h5>
                </div>
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <label class="small text-muted mb-1">{{ __('messages.contract_value') }}</label>
                    <h2 class="mb-0 text-primary">{{ number_format($contract->contract_value, 2) }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">{{ __('messages.status_and_terms') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="small text-muted mb-1">{{ __('messages.current_status') }}</label>
                            <div>
                                <span
                                    class="badge bg-primary px-3 py-2 fs-6">{{ __('messages.' . $contract->status) }}</span>
                            </div>
                        </div>
                    </div>
                    <hr class="my-4">
                </div>
            </div>
        </div>
    </div>
@endsection
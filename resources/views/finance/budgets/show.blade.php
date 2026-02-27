@extends('layouts.app')

@section('title', __('messages.budget_details') . ' - ' . __('messages.finance'))

@section('content')
<div class="container-fluid px-4">
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.finance') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('finance.budgets.index') }}">{{ __('messages.budgets') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('messages.budget_details') }}</li>
            </ol>
        </nav>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.budget_details') }}</h6>
            <div>
                <a href="{{ route('finance.budgets.edit', $budget->id) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-edit me-1"></i> {{ __('messages.edit') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6 mb-4">
                    <label class="small text-muted mb-1">{{ __('messages.name_en') }}</label>
                    <div class="h5">{{ $budget->name_en }}</div>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="small text-muted mb-1">{{ __('messages.name_ar') }}</label>
                    <div class="h5">{{ $budget->name_ar ?? '-' }}</div>
                </div>
                <div class="col-md-4 mb-4">
                    <label class="small text-muted mb-1">{{ __('messages.fiscal_year') }}</label>
                    <div class="h5">{{ $budget->fiscal_year }}</div>
                </div>
                <div class="col-md-4 mb-4">
                    <label class="small text-muted mb-1">{{ __('messages.total_amount') }}</label>
                    <div class="h5 text-primary font-weight-bold">{{ number_format($budget->total_amount, 2) }}</div>
                </div>
                <div class="col-md-4 mb-4">
                    <label class="small text-muted mb-1">{{ __('messages.status') }}</label>
                    <div>
                        <span class="badge bg-{{ $budget->status == 'active' ? 'success' : ($budget->status == 'draft' ? 'info' : 'secondary') }} h5">
                            {{ strtoupper($budget->status) }}
                        </span>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="small text-muted mb-1">{{ __('messages.start_date') }}</label>
                    <div class="h5">{{ $budget->start_date }}</div>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="small text-muted mb-1">{{ __('messages.end_date') }}</label>
                    <div class="h5">{{ $budget->end_date }}</div>
                </div>
            </div>

            <div class="mb-4">
                <label class="small text-muted mb-1">{{ __('messages.budget_utilization') }}</label>
                @php
                    $percentage = $budget->total_amount > 0 ? ($budget->spent_amount / $budget->total_amount) * 100 : 0;
                    $barColor = $percentage > 90 ? 'danger' : ($percentage > 70 ? 'warning' : 'success');
                @endphp
                <div class="progress mb-2" style="height: 25px;">
                    <div class="progress-bar bg-{{ $barColor }}" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                        {{ number_format($percentage, 1) }}%
                    </div>
                </div>
                <div class="d-flex justify-content-between small text-muted">
                    <span>{{ __('messages.spent') }}: {{ number_format($budget->spent_amount, 2) }}</span>
                    <span>{{ __('messages.remaining') }}: {{ number_format($budget->total_amount - $budget->spent_amount, 2) }}</span>
                </div>
            </div>

            <div class="mb-0">
                <label class="small text-muted mb-1">{{ __('messages.notes') }}</label>
                <div class="p-3 bg-light rounded text-gray-700">
                    {{ $budget->notes ?? __('messages.no_notes') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

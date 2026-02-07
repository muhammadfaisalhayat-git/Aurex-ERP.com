@extends('layouts.app')

@section('title', __('reports.supplier_title'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ __('reports.supplier_title') }}</h1>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('reports.by_code_name') }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ __('reports.by_code_name_desc') }}</p>
                    <a href="{{ route('reports.suppliers.by-code-name') }}" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> {{ __('reports.view_report') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('reports.local_purchases') }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ __('reports.local_purchases_desc') }}</p>
                    <a href="{{ route('reports.suppliers.local-purchases') }}" class="btn btn-primary w-100">
                        <i class="fas fa-file-invoice"></i> {{ __('reports.view_report') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('reports.purchase_summary') }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ __('reports.purchase_summary_desc') }}</p>
                    <a href="{{ route('reports.suppliers.purchase-summary') }}" class="btn btn-primary w-100">
                        <i class="fas fa-chart-bar"></i> {{ __('reports.view_report') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

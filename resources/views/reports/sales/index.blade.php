@extends('layouts.app')

@section('title', __('reports.sales_title'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ __('reports.sales_title') }}</h1>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('reports.by_customer') }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ __('reports.by_customer_desc') }}</p>
                    <a href="{{ route('reports.sales.by-customer') }}" class="btn btn-primary w-100">
                        <i class="fas fa-users"></i> {{ __('reports.view_report') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('reports.by_item') }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ __('reports.by_item_desc') }}</p>
                    <a href="{{ route('reports.sales.by-item') }}" class="btn btn-primary w-100">
                        <i class="fas fa-boxes"></i> {{ __('reports.view_report') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('reports.date_wise') }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ __('reports.date_wise_desc') }}</p>
                    <a href="{{ route('reports.sales.date-wise') }}" class="btn btn-primary w-100">
                        <i class="fas fa-calendar-alt"></i> {{ __('reports.view_report') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

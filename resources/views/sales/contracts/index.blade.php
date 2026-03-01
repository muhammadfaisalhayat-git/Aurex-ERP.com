@extends('layouts.app')

@section('title', __('messages.sales_contracts') ?? 'Sales Contracts')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">{{ __('messages.sales_contracts') ?? 'Sales Contracts' }}</h3>
                        <a href="#" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> {{ __('messages.create') }}
                        </a>
                    </div>
                    <div class="card-body text-center py-5">
                        <div class="opacity-50 mb-3">
                            <i class="fas fa-file-contract fa-4x"></i>
                        </div>
                        <h4>{{ __('messages.feature_coming_soon') ?? 'Feature Coming Soon' }}</h4>
                        <p class="text-muted">The Sales Contracts module is currently under development.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
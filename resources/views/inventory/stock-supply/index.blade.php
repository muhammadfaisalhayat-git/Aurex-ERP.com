@extends('layouts.app')

@section('title', __('messages.stock_management'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.stock_supply') }}</h1>
            <a href="{{ route('inventory.stock-supply.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i> {{ __('messages.create') }}
            </a>
        </div>

        <div class="card glassy">
            <div class="card-body py-5 text-center">
                <i class="fas fa-warehouse fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">{{ __('messages.feature_coming_soon') }}</h4>
                <p class="text-muted">{{ __('messages.no_records_found') }}</p>
            </div>
        </div>
    </div>
@endsection
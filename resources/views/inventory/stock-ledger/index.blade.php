@extends('layouts.app')

@section('title', __('messages.stock_ledger'))

@section('content')
    <div class="container-fluid">
        <div class="page-header mb-4">
            <h1 class="page-title">{{ __('messages.stock_ledger') }}</h1>
        </div>

        <div class="card glassy">
            <div class="card-body py-5 text-center">
                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">{{ __('messages.feature_coming_soon') }}</h4>
                <p class="text-muted">{{ __('messages.no_records_found') }}</p>
            </div>
        </div>
    </div>
@endsection
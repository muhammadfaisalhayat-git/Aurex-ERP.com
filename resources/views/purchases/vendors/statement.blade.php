@extends('layouts.app')

@section('title', __('messages.vendor_statement'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.vendor_statement') }}</h1>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print"></i> {{ __('messages.print') }}
                </button>
                <a href="{{ route('purchases.vendors.show', $vendor) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                </a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('messages.vendor_info') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>{{ $vendor->name }}</strong></p>
                        <p class="mb-1">{{ $vendor->code }}</p>
                        <p class="mb-0 text-muted">{{ $vendor->address }}</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="mb-1"><strong>{{ __('messages.current_balance') }}</strong></p>
                        <h3 class="text-{{ $vendor->current_balance > 0 ? 'danger' : 'success' }}">
                            {{ number_format($vendor->current_balance, 2) }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.document_number') }}</th>
                                <th>{{ __('messages.description') }}</th>
                                <th class="text-end">{{ __('messages.debit') }}</th>
                                <th class="text-end">{{ __('messages.credit') }}</th>
                                <th class="text-end">{{ __('messages.balance') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $vendor->created_at->format('Y-m-d') }}</td>
                                <td>-</td>
                                <td>{{ __('messages.opening_balance') }}</td>
                                <td class="text-end">0.00</td>
                                <td class="text-end">0.00</td>
                                <td class="text-end">{{ number_format($vendor->opening_balance, 2) }}</td>
                            </tr>
                            {{-- Transaction rows would go here in a real implementation --}}
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-end">{{ __('messages.total_balance') }}</th>
                                <th class="text-end">{{ number_format($vendor->current_balance, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
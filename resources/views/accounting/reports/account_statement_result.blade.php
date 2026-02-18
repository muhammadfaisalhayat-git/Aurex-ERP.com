@extends('layouts.app')

@section('title', __('messages.account_statement_report'))

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
            <h1 class="h3 mb-0 text-gray-800">{{ __('messages.account_statement_report') }}</h1>
            <div>
                <button onclick="window.print()" class="btn btn-secondary">
                    <i class="fas fa-print me-1"></i> {{ __('messages.print') }}
                </button>
                <a href="{{ route('accounting.gl.reports.account-statement') }}" class="btn btn-outline-primary ms-2">
                    <i class="fas fa-filter me-1"></i> {{ __('messages.change_filters') }}
                </a>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-1">{{ $account->code }} -
                            {{ $isRtl ? ($account->name_ar ?? $account->name_en) : ($account->name_en ?? $account->name_ar) }}
                        </h5>
                        <p class="text-muted mb-0">{{ __('messages.date_range') }}: {{ $request->start_date }} -
                            {{ $request->end_date }}</p>
                        @if(isset($customer))
                            <p class="text-info mb-0">{{ __('messages.customer') }}: {{ $customer->name_en ?? $customer->name_ar ?? $customer->name }}</p>
                        @endif
                        @if(isset($vendor))
                            <p class="text-info mb-0">{{ __('messages.vendor') }}: {{ $vendor->name_en ?? $vendor->name_ar ?? $vendor->name }}</p>
                        @endif
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ __('messages.opening_balance') }}: {{ number_format($openingBalance, 2) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.reference') }}</th>
                                <th>{{ __('messages.description') }}</th>
                                <th class="text-end">{{ __('messages.debit') }}</th>
                                <th class="text-end">{{ __('messages.credit') }}</th>
                                <th class="text-end">{{ __('messages.running_balance') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $currentBalance = $openingBalance; @endphp
                            <tr>
                                <td colspan="5" class="text-end fw-bold">{{ __('messages.opening_balance') }}</td>
                                <td class="text-end fw-bold">{{ number_format($openingBalance, 2) }}</td>
                            </tr>
                            @forelse($entries as $entry)
                                @php $currentBalance += ($entry->debit - $entry->credit); @endphp
                                <tr>
                                    <td>{{ $entry->transaction_date->format('Y-m-d') }}</td>
                                    <td><code>{{ $entry->reference_number }}</code></td>
                                    <td>{{ $entry->description }}</td>
                                    <td class="text-end text-success">
                                        {{ $entry->debit > 0 ? number_format($entry->debit, 2) : '-' }}</td>
                                    <td class="text-end text-danger">
                                        {{ $entry->credit > 0 ? number_format($entry->credit, 2) : '-' }}</td>
                                    <td class="text-end fw-bold">{{ number_format($currentBalance, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        {{ __('messages.no_transactions_found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <td colspan="5" class="text-end fw-bold">{{ __('messages.current_balance') }}</td>
                                <td class="text-end fw-bold">{{ number_format($currentBalance, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {

            .navbar,
            .sidebar,
            .footer,
            .d-print-none {
                display: none !important;
            }

            #wrapper #content-wrapper {
                margin-left: 0 !important;
                padding: 0 !important;
            }

            .container-fluid {
                padding: 0 !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
@endsection
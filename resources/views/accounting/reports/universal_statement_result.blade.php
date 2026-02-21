@extends('layouts.app')

@section('title', __('messages.universal_statement_report'))

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.universal_statement_report') }}</h1>
        <div>
            <button onclick="window.print()" class="btn btn-secondary">
                <i class="fas fa-print me-1"></i> {{ __('messages.print') }}
            </button>
            <a href="{{ route('accounting.gl.reports.universal-statement') }}" class="btn btn-outline-primary ms-2">
                <i class="fas fa-filter me-1"></i> {{ __('messages.change_filters') }}
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white border-bottom">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-1">{{ $entityName }}</h5>
                    <p class="text-muted mb-0">
                        {{ __('messages.date_range') }}: {{ $startDate }} - {{ $endDate }}
                    </p>
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
                    @php 
                        $isStock = in_array($type, ['product', 'warehouse', 'category', 'stock_supply', 'stock_receiving', 'stock_transfer', 'transfer_request', 'issue_order', 'composite_assembly']);
                        $hasOpeningBalance = !in_array($type, ['stock_supply', 'stock_receiving', 'stock_transfer', 'transfer_request', 'issue_order', 'composite_assembly', 'production_order']);
                    @endphp
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.reference') }}</th>
                            <th>{{ __('messages.description') }}</th>
                            @if($isStock)
                                <th class="text-end">{{ __('messages.stock_in') }}</th>
                                <th class="text-end">{{ __('messages.stock_out') }}</th>
                                <th class="text-end">{{ __('messages.running_balance') }}</th>
                            @else
                                <th class="text-end">{{ __('messages.debit') }}</th>
                                <th class="text-end">{{ __('messages.credit') }}</th>
                                <th class="text-end">{{ __('messages.running_balance') }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php $currentBalance = $openingBalance; @endphp
                        @if($hasOpeningBalance)
                        <tr>
                            <td colspan="5" class="text-end fw-bold">{{ __('messages.opening_balance') }}</td>
                            <td class="text-end fw-bold">{{ number_format($openingBalance, $isStock ? 3 : 2) }}</td>
                        </tr>
                        @endif
                        @forelse($results as $item)
                            @if($isStock)
                                @php 
                                    $in = $item->movement_type === 'in' ? $item->quantity : 0;
                                    $out = $item->movement_type === 'out' ? $item->quantity : 0;
                                    $currentBalance += ($in - $out);
                                @endphp
                                <tr>
                                    <td>{{ $item->transaction_date->format('Y-m-d') }}</td>
                                    <td><code>{{ $item->reference_number }}</code></td>
                                    <td>{{ $item->notes }}</td>
                                    <td class="text-end text-success">{{ $in > 0 ? number_format($in, 3) : '-' }}</td>
                                    <td class="text-end text-danger">{{ $out > 0 ? number_format($out, 3) : '-' }}</td>
                                    <td class="text-end fw-bold">{{ number_format($currentBalance, 3) }}</td>
                                </tr>
                            @else
                                @php 
                                    $debit = $item->debit;
                                    $credit = $item->credit;
                                    $balanceFactor = ($type === 'vendor') ? ($credit - $debit) : ($debit - $credit);
                                    $currentBalance += $balanceFactor;
                                @endphp
                                <tr>
                                    <td>{{ $item->transaction_date->format('Y-m-d') }}</td>
                                    <td><code>{{ $item->reference_number }}</code></td>
                                    <td>{{ $item->description }}</td>
                                    <td class="text-end text-success">{{ $debit > 0 ? number_format($debit, 2) : '-' }}</td>
                                    <td class="text-end text-danger">{{ $credit > 0 ? number_format($credit, 2) : '-' }}</td>
                                    <td class="text-end fw-bold">{{ number_format($currentBalance, 2) }}</td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    {{ __('messages.no_data_found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <td colspan="5" class="text-end fw-bold">{{ __('messages.current_balance') }}</td>
                            <td class="text-end fw-bold">{{ number_format($currentBalance, $isStock ? 3 : 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .navbar, .sidebar, .footer, .d-print-none { display: none !important; }
        #wrapper #content-wrapper { margin-left: 0 !important; padding: 0 !important; }
        .container-fluid { padding: 0 !important; }
        .card { border: none !important; box-shadow: none !important; }
    }
</style>
@endsection

@extends('layouts.app')

@section('title', 'Trial Balance')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Trial Balance</h1>
        <form action="{{ route('accounting.gl.reports.trial-balance') }}" method="GET" class="d-flex align-items-center">
            <input type="date" name="date" class="form-control me-2" value="{{ $date }}">
            <button type="submit" class="btn btn-primary">Refresh</button>
        </form>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light text-center">
                        <tr>
                            <th rowspan="2" class="align-middle">Account</th>
                            <th colspan="2">Closing Balance</th>
                        </tr>
                        <tr>
                            <th width="200">Debit</th>
                            <th width="200">Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalDebit = 0; $totalCredit = 0; @endphp
                        @foreach($accounts as $account)
                            @php 
                                $totalDebit += $account->total_debit;
                                $totalCredit += $account->total_credit;
                            @endphp
                            <tr>
                                <td>{{ $account->code }} - {{ $account->name_en }}</td>
                                <td class="text-end">{{ $account->total_debit > 0 ? number_format($account->total_debit, 2) : '-' }}</td>
                                <td class="text-end">{{ $account->total_credit > 0 ? number_format($account->total_credit, 2) : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-dark fst-italic font-weight-bold">
                        <tr>
                            <td class="text-end">TOTALS</td>
                            <td class="text-end">{{ number_format($totalDebit, 2) }}</td>
                            <td class="text-end">{{ number_format($totalCredit, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @if(round($totalDebit, 2) != round($totalCredit, 2))
                <div class="alert alert-danger mt-3">
                    <i class="fas fa-exclamation-triangle me-1"></i> 
                    <strong>Warning:</strong> Trial Balance is not equal! Difference: {{ number_format(abs($totalDebit - $totalCredit), 2) }}
                </div>
            @else
                <div class="alert alert-success mt-3">
                    <i class="fas fa-check-circle me-1"></i> 
                    Trial Balance is balanced.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

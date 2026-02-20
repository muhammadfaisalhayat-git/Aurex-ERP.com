@extends('layouts.app')

@section('title', 'Profit & Loss Statement')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Profit & Loss Statement</h1>
        <form action="{{ route('accounting.gl.reports.profit-loss') }}" method="GET" class="d-flex align-items-center">
            <input type="date" name="start_date" class="form-control me-2" value="{{ $startDate }}">
            <input type="date" name="end_date" class="form-control me-2" value="{{ $endDate }}">
            <button type="submit" class="btn btn-primary">Refresh</button>
        </form>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body p-5">
            <div class="text-center mb-5">
                <h2 class="font-weight-bold">Income Statement</h2>
                <p class="text-muted">For the period from {{ $startDate }} to {{ $endDate }}</p>
            </div>

            <div class="row">
                <div class="col-12">
                    <h5 class="text-primary font-weight-bold border-bottom pb-2 mb-3">REVENUE</h5>
                    <table class="table table-borderless">
                        @php $totalRevenue = 0; @endphp
                        @foreach($revenues as $revenue)
                            @if($revenue->balance != 0)
                                @php $totalRevenue += $revenue->balance; @endphp
                                <tr>
                                    <td>{{ $revenue->name_en }}</td>
                                    <td class="text-end">{{ number_format($revenue->balance, 2) }}</td>
                                </tr>
                            @endif
                        @endforeach
                        <tr class="fw-bold border-top">
                            <td>TOTAL REVENUE</td>
                            <td class="text-end border-double-bottom">{{ number_format($totalRevenue, 2) }}</td>
                        </tr>
                    </table>

                    <h5 class="text-danger font-weight-bold border-bottom pb-2 mt-5 mb-3">EXPENSES</h5>
                    <table class="table table-borderless">
                        @php $totalExpense = 0; @endphp
                        @foreach($expenses as $expense)
                            @if($expense->balance != 0)
                                @php $totalExpense += $expense->balance; @endphp
                                <tr>
                                    <td>{{ $expense->name_en }}</td>
                                    <td class="text-end">({{ number_format($expense->balance, 2) }})</td>
                                </tr>
                            @endif
                        @endforeach
                        <tr class="fw-bold border-top">
                            <td>TOTAL EXPENSES</td>
                            <td class="text-end">({{ number_format($totalExpense, 2) }})</td>
                        </tr>
                    </table>

                    <div class="mt-5 p-4 bg-light rounded d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 font-weight-bold">NET PROFIT / (LOSS)</h4>
                        <h4 class="mb-0 font-weight-bold text-{{ ($totalRevenue - $totalExpense) >= 0 ? 'success' : 'danger' }}">
                            {{ number_format($totalRevenue - $totalExpense, 2) }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-double-bottom {
        border-bottom: 3px double #000;
    }
</style>
@endsection

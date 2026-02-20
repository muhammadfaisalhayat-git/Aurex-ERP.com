@extends('layouts.app')

@section('title', 'Balance Sheet')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Balance Sheet</h1>
        <form action="{{ route('accounting.gl.reports.balance-sheet') }}" method="GET" class="d-flex align-items-center">
            <input type="date" name="date" class="form-control me-2" value="{{ $date }}">
            <button type="submit" class="btn btn-primary">Refresh</button>
        </form>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body p-5">
            <div class="text-center mb-5">
                <h2 class="font-weight-bold">Statement of Financial Position</h2>
                <p class="text-muted">As of {{ $date }}</p>
            </div>

            <div class="row">
                <div class="col-md-6 border-end">
                    <h5 class="text-primary font-weight-bold border-bottom pb-2 mb-3">ASSETS</h5>
                    <table class="table table-borderless">
                        @php $totalAssets = 0; @endphp
                        @foreach($assets as $asset)
                            @if($asset->balance != 0)
                                @php $totalAssets += $asset->balance; @endphp
                                <tr>
                                    <td>{{ $asset->name_en }}</td>
                                    <td class="text-end">{{ number_format($asset->balance, 2) }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </table>
                    <div class="d-flex justify-content-between fw-bold border-top pt-2">
                        <span>TOTAL ASSETS</span>
                        <span class="border-double-bottom">{{ number_format($totalAssets, 2) }}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <h5 class="text-danger font-weight-bold border-bottom pb-2 mb-3">LIABILITIES</h5>
                    <table class="table table-borderless">
                        @php $totalLiabilities = 0; @endphp
                        @foreach($liabilities as $liability)
                            @if($liability->balance != 0)
                                @php $totalLiabilities += $liability->balance; @endphp
                                <tr>
                                    <td>{{ $liability->name_en }}</td>
                                    <td class="text-end">{{ number_format($liability->balance, 2) }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </table>
                    <div class="d-flex justify-content-between fw-bold border-top pt-2 mb-4">
                        <span>TOTAL LIABILITIES</span>
                        <span>{{ number_format($totalLiabilities, 2) }}</span>
                    </div>

                    <h5 class="text-info font-weight-bold border-bottom pb-2 mb-3">EQUITY</h5>
                    <table class="table table-borderless">
                        @php $totalEquity = 0; @endphp
                        @foreach($equity as $e)
                            @if($e->balance != 0)
                                @php $totalEquity += $e->balance; @endphp
                                <tr>
                                    <td>{{ $e->name_en }}</td>
                                    <td class="text-end">{{ number_format($e->balance, 2) }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </table>
                    <div class="d-flex justify-content-between fw-bold border-top pt-2">
                        <span>TOTAL EQUITY</span>
                        <span>{{ number_format($totalEquity, 2) }}</span>
                    </div>

                    <div class="mt-5 p-3 bg-dark text-white rounded d-flex justify-content-between align-items-center">
                        <span>TOTAL LIABILITIES & EQUITY</span>
                        <span class="h5 mb-0 font-weight-bold">{{ number_format($totalLiabilities + $totalEquity, 2) }}</span>
                    </div>
                </div>
            </div>

            @if(round($totalAssets, 2) != round($totalLiabilities + $totalEquity, 2))
                <div class="alert alert-warning mt-5 mb-0">
                    <i class="fas fa-exclamation-triangle me-1"></i> Balance Sheet is not equal by {{ number_format(abs($totalAssets - ($totalLiabilities + $totalEquity)), 2) }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .border-double-bottom {
        border-bottom: 3px double #000;
    }
</style>
@endsection

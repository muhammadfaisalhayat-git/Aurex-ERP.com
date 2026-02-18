@extends('layouts.app')

@section('title', __('messages.journal_voucher_details'))

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('messages.jv_number') }}: {{ $jv->voucher_number }}</h1>
            <div class="btn-group">
                <a href="{{ route('accounting.gl.transactions.jv.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('messages.back') }}
                </a>
                @if($jv->status == 'draft')
                    @can('post journal vouchers')
                        <form action="{{ route('accounting.gl.transactions.jv.post', $jv->id) }}" method="POST"
                            class="d-inline ms-2">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check-circle me-1"></i> {{ __('messages.post') }}
                            </button>
                        </form>
                    @endcan
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.basic_information') }}</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>{{ __('messages.jv_date') }}:</strong> {{ $jv->voucher_date->format('Y-m-d') }}</p>
                        <p><strong>{{ __('messages.status') }}:</strong>
                            <span class="badge {{ $jv->status == 'posted' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ __('messages.' . $jv->status) }}
                            </span>
                        </p>
                        <p><strong>{{ __('messages.created_by') }}:</strong> {{ $jv->creator->name }}</p>
                        <p><strong>{{ __('messages.description') }}:</strong> {{ $jv->description }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.line_items') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('messages.account') }}</th>
                                        <th width="150" class="text-end">{{ __('messages.debit') }}</th>
                                        <th width="150" class="text-end">{{ __('messages.credit') }}</th>
                                        <th>{{ __('messages.notes') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalDebit = 0;
                                    $totalCredit = 0; @endphp
                                    @foreach($jv->items as $item)
                                        <tr>
                                            <td>{{ $item->account->code }} -
                                                {{ $isRtl ? ($item->account->name_ar ?? $item->account->name_en) : ($item->account->name_en ?? $item->account->name_ar) }}
                                            </td>
                                            <td class="text-end">{{ number_format($item->debit, 2) }}</td>
                                            <td class="text-end">{{ number_format($item->credit, 2) }}</td>
                                            <td>{{ $item->notes }}</td>
                                        </tr>
                                        @php $totalDebit += $item->debit;
                                        $totalCredit += $item->credit; @endphp
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light fw-bold">
                                    <tr>
                                        <td class="text-end">{{ __('messages.total') }}</td>
                                        <td class="text-end">{{ number_format($totalDebit, 2) }}</td>
                                        <td class="text-end">{{ number_format($totalCredit, 2) }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
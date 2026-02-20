@extends('layouts.app')

@section('title', __('messages.receipt_vouchers') . ' - ' . __('messages.finance_banking'))

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.receipt_vouchers') }}</h1>
        <a href="{{ route('finance.vouchers.receipt.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> {{ __('messages.new_receipt_voucher') }}
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('messages.number') ?? __('messages.code') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.payer') ?? __('messages.payee') }}</th>
                            <th>{{ __('messages.bank_cash') }}</th>
                            <th>{{ __('messages.amount') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vouchers as $voucher)
                            <tr>
                                <td><code>{{ $voucher->voucher_number }}</code></td>
                                <td>{{ $voucher->voucher_date->format('Y-m-d') }}</td>
                                <td>
                                    {{ $voucher->payer_name }}
                                    @if($voucher->beneficiary)
                                        <br><small class="text-muted"><i class="fas fa-link me-1"></i> {{ $voucher->beneficiary->name ?? $voucher->beneficiary->name_en ?? $voucher->beneficiary->full_name }} ({{ __('messages.' . strtolower(class_basename($voucher->beneficiary_type))) ?? class_basename($voucher->beneficiary_type) }})</small>
                                    @endif
                                </td>
                                <td>{{ App::getLocale() == 'ar' ? ($voucher->bankAccount->name_ar ?? $voucher->bankAccount->name_en) : $voucher->bankAccount->name_en }}</td>
                                <td class="text-end font-weight-bold">{{ number_format($voucher->amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $voucher->status == 'posted' ? 'success' : ($voucher->status == 'voided' ? 'danger' : 'secondary') }}">
                                        {{ __('messages.' . $voucher->status) ?? ucfirst($voucher->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('finance.vouchers.receipt.show', $voucher->id) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($voucher->status == 'draft')
                                            <form action="{{ route('finance.vouchers.receipt.post', $voucher->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-success" title="{{ __('messages.post_to_ledger') }}">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">{{ __('messages.no_vouchers_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $vouchers->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

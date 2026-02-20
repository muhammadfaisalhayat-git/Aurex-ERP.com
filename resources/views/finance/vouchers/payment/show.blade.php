@extends('layouts.app')

@section('title', 'Payment Voucher Details')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Payment Voucher: {{ $paymentVoucher->voucher_number }}</h1>
        <div>
            @if($paymentVoucher->status == 'draft')
                <form action="{{ route('finance.vouchers.payment.post', $paymentVoucher->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success"><i class="fas fa-check-circle me-1"></i> Post to Ledger</button>
                </form>
            @endif
            <a href="javascript:window.print()" class="btn btn-outline-secondary"><i class="fas fa-print me-1"></i> Print</a>
            <a href="{{ route('finance.vouchers.payment.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Back</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-body p-5">
                    <div class="row mb-5">
                        <div class="col-6">
                            <h2 class="text-primary font-weight-bold">PAYMENT VOUCHER</h2>
                        </div>
                        <div class="col-6 text-end">
                            <h4 class="text-gray-800">{{ $paymentVoucher->voucher_number }}</h4>
                            <p class="mb-0 text-muted">Date: {{ $paymentVoucher->voucher_date->format('d M, Y') }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="p-3 bg-light rounded">
                                <span class="text-muted d-block small mb-1">Paid To:</span>
                                <h4 class="mb-0">{{ $paymentVoucher->payee_name }}</h4>
                                @if($paymentVoucher->beneficiary)
                                    <span class="badge bg-info text-white me-1">{{ class_basename($paymentVoucher->beneficiary_type) }}</span>
                                    <small class="text-primary">
                                        {{ $paymentVoucher->beneficiary->name ?? $paymentVoucher->beneficiary->name_en ?? $paymentVoucher->beneficiary->full_name }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table border-bottom">
                            <thead>
                                <tr class="bg-primary text-white">
                                    <th>Description</th>
                                    <th>Account</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $paymentVoucher->description ?? 'Financial Payment' }}</td>
                                    <td>{{ $paymentVoucher->chartOfAccount->name_en }} ({{ $paymentVoucher->chartOfAccount->code }})</td>
                                    <td class="text-end font-weight-bold">{{ number_format($paymentVoucher->amount, 2) }} {{ $paymentVoucher->bankAccount->currency_code }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-end">Total Payment:</th>
                                    <th class="text-end h4 font-weight-bold">{{ number_format($paymentVoucher->amount, 2) }} {{ $paymentVoucher->bankAccount->currency_code }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="row mt-5">
                        <div class="col-6">
                            <p class="text-muted small mb-0">Prepared By:</p>
                            <div class="border-bottom w-50 mt-4"></div>
                            <small>{{ $paymentVoucher->creator->name ?? 'System' }}</small>
                        </div>
                        <div class="col-6 text-end">
                            <p class="text-muted small mb-0">Authorized By:</p>
                            <div class="border-bottom w-50 ms-auto mt-4"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Voucher Info</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Status:</span>
                            <span class="badge bg-{{ $paymentVoucher->status == 'posted' ? 'success' : 'secondary' }}">{{ ucfirst($paymentVoucher->status) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Source Account:</span>
                            <span class="font-weight-bold">{{ $paymentVoucher->bankAccount->name_en }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Payment Mode:</span>
                            <span>{{ str_replace('_', ' ', ucfirst($paymentVoucher->payment_method)) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Reference:</span>
                            <span>{{ $paymentVoucher->reference_number ?: 'N/A' }}</span>
                        </li>
                        @if($paymentVoucher->posted_at)
                            <li class="list-group-item">
                                <small class="text-muted d-block">Posted At:</small>
                                <span>{{ $paymentVoucher->posted_at->format('Y-m-d H:i') }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

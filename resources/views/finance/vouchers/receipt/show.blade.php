@extends('layouts.app')

@section('title', 'Receipt Voucher Details')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Receipt Voucher: {{ $receiptVoucher->voucher_number }}</h1>
        <div>
            @if($receiptVoucher->status == 'draft')
                <form action="{{ route('finance.vouchers.receipt.post', $receiptVoucher->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success"><i class="fas fa-check-circle me-1"></i> Post to Ledger</button>
                </form>
            @endif
            <a href="javascript:window.print()" class="btn btn-outline-secondary"><i class="fas fa-print me-1"></i> Print</a>
            <a href="{{ route('finance.vouchers.receipt.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Back</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-body p-5">
                    <div class="row mb-5">
                        <div class="col-6">
                            <h2 class="text-success font-weight-bold">RECEIPT VOUCHER</h2>
                        </div>
                        <div class="col-6 text-end">
                            <h4 class="text-gray-800">{{ $receiptVoucher->voucher_number }}</h4>
                            <p class="mb-0 text-muted">Date: {{ $receiptVoucher->voucher_date->format('d M, Y') }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="p-3 bg-light rounded">
                                <span class="text-muted d-block small mb-1">Received From:</span>
                                <h4 class="mb-0">{{ $receiptVoucher->payer_name }}</h4>
                                @if($receiptVoucher->beneficiary)
                                    <span class="badge bg-info text-white me-1">{{ class_basename($receiptVoucher->beneficiary_type) }}</span>
                                    <small class="text-success">
                                        {{ $receiptVoucher->beneficiary->name ?? $receiptVoucher->beneficiary->name_en ?? $receiptVoucher->beneficiary->full_name }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table border-bottom">
                            <thead>
                                <tr class="bg-success text-white">
                                    <th>Description</th>
                                    <th>Account</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $receiptVoucher->description ?? 'Financial Receipt' }}</td>
                                    <td>{{ $receiptVoucher->chartOfAccount->name_en }} ({{ $receiptVoucher->chartOfAccount->code }})</td>
                                    <td class="text-end font-weight-bold">{{ number_format($receiptVoucher->amount, 2) }} {{ $receiptVoucher->bankAccount->currency_code }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-end">Total Received:</th>
                                    <th class="text-end h4 font-weight-bold">{{ number_format($receiptVoucher->amount, 2) }} {{ $receiptVoucher->bankAccount->currency_code }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="row mt-5">
                        <div class="col-6">
                            <p class="text-muted small mb-0">Prepared By:</p>
                            <div class="border-bottom w-50 mt-4"></div>
                            <small>{{ $receiptVoucher->creator->name ?? 'System' }}</small>
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
                    <h6 class="m-0 font-weight-bold text-success">Voucher Info</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Status:</span>
                            <span class="badge bg-{{ $receiptVoucher->status == 'posted' ? 'success' : 'secondary' }}">{{ ucfirst($receiptVoucher->status) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Target Account:</span>
                            <span class="font-weight-bold">{{ $receiptVoucher->bankAccount->name_en }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Payment Mode:</span>
                            <span>{{ str_replace('_', ' ', ucfirst($receiptVoucher->payment_method)) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Reference:</span>
                            <span>{{ $receiptVoucher->reference_number ?: 'N/A' }}</span>
                        </li>
                        @if($receiptVoucher->posted_at)
                            <li class="list-group-item">
                                <small class="text-muted d-block">Posted At:</small>
                                <span>{{ $receiptVoucher->posted_at->format('Y-m-d H:i') }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Maintenance Vouchers')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Maintenance Vouchers</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Maintenance</li>
                <li class="breadcrumb-item active" aria-current="page">Vouchers</li>
            </ol>
        </nav>
    </div>
    <div class="page-actions">
        <a href="{{ route('maintenance.vouchers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Voucher
        </a>
    </div>
</div>


    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Workshop Orders</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Voucher #</th>
                            <th>Date</th>
                            <th>Asset / Entity</th>
                            <th>Workshop</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Est. Cost</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($vouchers as $voucher)
                            <tr>
                                <td><a href="{{ route('maintenance.vouchers.show', $voucher) }}" class="fw-bold text-decoration-none">{{ $voucher->voucher_number }}</a></td>
                                <td>{{ $voucher->voucher_date->format('Y-m-d') }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $voucher->entity_name }}</div>
                                    <div class="text-muted small">{{ ucfirst($voucher->entity_type) }}</div>
                                </td>
                                <td>{{ $voucher->workshop->name ?? 'N/A' }}</td>
                                <td><span class="badge bg-light text-dark border">{{ ucfirst($voucher->maintenance_type) }}</span></td>
                                <td>
                                    @php
                                        $statusClasses = [
                                            'draft' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                                            'in_progress' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                            'completed' => 'bg-success-subtle text-success border border-success-subtle',
                                            'cancelled' => 'bg-danger-subtle text-danger border border-danger-subtle'
                                        ];
                                    @endphp
                                    <span class="badge {{ $statusClasses[$voucher->status] ?? 'bg-secondary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $voucher->status)) }}
                                    </span>
                                </td>
                                <td>{{ number_format($voucher->estimated_cost, 2) }}</td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('maintenance.vouchers.show', $voucher) }}" class="btn btn-sm btn-outline-primary" title="Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($voucher->status === 'draft')
                                            <a href="{{ route('maintenance.vouchers.edit', $voucher) }}" class="btn btn-sm btn-outline-info" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-tools fa-3x mb-3"></i>
                                        <p class="mb-0">No maintenance vouchers found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($vouchers->hasPages())
            <div class="card-footer">
                {{ $vouchers->links() }}
            </div>
        @endif
    </div>

@endsection

@extends('layouts.app')

@section('title', __('messages.journal_vouchers'))

@section('content')
    <div class="container-fluid px-0">
        <!-- Toolbar-style Header -->
        <div class="toolbar-bg p-3 mb-4 border-bottom d-flex justify-content-between align-items-center shadow-sm">
            <div>
                <h1 class="h4 mb-0 text-gray-800 fw-bold uppercase tracking-wider letter-spacing-1">
                    <i class="fas fa-book-open me-2 text-primary"></i> {{ __('messages.journal_vouchers') }}
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 x-small">
                        <li class="breadcrumb-item"><a
                                href="{{ route('accounting.gl.coa.index') }}">{{ __('messages.accounting') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('messages.journal_vouchers') }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                @can('create journal vouchers')
                    <a href="{{ route('accounting.gl.transactions.jv.create') }}" class="btn btn-primary px-4 shadow-sm">
                        <i class="fas fa-plus me-2"></i> {{ __('messages.create_new') }}
                    </a>
                @endcan
                <button class="btn btn-tool" title="Refresh" onclick="location.reload()">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>

        <div class="px-4">
            <!-- Search & Filter Card -->
            <div class="card border-0 shadow-sm mb-4 bg-light-panel">
                <div class="card-body p-3">
                    <form action="{{ route('accounting.gl.transactions.jv.index') }}" method="GET" class="row g-2">
                        <div class="col-md-4">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control border-start-0 shadow-none"
                                    placeholder="{{ __('messages.search_vouchers') }}..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select form-select-sm shadow-none">
                                <option value="">{{ __('messages.all_statuses') }}</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>
                                    {{ __('messages.draft') }}
                                </option>
                                <option value="posted" {{ request('status') == 'posted' ? 'selected' : '' }}>
                                    {{ __('messages.posted') }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="from_date" class="form-control form-control-sm shadow-none"
                                value="{{ request('from_date') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="to_date" class="form-control form-control-sm shadow-none"
                                value="{{ request('to_date') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-sm btn-secondary w-100 shadow-none">
                                {{ __('messages.filter') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- List Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 premium-table">
                            <thead class="bg-gray-100 text-gray-700 x-small uppercase fw-bold tracking-tighter">
                                <tr>
                                    <th class="ps-4" style="width: 150px;">{{ __('messages.jv_number') }}</th>
                                    <th style="width: 120px;">{{ __('messages.jv_date') }}</th>
                                    <th>{{ __('messages.description') }}</th>
                                    <th class="text-center" style="width: 100px;">{{ __('messages.status') }}</th>
                                    <th class="text-end" style="width: 150px;">{{ __('messages.total_amount') }}</th>
                                    <th style="width: 150px;">{{ __('messages.created_by') }}</th>
                                    <th class="pe-4 text-center" style="width: 120px;">{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vouchers as $jv)
                                    <tr>
                                        <td class="ps-4">
                                            <a href="{{ route('accounting.gl.transactions.jv.show', $jv->id) }}"
                                                class="fw-bold text-primary text-decoration-none">
                                                <code>{{ $jv->voucher_number }}</code>
                                            </a>
                                        </td>
                                        <td><span class="text-muted small">{{ $jv->voucher_date->format('d/m/Y') }}</span></td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span
                                                    class="small fw-semibold text-gray-800">{{ Str::limit($jv->description, 60) }}</span>
                                                @if($jv->beneficiary_name)
                                                    <span class="x-small text-muted italic"><i class="fas fa-share me-1"></i>
                                                        {{ $jv->beneficiary_name }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $statusClass = match ($jv->status) {
                                                    'draft' => 'status-draft',
                                                    'posted' => 'status-posted',
                                                    'reversed' => 'status-reversed',
                                                    default => 'status-default'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }} rounded-pill px-3 py-1 x-small fw-bold">
                                                {{ __('messages.' . $jv->status) }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex flex-column align-items-end">
                                                <span class="small fw-bold text-danger" title="{{ __('messages.debit') }}">
                                                    {{ number_format($jv->items_sum_debit, 2) }}
                                                </span>
                                                <span class="small fw-bold text-success" title="{{ __('messages.credit') }}">
                                                    {{ number_format($jv->items_sum_credit, 2) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="avatar-xs bg-soft-blue text-primary rounded-circle me-2 d-flex align-items-center justify-content-center fw-bold x-small">
                                                    {{ strtoupper(substr($jv->creator->name, 0, 1)) }}
                                                </div>
                                                <span class="small text-muted">{{ $jv->creator->name }}</span>
                                            </div>
                                        </td>
                                        <td class="pe-4 text-center">
                                            <div class="btn-group btn-group-xs dropdown">
                                                <a href="{{ route('accounting.gl.transactions.jv.show', $jv->id) }}"
                                                    class="btn btn-outline-primary" title="{{ __('messages.view') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button"
                                                    class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <span class="visually-hidden">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 x-small">
                                                    <li><a class="dropdown-item"
                                                            href="{{ route('accounting.gl.transactions.jv.edit', $jv->id) }}"><i
                                                                class="fas fa-edit me-2 text-warning"></i>
                                                            {{ __('messages.edit') }}</a></li>
                                                    <li><a class="dropdown-item"
                                                            href="{{ route('accounting.gl.transactions.jv.print', $jv->id) }}"
                                                            target="_blank"><i class="fas fa-print me-2 text-secondary"></i>
                                                            {{ __('messages.print') }}</a></li>
                                                    @if($jv->status === 'draft')
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <form
                                                                action="{{ route('accounting.gl.transactions.jv.post', $jv->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-success fw-bold"><i
                                                                        class="fas fa-check-circle me-2"></i>
                                                                    {{ __('messages.post') }}</button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <div class="mb-2"><i class="fas fa-inbox fa-3x opacity-20"></i></div>
                                            {{ __('messages.no_data_found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($vouchers->hasPages())
                    <div class="card-footer bg-white border-0 py-3">
                        {{ $vouchers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        :root {
            --erp-bg: #f5f7fb;
            --toolbar-bg: #ffffff;
            --border-gray: #e9ecef;
            --primary-blue: #4e73df;
        }

        body {
            background-color: var(--erp-bg);
        }

        .toolbar-bg {
            background-color: var(--toolbar-bg);
        }

        .premium-table th {
            padding: 12px 15px !important;
            border-top: none !important;
        }

        .premium-table td {
            padding: 10px 15px !important;
            border-top: 1px solid var(--border-gray);
        }

        .premium-table tr:hover {
            background-color: #f8fafc;
        }

        .status-draft {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-posted {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .status-reversed {
            background-color: #f8d7da;
            color: #842029;
        }

        .status-default {
            background-color: #e2e3e5;
            color: #383d41;
        }

        .avatar-xs {
            width: 24px;
            height: 24px;
        }

        .bg-soft-blue {
            background-color: #eef2ff;
        }

        .btn-tool {
            background: transparent;
            border: 1px solid transparent;
            color: #6e707e;
            width: 32px;
            height: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-tool:hover {
            background: #f8f9fc;
            color: var(--primary-blue);
            border-color: #eaecf4;
        }

        .x-small {
            font-size: 0.75rem;
        }

        .btn-group-xs>.btn {
            padding: 0.1rem 0.4rem;
            font-size: 0.75rem;
        }

        .letter-spacing-1 {
            letter-spacing: 1px;
        }
    </style>
@endsection
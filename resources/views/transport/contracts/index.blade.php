@extends('layouts.app')

@section('title', __('messages.transport') . ' - ' . __('messages.contracts'))

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('messages.transport_contracts') }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('messages.transport') }}</li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('messages.contracts') }}</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <a href="{{ route('transport.contracts.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>{{ __('messages.add_contract') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">{{ __('messages.contract_list') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('messages.contract_number') }}</th>
                            <th>{{ __('messages.contractor') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.value') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th class="text-end">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contracts as $contract)
                            <tr>
                                <td><span class="fw-bold">{{ $contract->contract_number }}</span></td>
                                <td>
                                    <div>{{ $contract->contractor_name }}</div>
                                    <small class="text-muted">{{ $contract->contractor_phone }}</small>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-primary">{{ $contract->contract_date->format('Y-m-d') }}
                                    </div>
                                    <div class="small text-muted">{{ $contract->start_date->format('Y-m-d') }} -
                                        {{ $contract->end_date->format('Y-m-d') }}</div>
                                </td>
                                <td>{{ number_format($contract->contract_value, 2) }}</td>
                                <td>
                                    @php
                                        $statusClasses = [
                                            'active' => 'bg-success',
                                            'pending' => 'bg-warning text-dark',
                                            'closed' => 'bg-secondary',
                                            'cancelled' => 'bg-danger'
                                        ];
                                    @endphp
                                    <span class="badge {{ $statusClasses[$contract->status] ?? 'bg-light text-dark border' }}">
                                        {{ __('messages.' . $contract->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('transport.contracts.show', $contract) }}"
                                            class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('transport.contracts.edit', $contract) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($contract->status !== 'closed')
                                            <form action="{{ route('transport.contracts.close', $contract) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-warning"
                                                    title="{{ __('messages.close_contract') }}">
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-file-contract fa-3x mb-3"></i>
                                        <p>{{ __('messages.no_contracts_found') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($contracts->hasPages())
            <div class="card-footer bg-white">
                {{ $contracts->links() }}
            </div>
        @endif
    </div>
@endsection
@extends('layouts.app')

@section('title', __('messages.quotations'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.quotations') }}</h1>
            @can('create quotations')
                <a href="{{ route('sales.quotations.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('messages.create') }}
                </a>
            @endcan
        </div>

        @if(!session('active_branch_id'))
            <div class="card shadow-sm border-0 glassy mb-4">
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-code-branch fa-4x text-primary opacity-50"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-2">Please Select a Branch</h3>
                    <p class="text-muted mb-0">You must select a branch from the top menu to view or create quotations.</p>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.quotation_number') }}</th>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.customer') }}</th>
                                    <th>{{ __('messages.total') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.created_by') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($quotations as $quotation)
                                    <tr>
                                        <td>
                                            <a href="{{ route('sales.quotations.show', $quotation) }}"
                                                class="fw-bold text-decoration-none">
                                                {{ $quotation->document_number }}
                                            </a>
                                            @if($quotation->version > 1)
                                                <span class="badge bg-secondary">v{{ $quotation->version }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $quotation->quotation_date->format('Y-m-d') }}
                                            @if($quotation->expiry_date)
                                                <br>
                                                <small class="text-muted">{{ __('messages.expiry_date') }}:
                                                    {{ $quotation->expiry_date->format('Y-m-d') }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $quotation->customer->name ?? '-' }}</td>
                                        <td class="fw-bold">{{ number_format($quotation->total_amount, 2) }}</td>
                                        <td>
                                            @php
                                                $statusClass = [
                                                    'draft' => 'secondary',
                                                    'sent' => 'info',
                                                    'accepted' => 'success',
                                                    'rejected' => 'danger',
                                                    'converted' => 'primary',
                                                    'expired' => 'warning',
                                                ][$quotation->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge rounded-pill bg-{{ $statusClass }}">
                                                {{ __('messages.' . $quotation->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $quotation->creator->name ?? '-' }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('sales.quotations.show', $quotation) }}"
                                                    class="btn btn-sm btn-info" title="{{ __('messages.view') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @can('edit quotations')
                                                    <a href="{{ route('sales.quotations.edit', $quotation) }}"
                                                        class="btn btn-sm btn-primary" title="{{ __('messages.edit') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                <a href="{{ route('sales.quotations.print', $quotation) }}" target="_blank"
                                                    class="btn btn-sm btn-secondary" title="{{ __('messages.print') }}">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                <a href="{{ route('sales.quotations.pdf', $quotation) }}"
                                                    class="btn btn-sm btn-outline-danger" title="{{ __('messages.download_pdf') }}">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">{{ __('messages.no_records_found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $quotations->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
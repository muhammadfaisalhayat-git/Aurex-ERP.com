@extends('layouts.app')

@section('title', __('messages.journal_vouchers'))

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('messages.journal_vouchers') }}</h1>
            @can('create journal vouchers')
                <a href="{{ route('accounting.gl.transactions.jv.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> {{ __('messages.create') }}
                </a>
            @endcan
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('messages.jv_number') }}</th>
                                <th>{{ __('messages.jv_date') }}</th>
                                <th>{{ __('messages.description') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.created_by') }}</th>
                                <th width="150">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vouchers as $jv)
                                <tr>
                                    <td><code>{{ $jv->voucher_number }}</code></td>
                                    <td>{{ $jv->voucher_date->format('Y-m-d') }}</td>
                                    <td>{{ Str::limit($jv->description, 50) }}</td>
                                    <td>
                                        @php
                                            $statusClass = match ($jv->status) {
                                                'draft' => 'bg-warning text-dark',
                                                'posted' => 'bg-success',
                                                'reversed' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }}">
                                            {{ __('messages.' . $jv->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $jv->creator->name }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('accounting.gl.transactions.jv.show', $jv->id) }}"
                                                class="btn btn-outline-info" title="{{ __('messages.view') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('accounting.gl.transactions.jv.edit', $jv->id) }}"
                                                class="btn btn-outline-primary" title="{{ __('messages.edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('accounting.gl.transactions.jv.print', $jv->id) }}"
                                                target="_blank" class="btn btn-outline-secondary"
                                                title="{{ __('messages.print') }}">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        {{ __('messages.no_data_found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $vouchers->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
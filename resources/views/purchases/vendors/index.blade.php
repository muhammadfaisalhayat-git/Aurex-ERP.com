@extends('layouts.app')

@section('title', __('messages.vendors'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.vendors') }}</h1>
            @can('create vendors')
                <a href="{{ route('purchases.vendors.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('messages.create') }}
                </a>
            @endcan
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('messages.code') }}</th>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.email') }}</th>
                                <th>{{ __('messages.phone') }}</th>
                                <th>{{ __('messages.balance') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vendors as $vendor)
                                <tr>
                                    <td>
                                        <a href="{{ route('purchases.vendors.show', $vendor) }}">
                                            {{ $vendor->code }}
                                        </a>
                                    </td>
                                    <td>{{ $vendor->name }}</td>
                                    <td>{{ $vendor->email }}</td>
                                    <td>{{ $vendor->phone }}</td>
                                    <td>{{ number_format($vendor->current_balance, 2) }}</td>
                                    <td>
                                        @php
                                            $statusClass = $vendor->status === 'active' ? 'success' : 'danger';
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">
                                            {{ __('messages.' . $vendor->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('purchases.vendors.show', $vendor) }}" class="btn btn-sm btn-info"
                                                title="{{ __('messages.view') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @can('edit vendors')
                                                <a href="{{ route('purchases.vendors.edit', $vendor) }}"
                                                    class="btn btn-sm btn-primary" title="{{ __('messages.edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                            <a href="{{ route('purchases.vendors.statement', $vendor) }}"
                                                class="btn btn-sm btn-secondary" title="{{ __('messages.view_statement') }}">
                                                <i class="fas fa-file-invoice-dollar"></i>
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
                    {{ $vendors->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
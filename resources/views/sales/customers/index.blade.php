@extends('layouts.app')

@section('title', __('messages.customers'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.customers') }}</h1>
            @can('create customers')
                <a href="{{ route('sales.customers.create') }}" class="btn btn-primary">
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
                            @forelse($customers as $customer)
                                <tr>
                                    <td>
                                        <a href="{{ route('sales.customers.show', $customer) }}">
                                            {{ $customer->code }}
                                        </a>
                                    </td>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->phone }}</td>
                                    <td>{{ number_format($customer->current_balance, 2) }}</td>
                                    <td>
                                        @php
                                            $statusClass = $customer->status === 'active' ? 'success' : 'danger';
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">
                                            {{ __('messages.' . $customer->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            @can('view customers')
                                                <a href="{{ route('sales.customers.show', $customer) }}" class="btn btn-sm btn-info"
                                                    title="{{ __('messages.view') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan
                                            @can('edit customers')
                                                <a href="{{ route('sales.customers.edit', $customer) }}"
                                                    class="btn btn-sm btn-primary" title="{{ __('messages.edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                            @can('view customers')
                                                <a href="{{ route('sales.customers.statement', $customer) }}"
                                                    class="btn btn-sm btn-secondary" title="{{ __('messages.view_statement') }}">
                                                    <i class="fas fa-file-invoice-dollar"></i>
                                                </a>
                                            @endcan
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
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
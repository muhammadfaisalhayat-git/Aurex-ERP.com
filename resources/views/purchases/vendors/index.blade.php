@extends('layouts.app')

@section('title', __('messages.vendors'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.vendors') }}</h1>
            <div class="d-flex gap-2">
                <form action="{{ route('purchases.vendors.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control" placeholder="{{ __('messages.search') }}..."
                        value="{{ request('search') }}">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-search"></i>
                    </button>
                    @if (request('search'))
                        <a href="{{ route('purchases.vendors.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
                @can('create vendors')
                    <a href="{{ route('purchases.vendors.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('messages.create') }}
                    </a>
                @endcan
            </div>
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
                                            @can('delete vendors')
                                                <form action="{{ route('purchases.vendors.destroy', $vendor) }}" method="POST"
                                                    class="d-inline" onsubmit="return confirm('{{ __('messages.are_you_sure') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        title="{{ __('messages.delete') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
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
                    {{ $vendors->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
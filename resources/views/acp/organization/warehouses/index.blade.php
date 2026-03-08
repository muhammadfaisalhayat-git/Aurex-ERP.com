@extends('layouts.app')

@section('title', __('messages.warehouses'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.warehouses') }}</h1>
            @can('create warehouses')
                <a href="{{ route('acp.organization.warehouses.create') }}" class="btn btn-primary">
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
                                    <th>{{ __('messages.branch') }}</th>
                                    <th>{{ __('messages.manager') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($warehouses as $warehouse)
                                    <tr>
                                        <td>{{ $warehouse->code }}</td>
                                        <td>{{ $warehouse->name }}</td>
                                        <td>{{ $warehouse->branch->name ?? '-' }}</td>
                                        <td>{{ $warehouse->manager_name ?? '-' }}</td>
                                        <td>
                                            @php
                                                $statusClass = $warehouse->is_active ? 'success' : 'danger';
                                                $statusText = $warehouse->is_active ? 'active' : 'inactive';
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">
                                                {{ __('messages.' . $statusText) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('acp.organization.warehouses.show', $warehouse) }}"
                                                    class="btn btn-sm btn-info" title="{{ __('messages.view') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @can('edit warehouses')
                                                    <a href="{{ route('acp.organization.warehouses.edit', $warehouse) }}"
                                                        class="btn btn-sm btn-primary" title="{{ __('messages.edit') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete warehouses')
                                                    <form action="{{ route('acp.organization.warehouses.destroy', $warehouse) }}" method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
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
                                        <td colspan="6" class="text-center">{{ __('messages.no_records_found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $warehouses->links() }}
                    </div>
                </div>
            </div>
        
    </div>
@endsection
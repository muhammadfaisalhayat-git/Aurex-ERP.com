@extends('layouts.app')

@section('title', __('messages.view_warehouse'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.view_warehouse') }}: {{ $warehouse->name }}</h1>
            <a href="{{ route('acp.organization.warehouses.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">{{ $warehouse->name }}</h5>
                        <p class="text-muted">{{ $warehouse->code }}</p>
                        <div>
                            @php
                                $statusClass = $warehouse->is_active ? 'success' : 'danger';
                                $statusText = $warehouse->is_active ? 'active' : 'inactive';
                            @endphp
                            <span class="badge bg-{{ $statusClass }}">
                                {{ __('messages.' . $statusText) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">{{ __('messages.actions') }}</div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @can('edit warehouses')
                                <a href="{{ route('acp.organization.warehouses.edit', $warehouse) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> {{ __('messages.edit_warehouse') }}
                                </a>
                            @endcan

                            @can('delete warehouses')
                                <form action="{{ route('acp.organization.warehouses.destroy', $warehouse) }}" method="POST"
                                    onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="fas fa-trash"></i> {{ __('messages.delete_warehouse') }}
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">{{ __('messages.details') }}</div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold">{{ __('messages.code') }}</div>
                            <div class="col-sm-9">{{ $warehouse->code }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold">{{ __('messages.name_en') }}</div>
                            <div class="col-sm-9">{{ $warehouse->name_en }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold">{{ __('messages.name_ar') }}</div>
                            <div class="col-sm-9">{{ $warehouse->name_ar }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold">{{ __('messages.branch') }}</div>
                            <div class="col-sm-9">{{ $warehouse->branch->name ?? '-' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold">{{ __('messages.manager_name') }}</div>
                            <div class="col-sm-9">{{ $warehouse->manager_name ?? '-' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold">{{ __('messages.location') }}</div>
                            <div class="col-sm-9">{{ $warehouse->location ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
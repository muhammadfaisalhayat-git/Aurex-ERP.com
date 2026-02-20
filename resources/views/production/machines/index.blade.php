@extends('layouts.app')

@section('title', __('messages.machines') . ' - ' . __('messages.production'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('messages.machines') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('messages.production') }}</li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('messages.machines') }}</li>
            </ol>
        </nav>
    </div>
    <div class="page-actions">
        <a href="{{ route('production.machines.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>{{ __('messages.new_machine') }}
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">{{ __('messages.production_machinery') }}</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 10%">{{ __('messages.code') }}</th>
                        <th style="width: 20%">{{ __('messages.name') }}</th>
                        <th style="width: 20%">{{ __('messages.work_center') }}</th>
                        <th style="width: 15%">{{ __('messages.hourly_cost') }}</th>
                        <th style="width: 15%">{{ __('messages.status') }}</th>
                        <th style="width: 20%" class="text-end">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($machines) > 0)
                        @foreach($machines as $machine)
                            <tr>
                                <td><span class="badge bg-light text-dark border">{{ $machine->code }}</span></td>
                                <td>
                                    <div class="fw-semibold">{{ $machine->name }}</div>
                                    <div class="text-muted small">{{ $machine->brand }} {{ $machine->model }}</div>
                                </td>
                                <td>
                                    <span class="text-primary fw-medium">
                                        <i class="fas fa-building-circle-check me-1"></i>
                                        {{ $machine->workCenter->name ?? __('messages.none') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold">{{ number_format($machine->hourly_cost, 2) }}</span>
                                    <small class="text-muted">{{ __('messages.sar') ?? 'SAR' }}</small>
                                </td>
                                <td>
                                    @php
                                        $statusClasses = [
                                            'available' => 'bg-success text-white',
                                            'maintenance' => 'bg-warning text-dark',
                                            'busy' => 'bg-info text-white',
                                            'offline' => 'bg-danger text-white'
                                        ];
                                    @endphp
                                    <span class="badge {{ $statusClasses[$machine->status] ?? 'bg-secondary' }}">
                                        {{ __('messages.' . $machine->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('production.machines.edit', $machine) }}" class="btn btn-sm btn-outline-info" title="{{ __('messages.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('production.machines.destroy', $machine) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.are_you_sure') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('messages.delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-robot fa-3x mb-3"></i>
                                    <p class="mb-0">{{ __('messages.no_machines_found') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    @if($machines->hasPages())
        <div class="card-footer">
            {{ $machines->links() }}
        </div>
    @endif
</div>
@endsection

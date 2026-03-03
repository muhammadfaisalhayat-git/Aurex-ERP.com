@extends('layouts.app')

@section('title', __('messages.transport') . ' - ' . __('messages.trailers'))

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('messages.trailers') }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('messages.transport') }}</li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('messages.trailers') }}</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <a href="{{ route('transport.trailers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>{{ __('messages.add_trailer') }}
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted small text-uppercase mb-1">{{ __('messages.total_trailers') }}</h6>
                            <h4 class="mb-0">{{ $trailers->total() }}</h4>
                        </div>
                        <div class="ms-3 bg-primary-subtle p-3 rounded-circle text-primary">
                            <i class="fas fa-trailer fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title">{{ __('messages.trailer_list') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('messages.code') }}</th>
                            <th>{{ __('messages.plate_number') }}</th>
                            <th>{{ __('messages.type') }}</th>
                            <th>{{ __('messages.capacity') }}</th>
                            <th>{{ __('messages.driver') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th class="text-end">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trailers as $trailer)
                            <tr>
                                <td><span class="fw-bold">{{ $trailer->code }}</span></td>
                                <td>{{ $trailer->plate_number }}</td>
                                <td>{{ $trailer->trailer_type }}</td>
                                <td>{{ number_format($trailer->capacity_kg, 2) }} {{ __('messages.kg') }}</td>
                                <td>
                                    <div>{{ $trailer->driver_name ?? '-' }}</div>
                                    <small class="text-muted">{{ $trailer->driver_phone }}</small>
                                </td>
                                <td>
                                    @php
                                        $statusClasses = [
                                            'available' => 'bg-success',
                                            'busy' => 'bg-primary',
                                            'maintenance' => 'bg-warning text-dark',
                                            'retired' => 'bg-danger'
                                        ];
                                    @endphp
                                    <span class="badge {{ $statusClasses[$trailer->status] ?? 'bg-secondary' }}">
                                        {{ __('messages.' . $trailer->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('transport.trailers.edit', $trailer) }}"
                                            class="btn btn-sm btn-outline-primary" title="{{ __('messages.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('transport.trailers.destroy', $trailer) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_delete') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                title="{{ __('messages.delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-trailer fa-3x mb-3"></i>
                                        <p>{{ __('messages.no_trailers_found') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($trailers->hasPages())
            <div class="card-footer bg-white">
                {{ $trailers->links() }}
            </div>
        @endif
    </div>
@endsection
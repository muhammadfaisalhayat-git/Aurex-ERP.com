@extends('layouts.app')

@section('title', __('messages.work_centers') . ' - ' . __('messages.production'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('messages.work_centers') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('messages.production') }}</li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('messages.work_centers') }}</li>
            </ol>
        </nav>
    </div>
    <div class="page-actions">
        <a href="{{ route('production.work-centers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>{{ __('messages.create') }} {{ __('messages.work_center') }}
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">{{ __('messages.registered_work_centers') }}</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 15%">{{ __('messages.code') }}</th>
                        <th style="width: 30%">{{ __('messages.name') }}</th>
                        <th style="width: 15%">{{ __('messages.capacity') }}</th>
                        <th style="width: 15%">{{ __('messages.status') }}</th>
                        <th style="width: 10%">{{ __('messages.created_at') }}</th>
                        <th style="width: 15%" class="text-end">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($workCenters) > 0)
                        @foreach($workCenters as $wc)
                            <tr>
                                <td><span class="fw-bold text-primary">{{ $wc->code }}</span></td>
                                <td>
                                    <div class="fw-semibold">{{ $wc->name }}</div>
                                    <div class="text-muted small">{{ Str::limit($wc->description, 50) }}</div>
                                </td>
                                <td>{{ number_format($wc->capacity, 2) }} {{ __('messages.capacity_units_hr') }}</td>
                                <td>
                                    @if($wc->is_active)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">{{ __('messages.active') }}</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">{{ __('messages.inactive') }}</span>
                                    @endif
                                </td>
                                <td>{{ $wc->created_at->format('Y-m-d') }}</td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('production.work-centers.edit', $wc) }}" class="btn btn-sm btn-outline-info" title="{{ __('messages.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('production.work-centers.destroy', $wc) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.are_you_sure') }}')">
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
                                    <i class="fas fa-industry fa-3x mb-3"></i>
                                    <p class="mb-0">{{ __('messages.no_work_centers_found') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    @if($workCenters->hasPages())
        <div class="card-footer">
            {{ $workCenters->links() }}
        </div>
    @endif
</div>
@endsection

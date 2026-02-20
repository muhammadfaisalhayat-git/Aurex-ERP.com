@extends('layouts.app')

@section('title', 'Maintenance Workshops')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Workshops</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Maintenance</li>
                <li class="breadcrumb-item active" aria-current="page">Workshops</li>
            </ol>
        </nav>
    </div>
    <div class="page-actions">
        <a href="{{ route('maintenance.workshops.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Workshop
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Maintenance Facilities</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Contact Person</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($workshops as $workshop)
                        <tr>
                            <td><div class="fw-bold">{{ $workshop->name }}</div></td>
                            <td>{{ $workshop->location }}</td>
                            <td>{{ $workshop->contact_person }}</td>
                            <td>{{ $workshop->contact_phone }}</td>
                            <td>
                                @if($workshop->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('maintenance.workshops.edit', $workshop) }}" class="btn btn-sm btn-outline-info" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-warehouse fa-3x mb-3"></i>
                                    <p class="mb-0">No workshops registered. Please add one to start creating vouchers.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($workshops->hasPages())
        <div class="card-footer">
            {{ $workshops->links() }}
        </div>
    @endif
</div>
@endsection

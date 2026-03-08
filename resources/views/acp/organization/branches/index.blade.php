@extends('layouts.app')

@section('title', __('Branches'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('Branches') }}</h1>
        <div class="page-actions">
            @can('create branches')
                <a href="{{ route('acp.organization.branches.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> {{ __('Add Branch') }}
                </a>
            @endcan
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>{{ __('Company') }}</th>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Warehouses') }}</th>
                            <th>{{ __('Users') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th width="150">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($branches as $branch)
                            <tr>
                                <td>{{ $branch->id }}</td>
                                <td>
                                    <div class="fw-bold text-primary">{{ $branch->company->name ?? '-' }}</div>
                                </td>
                                <td><span class="badge bg-secondary">{{ $branch->code }}</span></td>
                                <td>
                                    <div class="fw-bold">{{ $branch->name_en }}</div>
                                    <div class="small text-muted">{{ $branch->name_ar }}</div>
                                </td>
                                <td>{{ $branch->warehouses_count }}</td>
                                <td>{{ $branch->users_count }}</td>
                                <td>
                                    @if($branch->is_active)
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('acp.organization.branches.show', $branch) }}"
                                            class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('edit branches')
                                            <a href="{{ route('acp.organization.branches.edit', $branch) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                        @can('delete branches')
                                            <form action="{{ route('acp.organization.branches.destroy', $branch) }}" method="POST"
                                                onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">{{ __('No branches found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($branches->hasPages())
            <div class="card-footer">
                {{ $branches->links() }}
            </div>
        @endif
    </div>
@endsection
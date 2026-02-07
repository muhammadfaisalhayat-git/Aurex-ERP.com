@extends('layouts.app')

@section('title', __('messages.view_role'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.view_role') }}: {{ $role->name }}</h1>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">{{ $role->name }}</h5>
                        <p class="mb-1"><strong>{{ __('messages.name_en') }}:</strong> {{ $role->display_name_en }}</p>
                        <p class="mb-1"><strong>{{ __('messages.name_ar') }}:</strong> {{ $role->display_name_ar }}</p>
                        <p class="mb-1"><strong>{{ __('messages.users') }}:</strong> <span
                                class="badge bg-info">{{ $role->users->count() }}</span></p>
                        <p class="mb-0"><strong>{{ __('messages.permissions') }}:</strong> <span
                                class="badge bg-secondary">{{ $role->permissions->count() }}</span></p>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">{{ __('messages.actions') }}</div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if(!$role->is_system)
                                @can('edit roles')
                                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> {{ __('messages.edit_role') }}
                                    </a>
                                @endcan

                                @can('delete roles')
                                    @if($role->users->count() == 0)
                                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST"
                                            onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger w-100">
                                                <i class="fas fa-trash"></i> {{ __('messages.delete_role') }}
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            @else
                                <div class="alert alert-info mb-0">
                                    {{ __('messages.cannot_edit_system_role') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">{{ __('messages.permissions') }}</div>
                    <div class="card-body">
                        <div class="row">
                            @php
                                $permissionsByModule = $role->permissions->groupBy('module');
                            @endphp

                            @forelse($permissionsByModule as $module => $permissions)
                                <div class="col-md-6 mb-3">
                                    <h6 class="fw-bold">{{ ucfirst($module) }}</h6>
                                    <div>
                                        @foreach($permissions as $permission)
                                            <span class="badge bg-success me-1 mb-1">{{ $permission->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center text-muted">
                                    {{ __('messages.no_permissions_assigned') }}
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">{{ __('messages.users') }}</div>
                    <div class="card-body">
                        @if($role->users->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>{{ __('messages.name') }}</th>
                                            <th>{{ __('messages.email') }}</th>
                                            <th>{{ __('messages.branch') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($role->users as $user)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a>
                                                </td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ $user->branch->name ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center text-muted m-0">{{ __('messages.no_users_assigned') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
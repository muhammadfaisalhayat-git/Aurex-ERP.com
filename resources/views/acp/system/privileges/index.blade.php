@extends('layouts.app')

@section('title', __('messages.sm_privileges_matrix'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">{{ __('messages.sm_privileges_matrix') }}</h1>
                <p class="text-muted">{{ __('messages.sm_manage_role_permissions_desc') }}</p>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 border-top">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4" style="width: 250px;">{{ __('messages.role') }}</th>
                                <th>{{ __('messages.permission_summary') }}</th>
                                <th class="pe-4 text-end">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold h6 mb-0">{{ $role->name }}</div>
                                        <div class="small text-muted">{{ $role->permissions->count() }}
                                            {{ __('messages.permissions_assigned') }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($role->permissions->take(8) as $perm)
                                                <span class="badge bg-light text-dark border small">{{ $perm->name }}</span>
                                            @endforeach
                                            @if($role->permissions->count() > 8)
                                                <span class="badge bg-light text-muted border small">+
                                                    {{ $role->permissions->count() - 8 }} more</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('acp.system.privileges.edit', $role) }}"
                                            class="btn btn-sm btn-primary">
                                            <i class="fas fa-shield-alt me-1"></i> {{ __('messages.manage_permissions') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-info-circle fa-2x opacity-50"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-1">{{ __('messages.tip') }}</h5>
                        <p class="card-text small mb-0">{{ __('messages.sm_privilege_tip_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
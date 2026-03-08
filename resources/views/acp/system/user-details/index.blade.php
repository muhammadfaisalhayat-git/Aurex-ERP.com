@extends('layouts.app')

@section('title', __('messages.sm_user_details'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">{{ __('messages.sm_user_details') }}</h1>
                <p class="text-muted">{{ __('messages.sm_view_user_profiles_desc') }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('acp.user-mgmt.user-profiles.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">{{ __('messages.search') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i
                                    class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0"
                                placeholder="{{ __('messages.search_by_name_email_code') }}"
                                value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">{{ __('messages.role') }}</label>
                        <select name="role" class="form-select">
                            <option value="">{{ __('messages.all_roles') }}</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">{{ __('messages.status') }}</label>
                        <select name="status" class="form-select">
                            <option value="">{{ __('messages.all_statuses') }}</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                {{ __('messages.active') }}
                            </option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                                {{ __('messages.inactive') }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            {{ __('messages.filter') }}
                        </button>
                        <a href="{{ route('acp.user-mgmt.user-profiles.index') }}" class="btn btn-outline-secondary">
                            {{ __('messages.reset') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
            @forelse($users as $user)
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 user-card overflow-hidden">
                        <div class="card-top-accent bg-primary" style="height: 4px;"></div>
                        <div class="card-body pt-4">
                            <div class="text-center mb-4">
                                <div class="avatar-lg bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center border"
                                    style="width: 80px; height: 80px;">
                                    <span class="h2 mb-0 text-primary">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                                <h5 class="mt-3 mb-1">{{ $user->name }}</h5>
                                <span
                                    class="badge rounded-pill bg-{{ $user->is_active ? 'success' : 'secondary' }}-soft text-{{ $user->is_active ? 'success' : 'secondary' }}"
                                    style="background: {{ $user->is_active ? 'rgba(46, 204, 113, 0.1)' : 'rgba(149, 165, 166, 0.1)' }}">
                                    {{ $user->is_active ? __('messages.active') : __('messages.inactive') }}
                                </span>
                            </div>

                            <ul class="list-group list-group-flush border-top border-bottom small">
                                <li class="list-group-item d-flex justify-content-between bg-transparent px-0 py-2">
                                    <span class="text-muted"><i class="fas fa-id-card me-2"></i>{{ __('messages.code') }}</span>
                                    <span class="fw-bold">{{ $user->employee_code ?? '-' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between bg-transparent px-0 py-2">
                                    <span class="text-muted"><i
                                            class="fas fa-envelope me-2"></i>{{ __('messages.email') }}</span>
                                    <span class="text-truncate ps-3">{{ $user->email }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between bg-transparent px-0 py-2">
                                    <span class="text-muted"><i
                                            class="fas fa-building me-2"></i>{{ __('messages.company') }}</span>
                                    <span>{{ $user->company->name ?? '-' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between bg-transparent px-0 py-2">
                                    <span class="text-muted"><i
                                            class="fas fa-code-branch me-2"></i>{{ __('messages.branch') }}</span>
                                    <span>{{ $user->branch->name ?? '-' }}</span>
                                </li>
                            </ul>

                            <div class="mt-3">
                                <p class="small text-muted mb-2">{{ __('messages.roles') }}</p>
                                <div class="d-flex flex-wrap gap-1">
                                    @forelse($user->roles as $role)
                                        <span class="badge bg-info-soft text-info"
                                            style="background: rgba(52, 152, 219, 0.1);">{{ $role->name }}</span>
                                    @empty
                                        <span class="text-muted small"><em>{{ __('messages.no_roles_assigned') }}</em></span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light border-0 d-grid">
                            <a href="{{ route('acp.user-mgmt.user-profiles.show', $user) }}"
                                class="btn btn-outline-primary btn-sm">
                                {{ __('messages.view_full_profile') }}
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">{{ __('messages.no_users_found') }}</h5>
                </div>
            @endforelse
        </div>

        @if($users->hasPages())
            <div class="mt-4 pb-4">
                {{ $users->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
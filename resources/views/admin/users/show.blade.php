@extends('layouts.app')

@section('title', __('messages.view_user'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.view_user') }}: {{ $user->name }}</h1>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <div class="avatar avatar-xl rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto"
                                style="width: 100px; height: 100px; font-size: 40px;">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        </div>
                        <h5 class="card-title">{{ $user->name }}</h5>
                        <p class="text-muted">{{ $user->email }}</p>
                        <div>
                            @php
                                $statusClass = $user->is_active ? 'success' : 'danger';
                                $statusText = $user->is_active ? 'active' : 'inactive';
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
                            @can('edit users')
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> {{ __('messages.edit_user') }}
                                </a>
                            @endcan

                            @can('edit users')
                                <form action="{{ route('admin.users.reset-password', $user) }}" method="POST"
                                    onsubmit="return confirm('{{ __('messages.confirm_reset_password') }}')">
                                    @csrf
                                    <button type="submit" class="btn btn-warning w-100">
                                        <i class="fas fa-key"></i> {{ __('messages.reset_password') }}
                                    </button>
                                </form>

                                <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-{{ $user->is_active ? 'danger' : 'success' }} w-100">
                                        <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                                        {{ $user->is_active ? __('messages.deactivate') : __('messages.activate') }}
                                    </button>
                                </form>
                            @endcan

                            @if(!$user->isSuperAdmin() && $user->id !== auth()->id())
                                @can('delete users')
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                        onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger w-100">
                                            <i class="fas fa-trash"></i> {{ __('messages.delete_user') }}
                                        </button>
                                    </form>
                                @endcan
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">{{ __('messages.details') }}</div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold">{{ __('messages.name') }}</div>
                            <div class="col-sm-9">{{ $user->name }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold">{{ __('messages.email') }}</div>
                            <div class="col-sm-9">{{ $user->email }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold">{{ __('messages.phone') }}</div>
                            <div class="col-sm-9">{{ $user->phone ?? '-' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold">{{ __('messages.employee_code') }}</div>
                            <div class="col-sm-9">{{ $user->employee_code ?? '-' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold">{{ __('messages.branch') }}</div>
                            <div class="col-sm-9">{{ $user->branch->name ?? '-' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold">{{ __('messages.default_language') }}</div>
                            <div class="col-sm-9">{{ $user->default_language == 'en' ? 'English' : 'Arabic' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold">{{ __('messages.last_login') }}</div>
                            <div class="col-sm-9">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : '-' }}
                                @if($user->last_login_ip)
                                    <small class="text-muted">({{ $user->last_login_ip }})</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">{{ __('messages.roles_permissions') }}</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="fw-bold mb-2">{{ __('messages.roles') }}</label>
                            <div>
                                @foreach($user->roles as $role)
                                    <span class="badge bg-primary me-1">{{ $role->name }}</span>
                                @endforeach
                            </div>
                        </div>

                        @if($user->warehouses->count() > 0)
                            <div>
                                <label class="fw-bold mb-2">{{ __('messages.warehouses') }}</label>
                                <div>
                                    @foreach($user->warehouses as $warehouse)
                                        <span class="badge bg-info me-1">{{ $warehouse->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
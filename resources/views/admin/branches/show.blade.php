@extends('layouts.app')

@section('title', __('messages.view_branch'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.view_branch') }}: {{ $branch->name }}</h1>
            <a href="{{ route('admin.branches.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">{{ $branch->name }}</h5>
                        <p class="text-muted">{{ $branch->code }}</p>
                        <div>
                            @php
                                $statusClass = $branch->is_active ? 'success' : 'danger';
                                $statusText = $branch->is_active ? 'active' : 'inactive';
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
                            @can('edit branches')
                                <a href="{{ route('admin.branches.edit', $branch) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> {{ __('messages.edit_branch') }}
                                </a>
                            @endcan

                            @can('delete branches')
                                <form action="{{ route('admin.branches.destroy', $branch) }}" method="POST"
                                    onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="fas fa-trash"></i> {{ __('messages.delete_branch') }}
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
                            <div class="col-sm-9">{{ $branch->code }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold">{{ __('messages.name_en') }}</div>
                            <div class="col-sm-9">{{ $branch->name_en }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold">{{ __('messages.name_ar') }}</div>
                            <div class="col-sm-9">{{ $branch->name_ar }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold">{{ __('messages.phone') }}</div>
                            <div class="col-sm-9">{{ $branch->phone ?? '-' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold">{{ __('messages.email') }}</div>
                            <div class="col-sm-9">{{ $branch->email ?? '-' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold">{{ __('messages.manager_name') }}</div>
                            <div class="col-sm-9">{{ $branch->manager_name ?? '-' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold">{{ __('messages.address') }}</div>
                            <div class="col-sm-9">{{ $branch->address ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">{{ __('messages.users') }}</div>
                    <div class="card-body">
                        @if($branch->users->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>{{ __('messages.name') }}</th>
                                            <th>{{ __('messages.email') }}</th>
                                            <th>{{ __('messages.role') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($branch->users as $user)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a>
                                                </td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    @foreach($user->roles as $role)
                                                        <span class="badge bg-secondary">{{ $role->name }}</span>
                                                    @endforeach
                                                </td>
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
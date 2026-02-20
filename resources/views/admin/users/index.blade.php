@extends('layouts.app')

@section('title', __('messages.users'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.users') }}</h1>
            @can('create users')
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('messages.create') }}
                </a>
            @endcan
        </div>

        <turbo-frame id="users_frame" data-turbo-action="advance">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.name') }}</th>
                                    <th>{{ __('messages.email') }}</th>
                                    <th>{{ __('messages.role') }}</th>
                                    <th>{{ __('messages.branch') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @foreach($user->roles as $role)
                                                <span class="badge bg-secondary">{{ $role->name }}</span>
                                            @endforeach
                                        </td>
                                        <td>{{ $user->branch->name ?? '-' }}</td>
                                        <td>
                                            @php
                                                $statusClass = $user->is_active ? 'success' : 'danger';
                                                $statusText = $user->is_active ? 'active' : 'inactive';
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">
                                                {{ __('messages.' . $statusText) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-info"
                                                    title="{{ __('messages.view') }}" data-turbo-frame="main-frame">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @can('edit users')
                                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary"
                                                        title="{{ __('messages.edit') }}" data-turbo-frame="main-frame">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">{{ __('messages.no_records_found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </turbo-frame>
    </div>
@endsection
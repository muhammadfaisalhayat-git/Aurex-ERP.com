@extends('layouts.app')

@section('title', __('messages.roles'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.roles') }}</h1>
            @can('create roles')
                <a href="{{ route('acp.user-mgmt.roles.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('messages.create') }}
                </a>
            @endcan
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.users') }}</th>
                                <th>{{ __('messages.permissions') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                                <tr>
                                    <td>{{ $role->name }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $role->users_count }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $role->permissions_count }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('acp.user-mgmt.roles.show', $role) }}" class="btn btn-sm btn-info"
                                                title="{{ __('messages.view') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($role->name !== 'Super Admin')
                                                @can('edit roles')
                                                    <a href="{{ route('acp.user-mgmt.roles.edit', $role) }}" class="btn btn-sm btn-primary"
                                                        title="{{ __('messages.edit') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete roles')
                                                    <form action="{{ route('acp.user-mgmt.roles.destroy', $role) }}" method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            title="{{ __('messages.delete') }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">{{ __('messages.no_records_found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $roles->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
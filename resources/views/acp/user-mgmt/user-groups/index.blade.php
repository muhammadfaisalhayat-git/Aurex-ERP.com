@extends('layouts.app')

@section('title', __('messages.sm_user_groups'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">{{ __('messages.sm_user_groups') }}</h1>
                <p class="text-muted">{{ __('messages.sm_user_groups_desc') }}</p>
            </div>
            <a href="{{ route('acp.user-mgmt.user-groups.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> {{ __('messages.create_new_group') }}
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">{{ __('messages.group_name') }}</th>
                                <th>{{ __('messages.description') }}</th>
                                <th>{{ __('messages.members') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th class="pe-4 text-end">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($groups as $group)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold">{{ $group->name }}</div>
                                        @if($group->name_ar)
                                            <div class="small text-muted">{{ $group->name_ar }}</div>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($group->description, 60) }}</td>
                                    <td>
                                        <span class="badge rounded-pill bg-info text-white">
                                            {{ $group->users_count }} {{ __('messages.members') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($group->is_active)
                                            <span class="badge rounded-pill bg-success-soft text-success"
                                                style="background: rgba(46, 204, 113, 0.1);">
                                                {{ __('messages.active') }}
                                            </span>
                                        @else
                                            <span class="badge rounded-pill bg-secondary-soft text-secondary"
                                                style="background: rgba(149, 165, 166, 0.1);">
                                                {{ __('messages.inactive') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('acp.user-mgmt.user-groups.show', $group) }}"
                                                class="btn btn-sm btn-light border" title="{{ __('messages.view') }}">
                                                <i class="fas fa-eye text-info"></i>
                                            </a>
                                            <a href="{{ route('acp.user-mgmt.user-groups.edit', $group) }}"
                                                class="btn btn-sm btn-light border" title="{{ __('messages.edit') }}">
                                                <i class="fas fa-edit text-primary"></i>
                                            </a>
                                            <form action="{{ route('acp.user-mgmt.user-groups.destroy', $group) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light border"
                                                    title="{{ __('messages.delete') }}">
                                                    <i class="fas fa-trash text-danger"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-users-slash mb-2 fa-2x"></i>
                                        <p class="mb-0">{{ __('messages.no_groups_found') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
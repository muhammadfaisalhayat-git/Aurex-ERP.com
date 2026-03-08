@extends('layouts.app')

@section('title', __('messages.sm_user_group_details'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a
                                href="{{ route('acp.user-mgmt.user-groups.index') }}">{{ __('messages.sm_user_groups') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('messages.details') }}</li>
                    </ol>
                </nav>
                <h1 class="h3 mb-0">{{ $userGroup->name }}</h1>
                @if($userGroup->name_ar)
                    <h4 class="text-muted h6 mt-1">{{ $userGroup->name_ar }}</h4>
                @endif
            </div>
            <div class="d-flex">
                <a href="{{ route('acp.user-mgmt.user-groups.edit', $userGroup) }}" class="btn btn-primary me-2">
                    <i class="fas fa-edit me-1"></i> {{ __('messages.edit_group') }}
                </a>
                <a href="{{ route('acp.user-mgmt.user-groups.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('messages.back_to_list') }}
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">{{ __('messages.group_info') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label
                                class="text-muted small fw-bold text-uppercase d-block mb-1">{{ __('messages.status') }}</label>
                            @if($userGroup->is_active)
                                <span class="badge rounded-pill bg-success-soft text-success"
                                    style="background: rgba(46, 204, 113, 0.1);">{{ __('messages.active') }}</span>
                            @else
                                <span class="badge rounded-pill bg-secondary-soft text-secondary"
                                    style="background: rgba(149, 165, 166, 0.1);">{{ __('messages.inactive') }}</span>
                            @endif
                        </div>
                        <div class="mb-4">
                            <label
                                class="text-muted small fw-bold text-uppercase d-block mb-1">{{ __('messages.description') }}</label>
                            <p class="mb-0">{{ $userGroup->description ?: __('messages.no_description') }}</p>
                        </div>
                        <div class="mb-0">
                            <label
                                class="text-muted small fw-bold text-uppercase d-block mb-1">{{ __('messages.member_count') }}</label>
                            <h3 class="mb-0">{{ $userGroup->users->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">{{ __('messages.group_members') }}</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-muted small">
                                    <tr>
                                        <th class="ps-4">{{ __('messages.name') }}</th>
                                        <th>{{ __('messages.email') }}</th>
                                        <th>{{ __('messages.branch') }}</th>
                                        <th class="pe-4">{{ __('messages.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($userGroup->users as $user)
                                        <tr>
                                            <td class="ps-4 fw-bold">{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->branch->name ?? '-' }}</td>
                                            <td class="pe-4">
                                                @if($user->is_active)
                                                    <span class="text-success"><i
                                                            class="fas fa-check-circle me-1"></i>{{ __('messages.active') }}</span>
                                                @else
                                                    <span class="text-muted"><i
                                                            class="fas fa-times-circle me-1"></i>{{ __('messages.inactive') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                {{ __('messages.no_members_in_group') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@extends('layouts.app')

@section('title', __('messages.sm_edit_user_group'))

@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a
                            href="{{ route('acp.user-mgmt.user-groups.index') }}">{{ __('messages.sm_user_groups') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ __('messages.edit') }}</li>
                </ol>
            </nav>
            <h1 class="h3">{{ __('messages.sm_edit_user_group') }}: {{ $userGroup->name }}</h1>
        </div>

        <form action="{{ route('acp.user-mgmt.user-groups.update', $userGroup) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">{{ __('messages.group_details') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('messages.group_name_en') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $userGroup->name) }}" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('messages.group_name_ar') }}</label>
                                    <input type="text" name="name_ar" class="form-control"
                                        value="{{ old('name_ar', $userGroup->name_ar) }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">{{ __('messages.description') }}</label>
                                    <textarea name="description" class="form-control"
                                        rows="3">{{ old('description', $userGroup->description) }}</textarea>
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                            id="is_active" {{ $userGroup->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">{{ __('messages.active') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ __('messages.manage_members') }}</h5>
                            <span class="badge bg-light text-muted">{{ $userGroup->users->count() }}
                                {{ __('messages.current_members') }}</span>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @php $groupUserIds = $userGroup->users->pluck('id')->toArray(); @endphp
                                @foreach($users as $user)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="users[]"
                                                value="{{ $user->id }}" id="user_{{ $user->id }}" {{ in_array($user->id, $groupUserIds) ? 'checked' : '' }}>
                                            <label class="form-check-label d-flex flex-column" for="user_{{ $user->id }}">
                                                <span class="fw-medium">{{ $user->name }}</span>
                                                <span class="small text-muted">{{ $user->email }}</span>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 sticky-top" style="top: 100px;">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-save me-1"></i> {{ __('messages.update_group') }}
                            </button>
                            <a href="{{ route('acp.user-mgmt.user-groups.index') }}"
                                class="btn btn-outline-secondary w-100">
                                {{ __('messages.cancel') }}
                            </a>
                            <hr>
                            <div class="small text-muted">
                                <i class="fas fa-clock me-1"></i> {{ __('messages.created_at') }}:
                                {{ $userGroup->created_at->format('Y-m-d') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
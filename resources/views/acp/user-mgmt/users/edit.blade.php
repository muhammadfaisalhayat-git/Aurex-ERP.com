@extends('layouts.app')

@section('title', __('messages.edit_user'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.edit_user') }}: {{ $user->name }}</h1>
            <a href="{{ route('acp.user-mgmt.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('acp.user-mgmt.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Photo Upload --}}
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex align-items-center gap-4 p-3 bg-light rounded">
                                <div id="avatar-preview-wrapper">
                                    @if($user->avatar)
                                        <img id="avatar-preview" src="{{ asset('storage/' . $user->avatar) }}"
                                            class="rounded-circle border" style="width:80px;height:80px;object-fit:cover;">
                                    @else
                                        <div id="avatar-placeholder" class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white"
                                            style="width:80px;height:80px;font-size:2rem;">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <label for="avatar" class="form-label fw-bold mb-1">{{ __('messages.profile_photo') }}</label>
                                    <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar"
                                        name="avatar" accept="image/*" onchange="previewAvatar(event)">
                                    <small class="text-muted">{{ __('messages.photo_upload_hint') }}</small>
                                    @error('avatar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">{{ __('messages.name') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">{{ __('messages.email') }} <span
                                    class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">{{ __('messages.phone') }}</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                name="phone" value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="employee_code" class="form-label">{{ __('messages.employee_code') }}</label>
                            <input type="text" class="form-control @error('employee_code') is-invalid @enderror"
                                id="employee_code" name="employee_code"
                                value="{{ old('employee_code', $user->employee_code) }}">
                            @error('employee_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="branch_id" class="form-label">{{ __('messages.branch') }}</label>
                            <select class="form-control @error('branch_id') is-invalid @enderror" id="branch_id"
                                name="branch_id">
                                <option value="">{{ __('messages.select_branch') }}</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id', $user->branch_id) == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="default_language" class="form-label">{{ __('messages.default_language') }} <span
                                    class="text-danger">*</span></label>
                            <select class="form-control @error('default_language') is-invalid @enderror"
                                id="default_language" name="default_language" required>
                                <option value="en" {{ old('default_language', $user->default_language) == 'en' ? 'selected' : '' }}>English</option>
                                <option value="ar" {{ old('default_language', $user->default_language) == 'ar' ? 'selected' : '' }}>Arabic</option>
                            </select>
                            @error('default_language')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">{{ __('messages.password') }}</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password">
                            <small class="text-muted">{{ __('messages.leave_blank_to_keep_current') }}</small>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation"
                                class="form-label">{{ __('messages.confirm_password') }}</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="roles" class="form-label">{{ __('messages.roles') }} <span
                                    class="text-danger">*</span></label>
                            <select class="form-control @error('roles') is-invalid @enderror" id="roles" name="roles[]"
                                multiple required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ app()->getLocale() == 'ar' ? ($role->display_name_ar ?? $role->name) : ($role->display_name_en ?? $role->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('roles')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="warehouses" class="form-label">{{ __('messages.warehouses') }}</label>
                            <select class="form-control @error('warehouses') is-invalid @enderror" id="warehouses"
                                name="warehouses[]" multiple>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ in_array($warehouse->id, old('warehouses', $user->warehouses->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('warehouses')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password_reset_key" class="form-label">{{ __('messages.password_reset_key') }} ({{ __('messages.administrative_access_only') }})</label>
                            <input type="text" class="form-control @error('password_reset_key') is-invalid @enderror"
                                id="password_reset_key" name="password_reset_key"
                                value="{{ old('password_reset_key', $user->password_reset_key) }}"
                                {{ auth()->user()->hasRole('Super Admin') ? '' : 'disabled' }}>
                            <small class="text-muted">{{ __('messages.password_reset_key_description') }}</small>
                            @error('password_reset_key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                {{ __('messages.active') }}
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('messages.update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function previewAvatar(event) {
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        const wrapper = document.getElementById('avatar-preview-wrapper');
        wrapper.innerHTML = `<img id="avatar-preview" src="${e.target.result}" class="rounded-circle border" style="width:80px;height:80px;object-fit:cover;">`;
    };
    reader.readAsDataURL(file);
}
</script>
@endpush
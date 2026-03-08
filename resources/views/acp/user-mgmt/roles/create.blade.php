@extends('layouts.app')

@section('title', __('messages.create_role'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.create_role') }}</h1>
            <a href="{{ route('acp.user-mgmt.roles.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('acp.user-mgmt.roles.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="name" class="form-label">{{ __('messages.name') }} (System) <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" required>
                            <small class="text-muted">Unique system name (e.g. sales_manager)</small>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="display_name_en" class="form-label">{{ __('messages.name_en') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('display_name_en') is-invalid @enderror"
                                id="display_name_en" name="display_name_en" value="{{ old('display_name_en') }}" required>
                            @error('display_name_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="display_name_ar" class="form-label">{{ __('messages.name_ar') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('display_name_ar') is-invalid @enderror"
                                id="display_name_ar" name="display_name_ar" value="{{ old('display_name_ar') }}" required>
                            @error('display_name_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr>
                    <h5 class="mb-3">{{ __('messages.permissions') }}</h5>

                    <div class="row">
                        @foreach($permissions as $module => $modulePermissions)
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-light fw-bold">
                                        {{ ucfirst($module) }}
                                    </div>
                                    <div class="card-body">
                                        @foreach($modulePermissions as $permission)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="permissions[]"
                                                    value="{{ $permission->id }}" id="perm_{{ $permission->id }}" {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('messages.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
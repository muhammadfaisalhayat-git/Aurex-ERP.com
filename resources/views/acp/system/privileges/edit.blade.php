@extends('layouts.app')

@section('title', __('messages.sm_edit_privileges'))

@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a
                            href="{{ route('acp.system.privileges.index') }}">{{ __('messages.sm_privileges_matrix') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ __('messages.edit') }}</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">{{ __('messages.sm_edit_privileges') }}: <span
                        class="text-primary">{{ $role->name }}</span></h1>
                <a href="{{ route('acp.system.privileges.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('messages.back') }}
                </a>
            </div>
        </div>

        <form action="{{ route('acp.system.privileges.update', $role) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-lg-9">
                    @foreach($permissions as $group => $perms)
                        <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                            <div class="card-header bg-light-soft py-3 d-flex justify-content-between align-items-center"
                                style="background: rgba(var(--bs-primary-rgb), 0.05);">
                                <h6 class="mb-0 fw-bold text-primary text-uppercase letter-spacing-1">
                                    <i class="fas fa-layer-group me-2"></i>{{ ucfirst($group) }} {{ __('messages.module') }}
                                </h6>
                                <div class="form-check form-check-inline mb-0">
                                    <input class="form-check-input select-all-in-group" type="checkbox"
                                        id="select_all_{{ $group }}">
                                    <label class="form-check-label small"
                                        for="select_all_{{ $group }}">{{ __('messages.select_all') }}</label>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    @foreach($perms as $perm)
                                        <div class="col-md-4 col-sm-6">
                                            <div class="permission-item p-2 rounded border transition-base hover-bg-light">
                                                <div class="form-check mb-0">
                                                    <input class="form-check-input permission-checkbox" type="checkbox"
                                                        name="permissions[]" value="{{ $perm->id }}" id="perm_{{ $perm->id }}" {{ in_array($perm->id, $rolePermissions) ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-medium cursor-pointer"
                                                        for="perm_{{ $perm->id }}">
                                                        {{ $perm->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="col-lg-3">
                    <div class="card shadow-sm border-0 sticky-top" style="top: 100px;">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3 border-bottom pb-2">{{ __('messages.summary') }}</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">{{ __('messages.total_permissions') }}</span>
                                <span id="total_count" class="fw-bold">{{ count($rolePermissions) }}</span>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mt-3 py-2 shadow-sm">
                                <i class="fas fa-save me-1"></i> {{ __('messages.save_changes') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Toggle group selection
                document.querySelectorAll('.select-all-in-group').forEach(checkbox => {
                    checkbox.addEventListener('change', function () {
                        const group = this.closest('.card').querySelectorAll('.permission-checkbox');
                        group.forEach(cb => {
                            cb.checked = this.checked;
                        });
                        updateTotalCount();
                    });
                });

                // Update total count
                const checkboxes = document.querySelectorAll('.permission-checkbox');
                checkboxes.forEach(cb => {
                    cb.addEventListener('change', updateTotalCount);
                });

                function updateTotalCount() {
                    const count = document.querySelectorAll('.permission-checkbox:checked').length;
                    document.getElementById('total_count').textContent = count;
                }
            });
        </script>
    @endpush
@endsection
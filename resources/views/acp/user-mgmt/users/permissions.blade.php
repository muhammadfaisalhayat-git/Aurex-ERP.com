@extends('layouts.app')

@section('title', __('messages.manage_permissions'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('acp.user-mgmt.user-profiles.index') }}">{{ __('messages.user_profiles') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('acp.user-mgmt.user-profiles.show', $user) }}">{{ $user->name }}</a></li>
                        <li class="breadcrumb-item active">{{ __('messages.manage_permissions') }}</li>
                    </ol>
                </nav>
                <h1 class="h3 mb-0">{{ __('messages.manage_permissions') }}: {{ $user->name }}</h1>
            </div>
            <a href="{{ route('acp.user-mgmt.user-profiles.show', $user) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> {{ __('messages.back') }}
            </a>
        </div>

        <form action="{{ route('acp.user-mgmt.users.permissions.update', $user) }}" method="POST">
            @csrf
            
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body bg-light rounded shadow-sm mb-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $user->name }}</h5>
                            <p class="text-muted small mb-0">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    @foreach($permissions as $module => $modulePermissions)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border shadow-sm">
                                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold text-primary text-uppercase">{{ ucfirst($module) }}</h6>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input select-all-module" type="checkbox" data-module="{{ $module }}">
                                        <label class="form-check-label small text-muted">{{ __('messages.select_all') }}</label>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        @foreach($modulePermissions as $permission)
                                            <div class="col-12">
                                                <div class="form-check">
                                                    <input class="form-check-input permission-checkbox module-{{ $module }}" 
                                                           type="checkbox" 
                                                           name="permissions[]" 
                                                           value="{{ $permission->id }}" 
                                                           id="perm_{{ $permission->id }}" 
                                                           {{ in_array($permission->id, $userPermissions) ? 'checked' : '' }}>
                                                    <label class="form-check-label small" for="perm_{{ $permission->id }}">
                                                        {{ $permission->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="card-footer bg-white py-4 mt-4 border-top-0 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="fas fa-save me-1"></i> {{ __('messages.save_changes') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('turbo:load', function() {
        // Toggle all checkboxes in a module
        document.querySelectorAll('.select-all-module').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const module = this.dataset.module;
                document.querySelectorAll('.module-' + module).forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        });

        // Initialize Select All state based on checkboxes
        document.querySelectorAll('.select-all-module').forEach(toggle => {
            const module = toggle.dataset.module;
            const checkboxes = document.querySelectorAll('.module-' + module);
            const checkedCount = document.querySelectorAll('.module-' + module + ':checked').length;
            toggle.checked = (checkboxes.length > 0 && checkedCount === checkboxes.length);
        });

        // Update Select All state when individual checkbox is changed
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const moduleClass = Array.from(this.classList).find(cls => cls.startsWith('module-'));
                if (moduleClass) {
                    const module = moduleClass.replace('module-', '');
                    const toggle = document.querySelector(`.select-all-module[data-module="${module}"]`);
                    const checkboxes = document.querySelectorAll('.' + moduleClass);
                    const checkedCount = document.querySelectorAll('.' + moduleClass + ':checked').length;
                    toggle.checked = (checkedCount === checkboxes.length);
                }
            });
        });
    });
</script>
@endpush

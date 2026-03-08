@extends('layouts.app')

@section('title', __('messages.sm_restore_backup'))

@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h1 class="h3 mb-0">{{ __('messages.sm_restore_backup') }}</h1>
            <p class="text-muted">{{ __('messages.sm_recover_data_desc') }}</p>
        </div>

        <div class="alert alert-warning border-0 shadow-sm mb-4">
            <div class="d-flex">
                <div class="me-3">
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">{{ __('messages.caution_critical_operation') }}</h6>
                    <p class="mb-0 small">{{ __('messages.restore_warning_desc') }}</p>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">{{ __('messages.select_backup_to_restore') }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">{{ __('messages.backup_file') }}</th>
                                <th>{{ __('messages.size') }}</th>
                                <th>{{ __('messages.created_at') }}</th>
                                <th>{{ __('messages.created_by') }}</th>
                                <th class="pe-4 text-end">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($backups as $backup)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-file-archive text-warning me-3 fa-lg"></i>
                                            <div>
                                                <div class="fw-bold">{{ $backup->filename }}</div>
                                                <div class="small text-muted">{{ $backup->disk }} disk storage</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $backup->formatted_size }}</td>
                                    <td>{{ $backup->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $backup->creator->name ?? __('messages.system') }}</td>
                                    <td class="pe-4 text-end">
                                        @can('restore backups')
                                            <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm"
                                                data-bs-toggle="modal" data-bs-target="#restoreModal{{ $backup->id }}">
                                                <i class="fas fa-undo-alt me-1"></i> {{ __('messages.restore_now') }}
                                            </button>
                                        @endcan

                                        <!-- Confirmation Modal -->
                                        <div class="modal fade" id="restoreModal{{ $backup->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content border-0">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title fw-bold">{{ __('messages.confirm_restore') }}
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-center p-4">
                                                        <i class="fas fa-exclamation-circle text-danger fa-4x mb-3"></i>
                                                        <h4 class="fw-bold">{{ __('messages.are_you_sure') }}</h4>
                                                        <p class="text-muted">
                                                            {{ __('messages.restore_confirmation_text', ['file' => $backup->filename]) }}
                                                        </p>
                                                        <div class="bg-light p-3 rounded mb-3 text-start small">
                                                            <strong>{{ __('messages.operation_details') }}:</strong><br>
                                                            - {{ __('messages.current_data_overwrite') }}<br>
                                                            - {{ __('messages.system_maintenance_mode') }}<br>
                                                            - {{ __('messages.logout_all_users') }}
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                                                        <form action="{{ route('acp.system.restore-backup.restore', $backup) }}"
                                                            method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-danger px-4 fw-bold">
                                                                <i class="fas fa-check me-1"></i>
                                                                {{ __('messages.confirm_and_restore') }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-history fa-2x mb-2"></i>
                                        <p class="mb-0">{{ __('messages.no_valid_backups_for_restore') }}</p>
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
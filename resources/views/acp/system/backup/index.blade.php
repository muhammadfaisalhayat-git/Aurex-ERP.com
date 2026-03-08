@extends('layouts.app')

@section('title', __('messages.sm_backup_management'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">{{ __('messages.sm_backup_management') }}</h1>
                <p class="text-muted">{{ __('messages.sm_data_safety_desc') }}</p>
            </div>
            <form action="{{ route('acp.system.backup.create') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary shadow-sm px-4">
                    <i class="fas fa-database me-1"></i> {{ __('messages.run_manual_backup') }}
                </button>
            </form>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-primary text-white h-100">
                    <div class="card-body">
                        <h6 class="text-white-50 small text-uppercase fw-bold">{{ __('messages.last_successful_backup') }}
                        </h6>
                        <h3 class="mb-2">
                            {{ $backups->where('status', 'completed')->first()?->created_at->diffForHumans() ?? __('messages.never') }}
                        </h3>
                        <div class="small fw-medium">
                            <i class="fas fa-check-circle me-1"></i> {{ $backups->where('status', 'completed')->count() }}
                            {{ __('messages.backups_available') }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h6 class="text-muted small text-uppercase fw-bold">{{ __('messages.total_storage_used') }}</h6>
                        <h3 class="mb-2">
                            @php $totalBytes = $backups->where('status', 'completed')->sum('size_bytes'); @endphp
                            @if ($totalBytes >= 1073741824) {{ number_format($totalBytes / 1073741824, 2) }} GB
                            @elseif ($totalBytes >= 1048576) {{ number_format($totalBytes / 1048576, 2) }} MB
                            @else {{ number_format($totalBytes / 1024, 2) }} KB
                            @endif
                        </h3>
                        <div class="small text-muted">
                            <i class="fas fa-hdd me-1"></i> {{ __('messages.storage_on_local_and_cloud') }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h6 class="text-muted small text-uppercase fw-bold">{{ __('messages.backup_status') }}</h6>
                        <div class="d-flex align-items-center mt-3">
                            <span class="badge bg-success-soft text-success rounded-pill px-3 py-2 me-2"
                                style="background: rgba(46, 204, 113, 0.1);">
                                <i class="fas fa-clock me-1"></i> {{ __('messages.daily_schedule_active') }}
                            </span>
                        </div>
                        <div class="small text-muted mt-2">{{ __('messages.next_scheduled_at') }}:
                            {{ date('Y-m-d 02:00:00', strtotime('tomorrow')) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="mb-0 fw-bold">{{ __('messages.backup_history') }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">{{ __('messages.filename') }}</th>
                                <th>{{ __('messages.size') }}</th>
                                <th>{{ __('messages.destination') }}</th>
                                <th>{{ __('messages.created_at') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th class="pe-4 text-end">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($backups as $backup)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold">{{ $backup->filename }}</div>
                                        <div class="small text-muted">{{ $backup->notes }}</div>
                                    </td>
                                    <td>{{ $backup->formatted_size }}</td>
                                    <td>
                                        @if($backup->disk == 'google')
                                            <span class="badge bg-primary-soft text-primary"
                                                style="background: rgba(var(--bs-primary-rgb), 0.1);">
                                                <i class="fab fa-google-drive me-1"></i> Google Drive
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-soft text-dark"
                                                style="background: rgba(149, 165, 166, 0.1);">
                                                <i class="fas fa-server me-1"></i> Local Disk
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $backup->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @if($backup->status == 'completed')
                                            <span class="badge bg-success rounded-pill">{{ __('messages.success') }}</span>
                                        @elseif($backup->status == 'failed')
                                            <span class="badge bg-danger rounded-pill">{{ __('messages.failed') }}</span>
                                        @else
                                            <span class="badge bg-info rounded-pill">{{ __('messages.in_progress') }}</span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        @if($backup->status == 'completed')
                                            <a href="{{ route('acp.system.backup.download', $backup) }}"
                                                class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                <i class="fas fa-download me-1"></i> {{ __('messages.download') }}
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-database fa-2x mb-2"></i>
                                        <p class="mb-0">{{ __('messages.no_backups_found') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($backups->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $backups->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
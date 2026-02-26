@extends('layouts.app')

@section('title', __('messages.audit_logs'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.audit_logs') }}</h1>
        </div>

        <!-- Filters -->
        <div class="card glassy mb-4">
            <div class="card-body">
                <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label small">{{ __('messages.date_from') }}</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">{{ __('messages.date_to') }}</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">{{ __('messages.user') }}</label>
                        <select name="user_id" class="form-select select2">
                            <option value="">{{ __('messages.all') }}</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">{{ __('messages.action') }}</label>
                        <select name="action" class="form-select select2">
                            <option value="">{{ __('messages.all') }}</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                    {{ $action }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">{{ __('messages.entity_type') }}</label>
                        <select name="entity_type" class="form-select select2">
                            <option value="">{{ __('messages.all') }}</option>
                            @foreach($entities as $entity)
                                <option value="{{ $entity }}" {{ request('entity_type') == $entity ? 'selected' : '' }}>
                                    {{ class_basename($entity) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> {{ __('messages.filter') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Logs Table -->
        <div class="card glassy">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.user') }}</th>
                                <th>{{ __('messages.action') }}</th>
                                <th>{{ __('messages.entity') }}</th>
                                <th>{{ __('messages.ip_address') }}</th>
                                <th class="text-center">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $log->created_at->format('Y-m-d') }}</div>
                                        <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <i class="fas fa-user-circle fa-lg text-secondary"></i>
                                            </div>
                                            <span>{{ $log->user->name ?? __('messages.system') }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge {{ $log->action == 'delete' ? 'bg-danger' : ($log->action == 'update' ? 'bg-warning' : 'bg-success') }}">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small fw-bold">{{ class_basename($log->entity_type) }}</div>
                                        <div class="text-muted tiny">ID: {{ $log->entity_id }}</div>
                                    </td>
                                    <td>
                                        <div class="small">{{ $log->ip_address }}</div>
                                        <div class="text-muted tiny text-truncate" style="max-width: 150px;"
                                            title="{{ $log->user_agent }}">
                                            {{ $log->user_agent }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                            data-bs-target="#logModal{{ $log->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Detail Modal -->
                                <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg border-0">
                                        <div class="modal-content glassy border-0">
                                            <div class="modal-header border-0">
                                                <h5 class="modal-title">{{ __('messages.view_details') }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="small text-muted">{{ __('messages.url') }}</label>
                                                        <div class="p-2 bg-light rounded small text-break">{{ $log->url }}</div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="small text-muted">{{ __('messages.notes') }}</label>
                                                        <div class="p-2 bg-light rounded small">{{ $log->notes ?: '-' }}</div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6 class="border-bottom pb-2">{{ __('messages.old_values') }}</h6>
                                                        <div class="json-viewer bg-dark text-light p-3 rounded"
                                                            style="max-height: 300px; overflow-y: auto;">
                                                            <pre
                                                                class="small mb-0">@json($log->old_values, JSON_PRETTY_PRINT)</pre>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6 class="border-bottom pb-2 text-success">
                                                            {{ __('messages.new_values') }}</h6>
                                                        <div class="json-viewer bg-dark text-light p-3 rounded"
                                                            style="max-height: 300px; overflow-y: auto;">
                                                            <pre
                                                                class="small mb-0">@json($log->new_values, JSON_PRETTY_PRINT)</pre>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-history fa-3x mb-3 d-block"></i>
                                        {{ __('messages.no_records_found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($logs->hasPages())
                <div class="card-footer bg-transparent border-0">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
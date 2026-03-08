@extends('layouts.app')

@section('title', __('messages.sm_transaction_audit'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">{{ __('messages.sm_transaction_audit') }}</h1>
                <p class="text-muted">{{ __('messages.sm_transaction_audit_desc') }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('acp.system.transaction-audit.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">{{ __('messages.user') }}</label>
                        <select name="user_id" class="form-select select2">
                            <option value="">{{ __('messages.all_users') }}</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">{{ __('messages.action') }}</label>
                        <select name="action" class="form-select">
                            <option value="">{{ __('messages.all_actions') }}</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                    {{ ucfirst($action) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">{{ __('messages.module') }}</label>
                        <select name="entity_type" class="form-select">
                            <option value="">{{ __('messages.all_modules') }}</option>
                            @foreach($entityTypes as $type)
                                <option value="{{ $type }}" {{ request('entity_type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">{{ __('messages.from_date') }}</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">{{ __('messages.to_date') }}</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">{{ __('messages.timestamp') }}</th>
                                <th>{{ __('messages.user') }}</th>
                                <th>{{ __('messages.action') }}</th>
                                <th>{{ __('messages.module') }}</th>
                                <th>{{ __('messages.details') }}</th>
                                <th class="pe-4">{{ __('messages.ip_address') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($audits as $audit)
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-bold">{{ $audit->created_at->format('M d, Y') }}</span>
                                        <div class="small text-muted">{{ $audit->created_at->format('H:i:s') }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ $audit->user->name ?? 'System' }}</div>
                                    </td>
                                    <td>
                                        @php
                                            $actionClass = match ($audit->action) {
                                                'create' => 'success',
                                                'update' => 'primary',
                                                'delete' => 'danger',
                                                default => 'info'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $actionClass }}-soft text-{{ $actionClass }}"
                                            style="background: rgba(var(--bs-{{ $actionClass }}-rgb), 0.1);">
                                            {{ strtoupper($audit->action) }}
                                        </span>
                                    </td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $audit->entity_type)) }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none"
                                            data-bs-toggle="modal" data-bs-target="#auditModal{{ $audit->id }}">
                                            {{ __('messages.view_changes') }}
                                        </button>

                                        <!-- Modal -->
                                        <div class="modal fade" id="auditModal{{ $audit->id }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-light">
                                                        <h5 class="modal-title">{{ __('messages.audit_details') }}
                                                            #{{ $audit->id }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row mb-3">
                                                            <div class="col-md-4">
                                                                <div class="small text-muted mb-1">
                                                                    {{ __('messages.performed_by') }}</div>
                                                                <div class="fw-bold">{{ $audit->user->name ?? 'System' }}</div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="small text-muted mb-1">
                                                                    {{ __('messages.timestamp') }}</div>
                                                                <div class="fw-bold">{{ $audit->created_at }}</div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="small text-muted mb-1">
                                                                    {{ __('messages.target_record') }}</div>
                                                                <div class="fw-bold">{{ $audit->entity_type }} (ID:
                                                                    {{ $audit->entity_id }})</div>
                                                            </div>
                                                        </div>

                                                        <div class="row h-100">
                                                            <div class="col-md-6">
                                                                <h6
                                                                    class="border-bottom pb-2 mb-3 text-muted fw-bold small text-uppercase">
                                                                    {{ __('messages.old_values') }}</h6>
                                                                <pre class="bg-light p-3 rounded small"
                                                                    style="max-height: 300px;">{{ json_encode($audit->old_values, JSON_PRETTY_PRINT) ?: 'No changes' }}</pre>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h6
                                                                    class="border-bottom pb-2 mb-3 text-primary fw-bold small text-uppercase">
                                                                    {{ __('messages.new_values') }}</h6>
                                                                <pre class="bg-light p-3 rounded small"
                                                                    style="max-height: 300px;">{{ json_encode($audit->new_values, JSON_PRETTY_PRINT) ?: 'No changes' }}</pre>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="pe-4"><code>{{ $audit->ip_address }}</code></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-history mb-2 fa-2x"></i>
                                        <p class="mb-0">{{ __('messages.no_audits_found') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($audits->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $audits->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
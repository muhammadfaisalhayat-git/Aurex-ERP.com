@extends('layouts.app')

@section('title', __('messages.sm_alert_system'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">{{ __('messages.sm_alert_system') }}</h1>
                <p class="text-muted">{{ __('messages.sm_alert_desc') }}</p>
            </div>
            <a href="{{ route('acp.system.alert-system.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> {{ __('messages.create_alert_rule') }}
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">{{ __('messages.alert_name') }}</th>
                                <th>{{ __('messages.module') }}</th>
                                <th>{{ __('messages.condition') }}</th>
                                <th>{{ __('messages.threshold') }}</th>
                                <th>{{ __('messages.recipients') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th class="pe-4 text-end">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($alerts as $alert)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold">{{ $alert->name }}</div>
                                        <div class="small text-muted">ID: #{{ $alert->id }}</div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $alert->module }}</span></td>
                                    <td>{{ str_replace('_', ' ', ucfirst($alert->condition_type)) }}</td>
                                    <td><span class="fw-bold text-primary">{{ number_format($alert->threshold, 2) }}</span></td>
                                    <td>
                                        @php $recipients = $alert->recipients ?? []; @endphp
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach(array_slice($recipients, 0, 2) as $email)
                                                <span class="badge bg-info-soft text-info"
                                                    style="background: rgba(52, 152, 219, 0.1);">{{ $email }}</span>
                                            @endforeach
                                            @if(count($recipients) > 2)
                                                <span class="text-muted small">+{{ count($recipients) - 2 }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" {{ $alert->is_active ? 'checked' : '' }} disabled>
                                        </div>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('acp.system.alert-system.edit', $alert) }}"
                                                class="btn btn-sm btn-light border">
                                                <i class="fas fa-edit text-primary"></i>
                                            </a>
                                            <form action="{{ route('acp.system.alert-system.destroy', $alert) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('{{ __('messages.confirm_delete_alert') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light border border-start-0">
                                                    <i class="fas fa-trash text-danger"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-bell-slash fa-2x mb-2"></i>
                                        <p class="mb-0">{{ __('messages.no_alerts_found') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($alerts->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $alerts->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
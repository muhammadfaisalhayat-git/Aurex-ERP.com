@extends('layouts.app')

@section('title', __('messages.sm_user_login_details'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">{{ __('messages.sm_user_login_details') }}</h1>
                <p class="text-muted">{{ __('messages.sm_login_history_desc') }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('acp.system.user-login-details.index') }}" method="GET" class="row g-3">
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
                        <label class="form-label small fw-bold">{{ __('messages.status') }}</label>
                        <select name="status" class="form-select">
                            <option value="">{{ __('messages.all_statuses') }}</option>
                            <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>
                                {{ __('messages.success') }}</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>
                                {{ __('messages.failed') }}</option>
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
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i> {{ __('messages.filter') }}
                        </button>
                        <a href="{{ route('acp.system.user-login-details.index') }}"
                            class="btn btn-outline-secondary">
                            <i class="fas fa-undo me-1"></i> {{ __('messages.reset') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">{{ __('messages.user') }}</th>
                                <th>{{ __('messages.login_time') }}</th>
                                <th>{{ __('messages.logout_time') }}</th>
                                <th>{{ __('messages.ip_address') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th class="pe-4">{{ __('messages.user_agent') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loginDetails as $detail)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary-soft text-primary rounded-circle me-3 d-flex align-items-center justify-content-center"
                                                style="width: 32px; height: 32px; background: rgba(52, 152, 219, 0.1);">
                                                {{ substr($detail->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $detail->user->name }}</div>
                                                <div class="small text-muted">{{ $detail->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $detail->login_at?->format('Y-m-d H:i:s') }}</td>
                                    <td>{{ $detail->logout_at?->format('Y-m-d H:i:s') ?? '-' }}</td>
                                    <td><code>{{ $detail->ip_address }}</code></td>
                                    <td>
                                        @if($detail->status == 'success')
                                            <span class="badge rounded-pill bg-success-soft text-success"
                                                style="background: rgba(46, 204, 113, 0.1);">
                                                <i class="fas fa-check-circle me-1"></i> {{ __('messages.success') }}
                                            </span>
                                        @else
                                            <span class="badge rounded-pill bg-danger-soft text-danger"
                                                style="background: rgba(231, 76, 60, 0.1);">
                                                <i class="fas fa-times-circle me-1"></i> {{ __('messages.failed') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="pe-4">
                                        <span class="small text-muted" title="{{ $detail->user_agent }}">
                                            {{ Str::limit($detail->user_agent, 40) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-info-circle mb-2 fa-2x"></i>
                                        <p class="mb-0">{{ __('messages.no_records_found') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($loginDetails->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $loginDetails->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
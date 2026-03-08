@extends('layouts.app')

@section('title', __('messages.sm_privilege_report'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">{{ __('messages.sm_privilege_report') }}</h1>
                <p class="text-muted">{{ __('messages.sm_access_audit_report_desc') }}</p>
            </div>
            <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                <i class="fas fa-print me-1"></i> {{ __('messages.print_report') }}
            </button>
        </div>

        <!-- Filters -->
        <div class="card mb-4 shadow-sm border-0 d-print-none">
            <div class="card-body">
                <form action="{{ route('acp.system.privilege-reports.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">{{ __('messages.filter_by_role') }}</label>
                        <select name="role_id" class="form-select select2" onchange="this.form.submit()">
                            <option value="">{{ __('messages.all_roles') }}</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-bold">{{ __('messages.search_permission') }}</label>
                        <input type="text" name="search" class="form-control"
                            placeholder="{{ __('messages.search_placeholder') }}" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">{{ __('messages.generate_report') }}</button>
                    </div>
                </form>
            </div>
        </div>

        @foreach($reportData as $data)
            <div class="card shadow-sm border-0 mb-4 page-break-after">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold">{{ __('messages.role') }}: <span
                                    class="text-primary">{{ $data['role'] }}</span></h5>
                            <div class="small text-muted">{{ count($data['users']) }} {{ __('messages.users_assigned') }}</div>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted mb-1">{{ __('messages.permissions_count') }}</div>
                            <span class="h6 mb-0">{{ count($data['permissions']) }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 border-end">
                            <h6 class="fw-bold small text-uppercase mb-3 text-muted">{{ __('messages.assigned_users') }}</h6>
                            <div class="d-flex flex-wrap gap-1">
                                @forelse($data['users'] as $user)
                                    <span class="badge bg-light text-dark border">{{ $user }}</span>
                                @empty
                                    <span class="text-muted italic small">{{ __('messages.no_users_assigned') }}</span>
                                @endforelse
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h6 class="fw-bold small text-uppercase mb-3 text-muted">
                                {{ __('messages.assigned_permissions_matrix') }}</h6>
                            <div class="row g-2">
                                @php
                                    $groupedPerms = collect($data['permissions'])->groupBy(function ($p) {
                                        return explode(' ', $p)[1] ?? 'module';
                                    });
                                @endphp
                                @forelse($groupedPerms as $module => $perms)
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="fw-bold small text-primary mb-1 border-bottom pb-1">{{ strtoupper($module) }}
                                        </div>
                                        <ul class="list-unstyled mb-0" style="font-size: 11px;">
                                            @foreach($perms as $p)
                                                <li><i class="fas fa-check text-success me-1"></i> {{ $p }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @empty
                                    <div class="col-12 text-center py-3 text-muted">
                                        {{ __('messages.no_permissions_assigned') }}
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="mt-5 d-none d-print-block text-center border-top pt-4">
            <div class="row">
                <div class="col-4">
                    <div class="small text-muted mb-4">{{ __('messages.generated_by') }}</div>
                    <div class="border-bottom mx-auto w-50" style="height: 40px;"></div>
                    <div class="small mt-2">{{ auth()->user()->name }}</div>
                </div>
                <div class="col-4">
                    <div class="small text-muted mb-4">{{ __('messages.system_audit_id') }}</div>
                    <div class="small fw-bold">AUTH-RPT-{{ date('YmdHis') }}</div>
                </div>
                <div class="col-4">
                    <div class="small text-muted mb-4">{{ __('messages.approval_stamp') }}</div>
                    <div class="border-bottom mx-auto w-50" style="height: 40px;"></div>
                    <div class="small mt-2">{{ __('messages.official_date') }}: {{ date('Y-m-d') }}</div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .page-break-after {
                page-break-after: always;
            }

            .btn,
            .breadcrumb,
            .d-print-none {
                display: none !important;
            }

            .card {
                border: 1px solid #ddd !important;
                shadow: none !important;
            }

            body {
                background: white !important;
            }
        }
    </style>
@endsection
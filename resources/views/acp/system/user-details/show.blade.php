@extends('layouts.app')

@section('title', __('messages.sm_user_full_profile'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a
                                href="{{ route('acp.user-mgmt.user-profiles.index') }}">{{ __('messages.sm_user_details') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('messages.full_profile') }}</li>
                    </ol>
                </nav>
                <h1 class="h3 mb-0">{{ $user->name }}</h1>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('acp.user-mgmt.users.edit', $user) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> {{ __('messages.edit') }}
                </a>
                <a href="{{ route('acp.user-mgmt.user-profiles.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('messages.back_to_directory') }}
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                    <div class="bg-primary py-5 text-center px-3">
                        @if($user->avatar)
                            <div class="mx-auto mb-2"
                                style="width:120px;height:120px;border-radius:50%;overflow:hidden;border:4px solid white;box-shadow:0 4px 12px rgba(0,0,0,0.15);">
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
                                    style="width:100%;height:100%;object-fit:cover;">
                            </div>
                        @else
                            <div class="avatar-xl bg-white-soft rounded-circle mx-auto d-flex align-items-center justify-content-center border border-white border-4 shadow"
                                style="width: 120px; height: 120px; background: rgba(255, 255, 255, 0.2);">
                                <span class="h1 mb-0 text-white">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="card-body text-center pt-0" style="margin-top: -60px;">
                        <div class="h5 mb-1 bg-white d-inline-block px-3 py-1 rounded shadow-sm border">{{ $user->name }}
                        </div>
                        <p class="text-muted small mb-3">{{ $user->email }}</p>
                        <div class="mb-4">
                            @if($user->is_active)
                                <span class="badge rounded-pill bg-success px-3">{{ __('messages.active_account') }}</span>
                            @else
                                <span class="badge rounded-pill bg-danger px-3">{{ __('messages.inactive_account') }}</span>
                            @endif
                        </div>

                        <div class="d-grid gap-2">
                            <div class="p-3 bg-light rounded text-start">
                                <div class="small text-muted mb-1">{{ __('messages.employee_code') }}</div>
                                <div class="fw-bold h6 mb-0">{{ $user->employee_code ?: '-' }}</div>
                            </div>
                            <div class="p-3 bg-light rounded text-start">
                                <div class="small text-muted mb-1">{{ __('messages.phone_number') }}</div>
                                <div class="fw-bold h6 mb-0">{{ $user->phone ?: '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Visiting Card --}}
                <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i
                                class="fas fa-id-card me-2 text-primary"></i>{{ __('messages.visiting_card') }}</h6>
                        <a href="{{ route('acp.user-mgmt.user-profiles.visiting-card-pdf', $user) }}"
                            class="btn btn-sm btn-danger" target="_blank">
                            <i class="fas fa-file-pdf me-1"></i> {{ __('messages.download_pdf') }}
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="visiting-card-preview p-4"
                            style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); min-height: 180px; position: relative; overflow: hidden;">
                            {{-- Decorative circles --}}
                            <div
                                style="position:absolute;top:-40px;right:-40px;width:150px;height:150px;border-radius:50%;background:rgba(255,255,255,0.04);">
                            </div>
                            <div
                                style="position:absolute;bottom:-20px;left:-20px;width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,0.06);">
                            </div>
                            <div class="d-flex flex-column h-100">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                                        style="width:52px;height:52px;background:rgba(255,255,255,0.15);border:2px solid rgba(255,255,255,0.3);">
                                        <span class="h4 mb-0 text-white fw-bold">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <div class="h5 mb-0 text-white fw-bold">{{ $user->name }}</div>
                                        @if($user->roles->isNotEmpty())
                                            <small class="text-white-50">{{ $user->roles->first()->name }}</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-auto d-flex flex-column gap-1">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-envelope me-2 text-white-50" style="font-size:11px;"></i>
                                        <span class="text-white-50" style="font-size:12px;">{{ $user->email }}</span>
                                    </div>
                                    @if($user->phone)
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-phone me-2 text-white-50" style="font-size:11px;"></i>
                                            <span class="text-white-50" style="font-size:12px;">{{ $user->phone }}</span>
                                        </div>
                                    @endif
                                    @if($user->company)
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-building me-2 text-white-50" style="font-size:11px;"></i>
                                            <span class="text-white-50"
                                                style="font-size:12px;">{{ $user->company->name }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold">{{ __('messages.organization_info') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small d-block">{{ __('messages.company') }}</label>
                            <span class="fw-medium">{{ $user->company->name ?? '-' }}</span>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small d-block">{{ __('messages.branch') }}</label>
                            <span class="fw-medium">{{ $user->branch->name ?? '-' }}</span>
                        </div>
                        <div class="mb-0">
                            <label class="text-muted small d-block">{{ __('messages.warehouses_access') }}</label>
                            <div class="d-flex flex-wrap gap-1 mt-1">
                                @forelse($user->warehouses as $wh)
                                    <span class="badge bg-secondary">{{ $wh->name }}</span>
                                @empty
                                    <span class="text-muted small italic">{{ __('messages.no_warehouses') }}</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold">{{ __('messages.roles_and_permissions') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label
                                class="text-muted small fw-bold text-uppercase d-block mb-2">{{ __('messages.assigned_roles') }}</label>
                            <div class="d-flex flex-wrap gap-2">
                                @forelse($user->roles as $role)
                                    <div class="btn btn-outline-info btn-sm rounded-pill px-3 py-1 cursor-default">
                                        <i class="fas fa-user-shield me-1"></i> {{ $role->name }}
                                    </div>
                                @empty
                                    <span class="text-muted small">{{ __('messages.no_roles_assigned') }}</span>
                                @endforelse
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label
                                class="text-muted small fw-bold text-uppercase mb-0">{{ __('messages.effective_permissions') }}</label>
                            <a href="{{ route('acp.user-mgmt.users.permissions', $user) }}"
                                class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size: 0.7rem;">
                                <i class="fas fa-edit me-1"></i> {{ __('messages.edit_permissions') }}
                            </a>
                        </div>
                        <div class="bg-light p-3 rounded" style="max-height: 400px; overflow-y: auto;">
                                <div class="row g-3">
                                    @php
                                        $allPermissions = $user->getAllPermissions()->groupBy(function ($p) {
                                            return explode(' ', $p->name)[1] ?? 'general';
                                        });
                                    @endphp
                                    @forelse($allPermissions as $group => $perms)
                                        <div class="col-md-6 mb-2">
                                            <div class="fw-bold small text-primary text-uppercase mb-1 border-bottom pb-1">
                                                {{ ucfirst($group) }}
                                            </div>
                                            <ul class="list-unstyled mb-0 small">
                                                @foreach($perms as $perm)
                                                    <li><i class="fas fa-check text-success me-2"></i>{{ $perm->name }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @empty
                                        <div class="col-12 text-center text-muted py-3">
                                            {{ __('messages.no_permissions_assigned') }}
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold">{{ __('messages.account_activity') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="p-3 border rounded">
                                    <span class="text-muted small d-block mb-1">{{ __('messages.last_login') }}</span>
                                    <span
                                        class="fw-bold">{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : __('messages.never') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded">
                                    <span class="text-muted small d-block mb-1">{{ __('messages.last_login_ip') }}</span>
                                    <span class="fw-bold"><code>{{ $user->last_login_ip ?: '-' }}</code></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded">
                                    <span class="text-muted small d-block mb-1">{{ __('messages.member_since') }}</span>
                                    <span class="fw-bold">{{ $user->created_at->format('Y-m-d') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded">
                                    <span class="text-muted small d-block mb-1">{{ __('messages.default_language') }}</span>
                                    <span class="fw-bold text-uppercase">{{ $user->default_language ?: 'en' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function printVisitingCard() {
            const cardEl = document.querySelector('.visiting-card-preview');
            if (!cardEl) return;

            const userName = @json($user->name);
            const userRole = @json(optional($user->roles->first())->name ?? '');
            const userEmail = @json($user->email);
            const userPhone = @json($user->phone ?? '');
            const userCompany = @json(optional($user->company)->name ?? '');
            const userInitial = userName.charAt(0);
            @if($user->avatar)
                const avatarUrl = @json(asset('storage/' . $user->avatar));
            @else
                const avatarUrl = null;
            @endif

            const avatarHtml = avatarUrl
                ? `<img src="${avatarUrl}" style="width:64px;height:64px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,0.5);">`
                : `<div style="width:64px;height:64px;border-radius:50%;background:rgba(255,255,255,0.2);border:2px solid rgba(255,255,255,0.4);display:flex;align-items:center;justify-content:center;font-size:26px;color:#fff;font-weight:700;">${userInitial}</div>`;

            const html = `<!DOCTYPE html>
        <html>
        <head>
        <meta charset="utf-8">
        <title>Visiting Card – ${userName}</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <style>
          * { margin:0; padding:0; box-sizing:border-box; }
          body { background:#f0f0f0; display:flex; align-items:center; justify-content:center; height:100vh; }
          @media print {
            body { background:white; }
            .card { box-shadow:none !important; }
          }
          .card {
            width: 90mm; min-height: 55mm;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            border-radius: 12px;
            padding: 20px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0,0,0,0.25);
            display: flex; flex-direction: column;
          }
          .deco1 { position:absolute;top:-40px;right:-40px;width:130px;height:130px;border-radius:50%;background:rgba(255,255,255,0.05); }
          .deco2 { position:absolute;bottom:-20px;left:-20px;width:90px;height:90px;border-radius:50%;background:rgba(255,255,255,0.06); }
          .top { display:flex; align-items:center; gap:14px; margin-bottom:14px; }
          .name { color:#fff; font-size:18px; font-weight:700; font-family:sans-serif; }
          .role { color:rgba(255,255,255,0.6); font-size:12px; font-family:sans-serif; margin-top:2px; }
          .info { display:flex; flex-direction:column; gap:6px; margin-top:auto; }
          .info-row { display:flex; align-items:center; gap:8px; color:rgba(255,255,255,0.65); font-size:11px; font-family:sans-serif; }
          .info-row i { font-size:11px; }
        </style>
        </head>
        <body>
        <div class="card">
          <div class="deco1"></div>
          <div class="deco2"></div>
          <div class="top">
            ${avatarHtml}
            <div>
              <div class="name">${userName}</div>
              ${userRole ? `<div class="role">${userRole}</div>` : ''}
            </div>
          </div>
          <div class="info">
            <div class="info-row"><i class="fas fa-envelope"></i> ${userEmail}</div>
            ${userPhone ? `<div class="info-row"><i class="fas fa-phone"></i> ${userPhone}</div>` : ''}
            ${userCompany ? `<div class="info-row"><i class="fas fa-building"></i> ${userCompany}</div>` : ''}
          </div>
        </div>
        <script>window.onload = function() { window.print(); window.onafterprint = function() { window.close(); }; };<\/script>
        </body>
        </html>`;

            const w = window.open('', '_blank', 'width=400,height=300,toolbar=0,menubar=0');
            w.document.write(html);
            w.document.close();
        }
    </script>
@endpush
@extends('layouts.app')

@section('title', __('messages.sm_user_header_config'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">{{ __('messages.sm_user_header_config') }}</h1>
                <p class="text-muted">{{ __('messages.sm_user_header_desc') }}</p>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">{{ __('messages.user') }}</th>
                                <th>{{ __('messages.header_title') }}</th>
                                <th>{{ __('messages.visibility_settings') }}</th>
                                <th>{{ __('messages.last_updated') }}</th>
                                <th class="pe-4 text-end">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                @php $setting = $settings->get($user->id); @endphp
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light text-primary rounded-circle me-3 d-flex align-items-center justify-content-center"
                                                style="width: 32px; height: 32px;">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $user->name }}</div>
                                                <div class="small text-muted">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($setting && $setting->header_title)
                                            <span class="fw-medium text-primary">{{ $setting->header_title }}</span>
                                        @else
                                            <span class="text-muted italic">{{ __('messages.default_username') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <span
                                                class="badge {{ ($setting->show_company ?? true) ? 'bg-success' : 'bg-light text-muted' }} small">CO</span>
                                            <span
                                                class="badge {{ ($setting->show_branch ?? true) ? 'bg-success' : 'bg-light text-muted' }} small">BR</span>
                                            <span
                                                class="badge {{ ($setting->show_date ?? true) ? 'bg-success' : 'bg-light text-muted' }} small">DT</span>
                                        </div>
                                    </td>
                                    <td>{{ $setting ? $setting->updated_at->diffForHumans() : '-' }}</td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('acp.system.user-header.edit', $user->id) }}"
                                            class="btn btn-sm btn-outline-primary rounded-pill">
                                            <i class="fas fa-cog me-1"></i> {{ __('messages.configure') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
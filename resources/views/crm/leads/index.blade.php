@extends('layouts.app')

@section('title', __('crm.leads'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('crm.leads') }}</h1>
            <div>
                @can('create crm leads')
                    <a href="{{ route('crm.leads.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> {{ __('crm.create_lead') }}
                    </a>
                @endcan
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body py-2">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center">
                            <span class="me-3 text-muted"><i class="fas fa-filter"></i>
                                {{ __('crm.filter_by_status') }}:</span>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('crm.leads.index', ['search' => request('search')]) }}"
                                    class="btn btn-outline-secondary {{ !request('status') ? 'active' : '' }}">
                                    {{ __('crm.all') }}
                                </a>
                                @foreach(['new', 'contacted', 'qualified', 'converted', 'lost'] as $status)
                                    <a href="{{ route('crm.leads.index', ['status' => $status, 'search' => request('search')]) }}"
                                        class="btn btn-outline-{{ $status === 'lost' ? 'danger' : ($status === 'converted' ? 'success' : 'info') }} {{ request('status') == $status ? 'active' : '' }}">
                                        {{ __('crm.status_' . $status) }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <form action="{{ route('crm.leads.index') }}" method="GET">
                            @if(request('status'))
                                <input type="hidden" name="status" value="{{ request('status') }}">
                            @endif
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" class="form-control"
                                    placeholder="{{ __('crm.search') }}..." value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">{{ __('crm.lead_name') }}</th>
                                <th>{{ __('crm.company') }}</th>
                                <th>{{ __('crm.email') }}/{{ __('crm.phone') }}</th>
                                <th>{{ __('crm.salesman') }}</th>
                                <th>{{ __('crm.status') }}</th>
                                <th>{{ __('crm.created_at') }}</th>
                                <th class="text-end pe-4">{{ __('crm.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leads as $lead)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-primary">{{ $lead->full_name }}</div>
                                        <small class="text-muted">{{ $lead->source }}</small>
                                    </td>
                                    <td>{{ $lead->company_name ?? '-' }}</td>
                                    <td>
                                        <div>{{ $lead->email }}</div>
                                        <small class="text-muted">{{ $lead->phone ?? $lead->mobile }}</small>
                                    </td>
                                    <td>{{ $lead->salesman->name ?? '-' }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match ($lead->status) {
                                                'new' => 'primary',
                                                'contacted' => 'info',
                                                'qualified' => 'warning',
                                                'converted' => 'success',
                                                'lost' => 'danger',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $badgeClass }} opacity-75">
                                            {{ __('crm.status_' . $lead->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $lead->created_at->format('Y-m-d') }}</td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <a href="{{ route('crm.leads.show', $lead) }}" class="btn btn-sm btn-outline-info"
                                                title="{{ __('crm.view') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('crm.leads.edit', $lead) }}"
                                                class="btn btn-sm btn-outline-primary" title="{{ __('crm.edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($lead->status !== 'converted')
                                                <form action="{{ route('crm.opportunities.create') }}" method="GET"
                                                    class="d-inline">
                                                    <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-success"
                                                        title="{{ __('crm.convert_to_opportunity') }}">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        {{ __('crm.no_records_found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-top">
                    {{ $leads->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
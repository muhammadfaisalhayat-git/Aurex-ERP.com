@extends('layouts.app')

@section('title', __('crm.opportunities'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('crm.opportunities') }}</h1>
            <div class="btn-group shadow-sm">
                <a href="{{ route('crm.pipeline.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-th-large"></i> {{ __('crm.kanban_view') }}
                </a>
                @can('create crm opportunities')
                    <a href="{{ route('crm.opportunities.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> {{ __('crm.create_opportunity') }}
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
                                {{ __('crm.filter_by_stage') }}:</span>
                            <div class="btn-group btn-group-sm overflow-auto">
                                <a href="{{ route('crm.opportunities.index', ['search' => request('search')]) }}"
                                    class="btn btn-outline-secondary {{ !request('stage_id') ? 'active' : '' }}">
                                    {{ __('crm.all') }}
                                </a>
                                @foreach($stages as $stage)
                                    <a href="{{ route('crm.opportunities.index', ['stage_id' => $stage->id, 'search' => request('search')]) }}"
                                        class="btn btn-outline-primary {{ request('stage_id') == $stage->id ? 'active' : '' }}">
                                        {{ $stage->name_en }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <form action="{{ route('crm.opportunities.index') }}" method="GET">
                            @if(request('stage_id'))
                                <input type="hidden" name="stage_id" value="{{ request('stage_id') }}">
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
                                <th class="ps-4">{{ __('crm.opportunity_title') }}</th>
                                <th>{{ __('crm.lead_customer') }}</th>
                                <th>{{ __('crm.expected_revenue') }}</th>
                                <th>{{ __('crm.closing_date') }}</th>
                                <th>{{ __('crm.stage') }}</th>
                                <th>{{ __('crm.probability') }}</th>
                                <th class="text-end pe-4">{{ __('crm.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($opportunities as $opportunity)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-primary">{{ $opportunity->title }}</div>
                                        <small class="text-muted">{{ $opportunity->salesman->name ?? '-' }}</small>
                                    </td>
                                    <td>
                                        @if($opportunity->customer_id)
                                            <i class="fas fa-user-circle text-success me-1"></i> {{ $opportunity->customer->name }}
                                        @elseif($opportunity->lead_id)
                                            <i class="fas fa-user-circle text-primary me-1"></i> {{ $opportunity->lead->full_name }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="fw-bold">{{ number_format($opportunity->expected_revenue, 2) }}</td>
                                    <td>{{ $opportunity->expected_closing ? $opportunity->expected_closing->format('Y-m-d') : '-' }}
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $opportunity->stage->color }}">
                                            {{ $opportunity->stage->name_en }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                <div class="progress-bar" role="progressbar"
                                                    style="width: {{ $opportunity->probability }}%; background-color: {{ $opportunity->stage->color }}">
                                                </div>
                                            </div>
                                            <small class="fw-bold">{{ $opportunity->probability }}%</small>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <a href="{{ route('crm.opportunities.show', $opportunity) }}"
                                                class="btn btn-sm btn-outline-info" title="{{ __('crm.view') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('crm.opportunities.edit', $opportunity) }}"
                                                class="btn btn-sm btn-outline-primary" title="{{ __('crm.edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
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
                    {{ $opportunities->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
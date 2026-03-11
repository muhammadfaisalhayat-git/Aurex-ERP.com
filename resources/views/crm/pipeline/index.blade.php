@extends('layouts.app')

@section('title', __('crm.pipeline_kanban'))

@push('styles')
    <style>
        .kanban-container {
            display: flex;
            overflow-x: auto;
            padding-bottom: 20px;
            min-height: calc(100vh - 200px);
        }

        .kanban-column {
            min-width: 320px;
            max-width: 320px;
            margin-right: 1.5rem;
            background: #f8fafc;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            border: 1px solid #e2e8f0;
        }

        .kanban-header {
            padding: 1.25rem;
            background: #fff;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        .kanban-items {
            flex-grow: 1;
            padding: 1rem;
            min-height: 100px;
        }

        .kanban-card {
            background: #fff;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            cursor: grab;
            border-left: 4px solid transparent;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .kanban-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .kanban-card:active {
            cursor: grabbing;
        }

        .sortable-ghost {
            opacity: 0.4;
            background: #e2e8f0;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('crm.pipeline_kanban') }}</h1>
            <div class="d-flex gap-2">
                <a href="{{ route('crm.opportunities.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-list"></i> {{ __('crm.list_view') }}
                </a>
                @can('create crm opportunities')
                    <a href="{{ route('crm.opportunities.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> {{ __('crm.create_opportunity') }}
                    </a>
                @endcan
            </div>
        </div>

        <div class="kanban-container">
            @foreach($stages as $stage)
                <div class="kanban-column" data-stage-id="{{ $stage->id }}">
                    <div class="kanban-header">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0" style="color: {{ $stage->color }}">{{ $stage->name_en }}</h6>
                            <span class="badge bg-light text-dark border">{{ $stage->opportunities_count }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">{{ __('crm.total_revenue') }}:</small>
                            <span
                                class="fw-bold text-primary">{{ number_format($stage->opportunities_sum_expected_revenue ?? 0, 2) }}</span>
                        </div>
                    </div>

                    <div class="kanban-items" id="stage-{{ $stage->id }}">
                        @foreach($stage->opportunities as $opportunity)
                            <div class="kanban-card" data-id="{{ $opportunity->id }}"
                                style="border-left-color: {{ $stage->color }}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-bold mb-0 small">
                                        <a href="{{ route('crm.opportunities.show', $opportunity) }}"
                                            class="text-decoration-none text-dark">
                                            {{ $opportunity->title }}
                                        </a>
                                    </h6>
                                    <div class="dropdown">
                                        <i class="fas fa-ellipsis-v text-muted btn-link" role="button"
                                            data-bs-toggle="dropdown"></i>
                                        <ul class="dropdown-content dropdown-menu dropdown-menu-end shadow border-0">
                                            <li><a class="dropdown-item"
                                                    href="{{ route('crm.opportunities.show', $opportunity) }}"><i
                                                        class="fas fa-eye me-2 text-info"></i>{{ __('crm.view') }}</a></li>
                                            <li><a class="dropdown-item"
                                                    href="{{ route('crm.opportunities.edit', $opportunity) }}"><i
                                                        class="fas fa-edit me-2 text-primary"></i>{{ __('crm.edit') }}</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <span class="small text-muted">
                                        @if($opportunity->customer_id)
                                            <i class="fas fa-user-tie me-1"></i> {{ $opportunity->customer->name }}
                                        @elseif($opportunity->lead_id)
                                            <i class="fas fa-user-tag me-1"></i> {{ $opportunity->lead->full_name }}
                                        @endif
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-primary small">{{ number_format($opportunity->expected_revenue, 0) }}
                                        {{ config('app.currency', 'SAR') }}</span>
                                    <span class="text-muted" style="font-size: 0.75rem;">
                                        <i class="far fa-clock me-1"></i>
                                        {{ $opportunity->expected_closing ? $opportunity->expected_closing->format('M d') : '-' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const columns = document.querySelectorAll('.kanban-items');

            columns.forEach(column => {
                new Sortable(column, {
                    group: 'kanban',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    onEnd: function (evt) {
                        const opportunityId = evt.item.getAttribute('data-id');
                        const newStageId = evt.to.closest('.kanban-column').getAttribute('data-stage-id');

                        if (evt.from !== evt.to) {
                            updateOpportunityStage(opportunityId, newStageId);
                        }
                    }
                });
            });

            function updateOpportunityStage(id, stageId) {
                fetch(`{{ url('crm/pipeline/update-stage') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        opportunity_id: id,
                        stage_id: stageId
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            toastr.success(data.message);
                            // Optionally refresh stage totals here via AJAX
                        } else {
                            toastr.error(data.message || 'Error updating stage');
                            location.reload(); // Revert on error
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        toastr.error('System error occurred');
                        location.reload();
                    });
            }
        });
    </script>
@endpush
@extends('layouts.app')

@section('title', __('crm.opportunity_details'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('crm.opportunity_details') }}: {{ $opportunity->title }}</h1>
            <div>
                <a href="{{ route('crm.opportunities.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left"></i> {{ __('crm.back_to_opportunities') }}
                </a>
                <a href="{{ route('crm.opportunities.edit', $opportunity) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> {{ __('crm.edit') }}
                </a>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column: Details -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title mb-0">{{ __('crm.opportunity_summary') }}</h5>
                            <span class="badge px-3 py-2" style="background-color: {{ $opportunity->stage->color }}">
                                {{ $opportunity->stage->name_en }}
                            </span>
                        </div>

                        <div class="mb-4 text-center py-3 bg-light rounded">
                            <h3 class="mb-1 text-primary fw-bold">{{ number_format($opportunity->expected_revenue, 2) }}
                            </h3>
                            <p class="text-muted small mb-0">{{ __('crm.expected_revenue') }}
                                ({{ config('app.currency', 'SAR') }})</p>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">{{ __('crm.expected_closing') }}</span>
                                <span
                                    class="fw-bold small">{{ $opportunity->expected_closing ? $opportunity->expected_closing->format('Y-m-d') : '-' }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted small">{{ __('crm.probability') }}</span>
                                <span class="fw-bold small">{{ $opportunity->probability }}%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" role="progressbar"
                                    style="width: {{ $opportunity->probability }}%; background-color: {{ $opportunity->stage->color }}">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <h6 class="text-uppercase text-muted fw-bold small mb-3">{{ __('crm.linked_entities') }}</h6>
                            @if($opportunity->customer_id)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                        style="width: 32px; height: 32px;">
                                        <i class="fas fa-user-tie small"></i>
                                    </div>
                                    <div>
                                        <div class="small text-muted">{{ __('crm.customer') }}</div>
                                        <div class="fw-bold">{{ $opportunity->customer->name }}</div>
                                    </div>
                                </div>
                            @endif
                            @if($opportunity->lead_id)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                        style="width: 32px; height: 32px;">
                                        <i class="fas fa-user-tag small"></i>
                                    </div>
                                    <div>
                                        <div class="small text-muted">{{ __('crm.lead') }}</div>
                                        <div class="fw-bold">{{ $opportunity->lead->full_name }}</div>
                                    </div>
                                </div>
                            @endif
                            @if(!$opportunity->customer_id && !$opportunity->lead_id)
                                <p class="text-muted small italic">{{ __('crm.no_entities_linked') }}</p>
                            @endif
                        </div>

                        <div>
                            <h6 class="text-uppercase text-muted fw-bold small mb-3">{{ __('crm.assignment') }}</h6>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-user-tie text-primary me-3" style="width: 16px;"></i>
                                <span class="small">{{ $opportunity->salesman->name ?? '-' }}</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-building text-primary me-3" style="width: 16px;"></i>
                                <span class="small">{{ $opportunity->branch->name ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Timeline & Notes -->
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-transparent border-0 pt-3 ps-4">
                        <ul class="nav nav-tabs card-header-tabs" id="opportunityTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active fw-bold" id="activities-tab" data-bs-toggle="tab"
                                    href="#activities" role="tab">{{ __('crm.activities') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-bold" id="details-tab" data-bs-toggle="tab" href="#details"
                                    role="tab">{{ __('crm.description') }}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-4">
                        <div class="tab-content" id="opportunityTabsContent">
                            <!-- Activities Tab -->
                            <div class="tab-pane fade show active" id="activities" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="mb-0">{{ __('crm.planned_activities') }}</h5>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#addActivityModal">
                                        <i class="fas fa-calendar-plus me-1"></i> {{ __('crm.schedule_activity') }}
                                    </button>
                                </div>

                                <div class="timeline">
                                    @forelse($opportunity->activities as $activity)
                                        <div class="d-flex mb-4">
                                            <div class="flex-shrink-0">
                                                <div class="bg-{{ $activity->status === 'completed' ? 'success' : 'primary' }} text-white rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px;">
                                                    <i
                                                        class="fas fa-{{ $activity->status === 'completed' ? 'check' : 'hourglass-half' }}"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3 border-bottom pb-3">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <h6 class="mb-0 fw-bold">{{ $activity->activity_type }}:
                                                        {{ $activity->summary }}</h6>
                                                    <small class="text-muted">{{ $activity->due_date->format('Y-m-d') }}</small>
                                                </div>
                                                <p class="mb-2 text-body small text-muted">
                                                    {{ $activity->feedback ?? __('crm.no_feedback_yet') }}</p>
                                                <div class="d-flex gap-2">
                                                    @if($activity->status === 'pending')
                                                        <form action="{{ route('crm.activities.update', $activity) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="completed">
                                                            <button type="submit"
                                                                class="btn btn-xs btn-success">{{ __('crm.mark_done') }}</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-5">
                                            <i class="fas fa-tasks fa-3x text-light mb-3"></i>
                                            <p class="text-muted">{{ __('crm.no_activities_scheduled') }}</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Details Tab -->
                            <div class="tab-pane fade" id="details" role="tabpanel">
                                <h5 class="mb-3">{{ __('crm.full_description') }}</h5>
                                <div class="bg-light p-4 rounded border text-body">
                                    {!! nl2br(e($opportunity->description ?? __('crm.no_description_available'))) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Scheduling Activity -->
    <div class="modal fade" id="addActivityModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('crm.activities.store') }}" method="POST">
                @csrf
                <input type="hidden" name="activitable_type" value="CrmOpportunity">
                <input type="hidden" name="activitable_id" value="{{ $opportunity->id }}">
                <input type="hidden" name="status" value="pending">
                <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                <div class="modal-content text-body">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('crm.schedule_activity') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('crm.activity_type') }}</label>
                            <select name="activity_type" class="form-select" required>
                                <option value="Email">{{ __('crm.email') }}</option>
                                <option value="Call">{{ __('crm.call') }}</option>
                                <option value="Meeting">{{ __('crm.meeting') }}</option>
                                <option value="Follow-up">{{ __('crm.follow_up') }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('crm.summary') }}</label>
                            <input type="text" name="summary" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('crm.due_date') }}</label>
                            <input type="date" name="due_date" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('crm.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('crm.save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
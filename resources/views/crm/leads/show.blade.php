@extends('layouts.app')

@section('title', __('crm.lead_details'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('crm.lead_details') }}: {{ $lead->full_name }}</h1>
            <div>
                <a href="{{ route('crm.leads.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left"></i> {{ __('crm.back_to_leads') }}
                </a>
                <a href="{{ route('crm.leads.edit', $lead) }}" class="btn btn-primary me-2">
                    <i class="fas fa-edit"></i> {{ __('crm.edit') }}
                </a>
                @if($lead->status !== 'converted')
                    <form action="{{ route('crm.opportunities.create') }}" method="GET" class="d-inline">
                        <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-exchange-alt"></i> {{ __('crm.convert_to_opportunity') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="row g-4">
            <!-- Summary Info -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 mb-4 h-100">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="avatar avatar-xl bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                                style="width: 80px; height: 80px; font-size: 2rem;">
                                {{ strtoupper(substr($lead->first_name, 0, 1)) }}{{ strtoupper(substr($lead->last_name ?? '', 0, 1)) }}
                            </div>
                            <h4 class="mb-1">{{ $lead->full_name }}</h4>
                            <p class="text-muted mb-3">{{ $lead->company_name ?? __('crm.individual') }}</p>

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
                            <span class="badge bg-{{ $badgeClass }} px-3 py-2 fs-6">
                                {{ __('crm.status_' . $lead->status) }}
                            </span>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <h6 class="text-uppercase text-muted fw-bold small mb-3">{{ __('crm.contact_info') }}</h6>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-envelope text-primary me-3" style="width: 20px;"></i>
                                <span class="text-body">{{ $lead->email ?? '-' }}</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-phone text-primary me-3" style="width: 20px;"></i>
                                <span class="text-body">{{ $lead->phone ?? '-' }}</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-map-marker-alt text-primary me-3" style="width: 20px;"></i>
                                <span class="text-body">{{ $lead->address ?? '-' }}</span>
                            </div>
                        </div>

                        <div>
                            <h6 class="text-uppercase text-muted fw-bold small mb-3">{{ __('crm.assignment') }}</h6>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-user-tie text-primary me-3" style="width: 20px;"></i>
                                <span class="text-body">{{ $lead->salesman->name ?? '-' }}</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-building text-primary me-3" style="width: 20px;"></i>
                                <span class="text-body">{{ $lead->branch->name ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Tabs -->
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-transparent border-0 pt-3 ps-4">
                        <ul class="nav nav-tabs card-header-tabs" id="leadTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active fw-bold" id="activities-tab" data-bs-toggle="tab"
                                    href="#activities" role="tab">{{ __('crm.activities') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-bold" id="opportunities-tab" data-bs-toggle="tab"
                                    href="#opportunities" role="tab">{{ __('crm.opportunities') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-bold" id="notes-tab" data-bs-toggle="tab" href="#notes"
                                    role="tab">{{ __('crm.notes') }}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-4">
                        <div class="tab-content" id="leadTabsContent">
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
                                    @forelse($lead->activities as $activity)
                                        <div class="d-flex mb-4">
                                            <div class="flex-shrink-0">
                                                <div class="bg-{{ $activity->status === 'completed' ? 'success' : 'primary' }} text-white rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px;">
                                                    <i class="fas fa-check"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3 border-bottom pb-3">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <h6 class="mb-0 fw-bold">{{ $activity->activity_type }}</h6>
                                                    <small class="text-muted">{{ $activity->due_date->format('Y-m-d') }}</small>
                                                </div>
                                                <p class="mb-2 text-body">{{ $activity->summary }}</p>
                                                @if($activity->feedback)
                                                    <div class="alert alert-light py-2 px-3 mb-0 small border">
                                                        <strong>{{ __('crm.feedback') }}:</strong> {{ $activity->feedback }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4">
                                            <i class="fas fa-calendar-day fa-3x text-light mb-3"></i>
                                            <p class="text-muted">{{ __('crm.no_activities_scheduled') }}</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Opportunities Tab -->
                            <div class="tab-pane fade" id="opportunities" role="tabpanel">
                                <h5 class="mb-4">{{ __('crm.linked_opportunities') }}</h5>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>{{ __('crm.opportunity_title') }}</th>
                                                <th>{{ __('crm.expected_revenue') }}</th>
                                                <th>{{ __('crm.stage') }}</th>
                                                <th>{{ __('crm.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($lead->opportunities as $opportunity)
                                                <tr>
                                                    <td>{{ $opportunity->title }}</td>
                                                    <td>{{ number_format($opportunity->expected_revenue, 2) }}</td>
                                                    <td>
                                                        <span class="badge"
                                                            style="background-color: {{ $opportunity->stage->color }}">
                                                            {{ $opportunity->stage->name_en }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('crm.opportunities.show', $opportunity) }}"
                                                            class="btn btn-sm btn-outline-info">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-4 text-muted">
                                                        {{ __('crm.no_opportunities_found') }}
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Notes Tab -->
                            <div class="tab-pane fade" id="notes" role="tabpanel">
                                <h5 class="mb-3">{{ __('crm.additional_notes') }}</h5>
                                <div class="bg-light p-4 rounded border">
                                    {!! nl2br(e($lead->notes ?? __('crm.no_notes_available'))) !!}
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
                <input type="hidden" name="activitable_type" value="CrmLead">
                <input type="hidden" name="activitable_id" value="{{ $lead->id }}">
                <input type="hidden" name="status" value="pending">
                <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('crm.schedule_activity') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('crm.activity_type') }}</label>
                            <select name="activity_type" class="form-select" required>
                                <option value="Email">{{ __('crm.email') }}</option>
                                <option value="Call">{{ __('crm.call') }}</option>
                                <option value="Meeting">{{ __('crm.meeting') }}</option>
                                <option value="Follow-up">{{ __('crm.follow_up') }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('crm.summary') }}</label>
                            <input type="text" name="summary" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('crm.due_date') }}</label>
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
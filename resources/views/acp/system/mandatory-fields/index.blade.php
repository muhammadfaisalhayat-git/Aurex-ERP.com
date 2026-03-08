@extends('layouts.app')

@section('title', __('messages.sm_mandatory_fields'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">{{ __('messages.sm_mandatory_fields') }}</h1>
                <p class="text-muted">{{ __('messages.sm_enforcement_desc') }}</p>
            </div>
        </div>

        <!-- Module Switcher -->
        <div class="card mb-4 shadow-sm border-0 bg-primary-soft" style="background: rgba(var(--bs-primary-rgb), 0.05);">
            <div class="card-body">
                <form action="{{ route('acp.system.mandatory-fields.index') }}" method="GET"
                    class="d-flex align-items-center gap-3">
                    <label class="fw-bold mb-0 text-nowrap">{{ __('messages.select_module_to_configure') }}:</label>
                    <select name="module" class="form-select w-auto" onchange="this.form.submit()">
                        @foreach($modules as $module)
                            <option value="{{ $module }}" {{ $selectedModule == $module ? 'selected' : '' }}>{{ $module }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">{{ $selectedModule }} {{ __('messages.fields_configuration') }}</h5>
                        <span class="badge bg-light text-muted">{{ $fields->count() }}
                            {{ __('messages.fields_detected') }}</span>
                    </div>
                    <div class="card-body p-0">
                        <form action="{{ route('acp.system.mandatory-fields.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="ps-4">{{ __('messages.field_label') }}</th>
                                            <th>{{ __('messages.internal_name') }}</th>
                                            <th class="text-center">{{ __('messages.mandatory') }}</th>
                                            <th class="pe-4 text-center">{{ __('messages.status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($fields as $index => $field)
                                            <tr>
                                                <td class="ps-4 fw-bold">
                                                    <input type="hidden" name="fields[{{ $index }}][id]"
                                                        value="{{ $field->id }}">
                                                    {{ $field->field_label }}
                                                </td>
                                                <td><code>{{ $field->field_name }}</code></td>
                                                <td class="text-center">
                                                    <div class="form-check form-switch d-inline-block">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="fields[{{ $index }}][is_mandatory]" value="1" {{ $field->is_mandatory ? 'checked' : '' }}>
                                                    </div>
                                                </td>
                                                <td class="pe-4 text-center">
                                                    @if($field->is_active)
                                                        <span class="badge bg-success-soft text-success rounded-pill"
                                                            style="font-size: 10px;">ACTIVE</span>
                                                    @else
                                                        <span class="badge bg-secondary-soft text-secondary rounded-pill"
                                                            style="font-size: 10px;">INACTIVE</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer bg-white py-3 border-0 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                    <i class="fas fa-save me-1"></i> {{ __('messages.save_configuration') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm bg-info text-white mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><i class="fas fa-info-circle me-1"></i> {{ __('messages.important_note') }}
                        </h6>
                        <p class="small mb-0">{{ __('messages.mandatory_fields_hint') }}</p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold">{{ __('messages.field_analysis') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2 small">
                            <span>{{ __('messages.total_fields') }}</span>
                            <span class="fw-bold">{{ $fields->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span>{{ __('messages.currently_mandatory') }}</span>
                            <span class="fw-bold text-danger">{{ $fields->where('is_mandatory', true)->count() }}</span>
                        </div>
                        <div class="progress mt-3" style="height: 6px;">
                            @php $percent = $fields->count() > 0 ? ($fields->where('is_mandatory', true)->count() / $fields->count()) * 100 : 0; @endphp
                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
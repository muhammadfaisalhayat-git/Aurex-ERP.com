@extends('layouts.app')

@section('title', __('messages.experience_letters'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.experience_letters') }}</h1>
        </div>

        <div class="card glassy">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('messages.employee') }}</th>
                                <th>{{ __('messages.department') }}</th>
                                <th>{{ __('messages.designation') }}</th>
                                <th>{{ __('messages.joining_date') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th class="text-center">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $employee)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3 text-info">
                                                <i class="fas fa-id-badge fa-2x"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $employee->name }}</div>
                                                <small class="text-muted">{{ $employee->employee_code }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $employee->department->name ?? '-' }}</td>
                                    <td>{{ $employee->designation->name ?? '-' }}</td>
                                    <td>{{ $employee->joining_date ? $employee->joining_date->format('Y-m-d') : '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $employee->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ __('messages.' . $employee->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="input-group input-group-sm justify-content-center">
                                            <select class="form-select flex-grow-0" style="width: 120px;"
                                                id="template_{{ $employee->id }}">
                                                <option value="classic">Classic</option>
                                                <option value="modern">Modern</option>
                                                <option value="elegant">Elegant</option>
                                                <option value="minimalist">Minimalist</option>
                                                <option value="executive">Executive</option>
                                            </select>
                                            <button class="btn btn-outline-success" type="button"
                                                onclick="issueLetter({{ $employee->id }}, '{{ route('hr.experience.show', $employee) }}')">
                                                <i class="fas fa-file-contract"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        {{ __('messages.no_records_found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $employees->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function issueLetter(employeeId, baseUrl) {
            const template = document.getElementById('template_' + employeeId).value;
            const url = `${baseUrl}?template=${template}`;
            window.open(url, '_blank');
        }
    </script>
@endpush
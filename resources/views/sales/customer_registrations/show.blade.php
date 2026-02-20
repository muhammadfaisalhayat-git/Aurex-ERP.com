@extends('layouts.app')

@section('title', __('customer_registration.view'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('customer_registration.view') }}</h1>
            <div>
                <a href="{{ route('sales.customer-registrations.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> {{ __('general.back') }}
                </a>
                @if($customerRegistration->status === 'pending')
                    @can('customer_registration.edit')
                        <a href="{{ route('sales.customer-registrations.edit', $customerRegistration) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> {{ __('general.edit') }}
                        </a>
                    @endcan
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $customerRegistration->registration_number }}</h5>
                        @php
                            $statusClass = [
                                'pending' => 'warning',
                                'under_review' => 'info',
                                'approved' => 'success',
                                'active' => 'success',
                                'rejected' => 'danger',
                            ][$customerRegistration->status] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $statusClass }}">
                            {{ __('customer_registration.status_' . ($customerRegistration->status === 'active' ? 'approved' : $customerRegistration->status)) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>{{ $customerRegistration->customer_type === 'individual' ? __('customer_registration.personal_info') : __('customer_registration.company_info') }}</h6>
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td class="fw-bold">{{ __('customer_registration.customer_type') }}</td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ __('customer_registration.' . $customerRegistration->customer_type) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ $customerRegistration->customer_type === 'individual' ? __('customer_registration.person_name') : __('customer_registration.company_name') }}</td>
                                        <td>{{ $customerRegistration->name_en }}</td>
                                    </tr>
                                    @if($customerRegistration->customer_type !== 'individual')
                                    <tr>
                                        <td class="fw-bold">{{ __('customer_registration.contact_person') }}</td>
                                        <td>{{ $customerRegistration->contact_person }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td class="fw-bold">{{ __('customer_registration.email') }}</td>
                                        <td>{{ $customerRegistration->email }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('customer_registration.phone') }}</td>
                                        <td>{{ $customerRegistration->phone }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('customer_registration.mobile') }}</td>
                                        <td>{{ $customerRegistration->mobile ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>{{ __('customer_registration.address_info') }}</h6>
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td class="fw-bold">{{ __('customer_registration.address') }}</td>
                                        <td>{{ $customerRegistration->address }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('customer_registration.city') }}</td>
                                        <td>{{ $customerRegistration->city }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('customer_registration.country') }}</td>
                                        <td>{{ $customerRegistration->country }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('customer_registration.postal_code') }}</td>
                                        <td>{{ $customerRegistration->postal_code ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <h6>{{ __('customer_registration.business_info') }}</h6>
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td class="fw-bold">{{ __('customer_registration.tax_number') }}</td>
                                        <td>{{ $customerRegistration->tax_number ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('customer_registration.registration_number') }}</td>
                                        <td>{{ $customerRegistration->commercial_registration ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('customer_registration.business_type') }}</td>
                                        <td>{{ $customerRegistration->business_type ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('customer_registration.website') }}</td>
                                        <td>{!! $customerRegistration->website ? '<a href="' . $customerRegistration->website . '" target="_blank">' . $customerRegistration->website . '</a>' : '-' !!}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>{{ __('customer_registration.credit_info') }}</h6>
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td class="fw-bold">{{ __('customer_registration.credit_limit') }}</td>
                                        <td>{{ $customerRegistration->credit_limit ? number_format($customerRegistration->credit_limit, 2) : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('customer_registration.payment_terms') }}</td>
                                        <td>{{ $customerRegistration->payment_terms ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($customerRegistration->notes)
                            <hr>
                            <h6>{{ __('customer_registration.notes') }}</h6>
                            <p>{{ $customerRegistration->notes }}</p>
                        @endif

                        @if($customerRegistration->rejection_reason)
                            <hr>
                            <div class="alert alert-danger">
                                <h6>{{ __('customer_registration.rejection_reason') }}</h6>
                                <p>{{ $customerRegistration->rejection_reason }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                @if($customerRegistration->documents->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('customer_registration.documents') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>{{ __('customer_registration.document_name') }}</th>
                                            <th>{{ __('customer_registration.document_type') }}</th>
                                            <th>{{ __('customer_registration.file_size') }}</th>
                                            <th>{{ __('general.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($customerRegistration->documents as $document)
                                            <tr>
                                                <td>{{ $document->document_name }}</td>
                                                <td>{{ $document->document_type }}</td>
                                                <td>{{ number_format($document->file_size / 1024, 2) }} KB</td>
                                                <td>
                                                    <a href="{{ Storage::url($document->file_path) }}" target="_blank"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    @if($customerRegistration->status === 'pending')
                                                        @can('customer_registration.edit')
                                                            <form
                                                                action="{{ route('sales.customer-registrations.documents.delete', $document) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger"
                                                                    onclick="return confirm('{{ __('general.confirm_delete') }}')">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endcan
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('customer_registration.audit_info') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="fw-bold">{{ __('customer_registration.created_by') }}</td>
                                <td>{{ $customerRegistration->creator->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">{{ __('customer_registration.created_at') }}</td>
                                <td>{{ $customerRegistration->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                            @if($customerRegistration->reviewed_by)
                                <tr>
                                    <td class="fw-bold">{{ __('customer_registration.approved_by') }}</td>
                                    <td>{{ $customerRegistration->reviewer->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">{{ __('customer_registration.approved_at') }}</td>
                                    <td>{{ $customerRegistration->reviewed_at?->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                @if($customerRegistration->status === 'pending' || $customerRegistration->status === 'under_review')
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('customer_registration.approval_actions') }}</h5>
                        </div>
                        <div class="card-body">
                            @can('customer_registration.approve')
                                <form action="{{ route('sales.customer-registrations.approve', $customerRegistration) }}"
                                    method="POST" class="mb-3">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="approval_notes"
                                            class="form-label">{{ __('customer_registration.approval_notes') }}</label>
                                        <textarea class="form-control" id="approval_notes" name="approval_notes"
                                            rows="2"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100"
                                        onclick="return confirm('{{ __('customer_registration.confirm_approve') }}')">
                                        <i class="fas fa-check"></i> {{ __('customer_registration.approve') }}
                                    </button>
                                </form>

                                <form action="{{ route('sales.customer-registrations.reject', $customerRegistration) }}"
                                    method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="rejection_reason"
                                            class="form-label">{{ __('customer_registration.rejection_reason') }} *</label>
                                        <textarea class="form-control @error('rejection_reason') is-invalid @enderror"
                                            id="rejection_reason" name="rejection_reason" rows="2" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-danger w-100"
                                        onclick="return confirm('{{ __('customer_registration.confirm_reject') }}')">
                                        <i class="fas fa-times"></i> {{ __('customer_registration.reject') }}
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
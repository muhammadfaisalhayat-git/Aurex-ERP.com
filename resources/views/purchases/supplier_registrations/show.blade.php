@extends('layouts.app')

@section('title', __('supplier_registration.view'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('supplier_registration.view') }}</h1>
            <div>
                <a href="{{ route('purchases.supplier-registrations.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> {{ __('general.back') }}
                </a>
                @if($supplierRegistration->status === 'pending')
                    @can('supplier_registration.edit')
                        <a href="{{ route('purchases.supplier-registrations.edit', $supplierRegistration) }}"
                            class="btn btn-primary">
                            <i class="fas fa-edit"></i> {{ __('general.edit') }}
                        </a>
                    @endcan
                @endif
                @if($supplierRegistration->status === 'approved' && !$supplierRegistration->converted_vendor_id)
                    @can('supplier_registration.approve')
                        <form action="{{ route('purchases.supplier-registrations.convert', $supplierRegistration) }}" method="POST"
                            class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success"
                                onclick="return confirm('{{ __('supplier_registration.confirm_convert') }}')">
                                <i class="fas fa-exchange-alt"></i> {{ __('supplier_registration.convert_to_vendor') }}
                            </button>
                        </form>
                    @endcan
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $supplierRegistration->registration_code }}</h5>
                        @php
                            $statusClass = [
                                'pending' => 'warning',
                                'under_review' => 'info',
                                'approved' => 'success',
                                'rejected' => 'danger',
                            ][$supplierRegistration->status];
                        @endphp
                        <span class="badge bg-{{ $statusClass }}">
                            {{ __('supplier_registration.status_' . $supplierRegistration->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>{{ __('supplier_registration.company_info') }}</h6>
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td class="fw-bold">{{ __('supplier_registration.company_name') }}</td>
                                        <td>{{ $supplierRegistration->company_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('supplier_registration.contact_person') }}</td>
                                        <td>{{ $supplierRegistration->contact_person }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('supplier_registration.email') }}</td>
                                        <td>{{ $supplierRegistration->email }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('supplier_registration.phone') }}</td>
                                        <td>{{ $supplierRegistration->phone }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('supplier_registration.mobile') }}</td>
                                        <td>{{ $supplierRegistration->mobile ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>{{ __('supplier_registration.address_info') }}</h6>
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td class="fw-bold">{{ __('supplier_registration.address') }}</td>
                                        <td>{{ $supplierRegistration->address }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('supplier_registration.city') }}</td>
                                        <td>{{ $supplierRegistration->city }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('supplier_registration.country') }}</td>
                                        <td>{{ $supplierRegistration->country }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('supplier_registration.postal_code') }}</td>
                                        <td>{{ $supplierRegistration->postal_code ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <h6>{{ __('supplier_registration.business_info') }}</h6>
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td class="fw-bold">{{ __('supplier_registration.tax_number') }}</td>
                                        <td>{{ $supplierRegistration->tax_number ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('supplier_registration.registration_number') }}</td>
                                        <td>{{ $supplierRegistration->registration_number ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('supplier_registration.business_type') }}</td>
                                        <td>{{ $supplierRegistration->business_type ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('supplier_registration.website') }}</td>
                                        <td>{{ $supplierRegistration->website ? '<a href="' . $supplierRegistration->website . '" target="_blank">' . $supplierRegistration->website . '</a>' : '-' }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>{{ __('supplier_registration.bank_info') }}</h6>
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td class="fw-bold">{{ __('supplier_registration.bank_name') }}</td>
                                        <td>{{ $supplierRegistration->bank_name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('supplier_registration.bank_account') }}</td>
                                        <td>{{ $supplierRegistration->bank_account ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('supplier_registration.iban') }}</td>
                                        <td>{{ $supplierRegistration->iban ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($supplierRegistration->products_services)
                            <hr>
                            <h6>{{ __('supplier_registration.products_services') }}</h6>
                            <p>{{ $supplierRegistration->products_services }}</p>
                        @endif

                        @if($supplierRegistration->notes)
                            <hr>
                            <h6>{{ __('supplier_registration.notes') }}</h6>
                            <p>{{ $supplierRegistration->notes }}</p>
                        @endif

                        @if($supplierRegistration->rejection_reason)
                            <hr>
                            <div class="alert alert-danger">
                                <h6>{{ __('supplier_registration.rejection_reason') }}</h6>
                                <p>{{ $supplierRegistration->rejection_reason }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                @if($supplierRegistration->documents->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('supplier_registration.documents') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>{{ __('supplier_registration.document_name') }}</th>
                                            <th>{{ __('supplier_registration.document_type') }}</th>
                                            <th>{{ __('supplier_registration.file_size') }}</th>
                                            <th>{{ __('general.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($supplierRegistration->documents as $document)
                                            <tr>
                                                <td>{{ $document->document_name }}</td>
                                                <td>{{ $document->document_type }}</td>
                                                <td>{{ number_format($document->file_size / 1024, 2) }} KB</td>
                                                <td>
                                                    <a href="{{ Storage::url($document->document_path) }}" target="_blank"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    @if($supplierRegistration->status === 'pending')
                                                        @can('supplier_registration.edit')
                                                            <form
                                                                action="{{ route('purchases.supplier-registrations.documents.delete', $document) }}"
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
                        <h5 class="mb-0">{{ __('supplier_registration.audit_info') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="fw-bold">{{ __('supplier_registration.created_by') }}</td>
                                <td>{{ $supplierRegistration->creator->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">{{ __('supplier_registration.created_at') }}</td>
                                <td>{{ $supplierRegistration->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                            @if($supplierRegistration->approved_by)
                                <tr>
                                    <td class="fw-bold">{{ __('supplier_registration.approved_by') }}</td>
                                    <td>{{ $supplierRegistration->approver->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">{{ __('supplier_registration.approved_at') }}</td>
                                    <td>{{ $supplierRegistration->approved_at?->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endif
                            @if($supplierRegistration->converted_vendor_id)
                                <tr>
                                    <td class="fw-bold">{{ __('supplier_registration.converted_vendor') }}</td>
                                    <td>
                                        <a href="{{ route('vendors.show', $supplierRegistration->converted_vendor_id) }}">
                                            {{ __('supplier_registration.view_vendor') }}
                                        </a>
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                @if($supplierRegistration->status === 'pending' || $supplierRegistration->status === 'under_review')
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('supplier_registration.approval_actions') }}</h5>
                        </div>
                        <div class="card-body">
                            @can('supplier_registration.approve')
                                <form action="{{ route('purchases.supplier-registrations.approve', $supplierRegistration) }}"
                                    method="POST" class="mb-3">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="approval_notes"
                                            class="form-label">{{ __('supplier_registration.approval_notes') }}</label>
                                        <textarea class="form-control" id="approval_notes" name="approval_notes"
                                            rows="2"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100"
                                        onclick="return confirm('{{ __('supplier_registration.confirm_approve') }}')">
                                        <i class="fas fa-check"></i> {{ __('supplier_registration.approve') }}
                                    </button>
                                </form>

                                <form action="{{ route('purchases.supplier-registrations.reject', $supplierRegistration) }}"
                                    method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="rejection_reason"
                                            class="form-label">{{ __('supplier_registration.rejection_reason') }} *</label>
                                        <textarea class="form-control @error('rejection_reason') is-invalid @enderror"
                                            id="rejection_reason" name="rejection_reason" rows="2" required></textarea>
                                        @error('rejection_reason')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-danger w-100"
                                        onclick="return confirm('{{ __('supplier_registration.confirm_reject') }}')">
                                        <i class="fas fa-times"></i> {{ __('supplier_registration.reject') }}
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
@extends('layouts.app')

@section('title', __('Companies'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('Companies') }}</h1>
        <div class="page-actions">
            <a href="{{ route('admin.companies.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> {{ __('Add Company') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>{{ __('Logo') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Registration') }}</th>
                            <th>{{ __('Currency') }}</th>
                            <th>{{ __('Branches') }}</th>
                            <th>{{ __('Users') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th width="150">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($companies as $company)
                            <tr>
                                <td>{{ $company->id }}</td>
                                <td>
                                    @if($company->logo)
                                        <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" class="rounded"
                                            style="height: 30px;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                            style="width: 30px; height: 30px;">
                                            <i class="fas fa-building text-muted" style="font-size: 0.8rem;"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $company->name_en }}</div>
                                    <div class="small text-muted">{{ $company->name_ar }}</div>
                                </td>
                                <td>{{ $company->registration_number }}</td>
                                <td><span class="badge bg-info text-white">{{ $company->currency }}</span></td>
                                <td>{{ $company->branches_count }}</td>
                                <td>{{ $company->users_count }}</td>
                                <td>
                                    @if($company->is_active)
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.companies.edit', $company) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.companies.destroy', $company) }}" method="POST"
                                            onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">{{ __('No companies found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($companies->hasPages())
            <div class="card-footer">
                {{ $companies->links() }}
            </div>
        @endif
    </div>
@endsection
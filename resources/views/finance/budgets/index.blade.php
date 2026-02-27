@extends('layouts.app')

@section('title', __('messages.budget_management') . ' - ' . __('messages.finance_banking'))

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.budget_management') }}</h1>
        <a href="{{ route('finance.budgets.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> {{ __('messages.create_budget') }}
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.budget_list') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ __('messages.name') }}</th>
                            <th>{{ __('messages.year') }}</th>
                            <th>{{ __('messages.total_amount') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($budgets as $budget)
                            <tr>
                                <td>{{ $budget->name }}</td>
                                <td>{{ $budget->year }}</td>
                                <td class="text-end font-weight-bold">{{ number_format($budget->total_amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $budget->status == 'approved' ? 'success' : ($budget->status == 'draft' ? 'secondary' : 'info') }}">
                                        {{ ucfirst($budget->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('finance.budgets.edit', $budget->id) }}" class="btn btn-datatable btn-icon btn-transparent-dark mr-2"><i class="fas fa-edit"></i></a>
                                    <a href="{{ route('finance.budgets.show', $budget->id) }}" class="btn btn-datatable btn-icon btn-transparent-dark"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

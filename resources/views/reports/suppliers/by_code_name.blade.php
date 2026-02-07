@extends('layouts.app')

@section('title', __('reports.supplier_by_code_name'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ __('reports.supplier_by_code_name') }}</h1>
        <a href="{{ route('reports.suppliers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('general.back') }}
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ __('reports.filters') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.suppliers.by-code-name') }}" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="search" class="form-label">{{ __('reports.search') }}</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ $validated['search'] ?? '' }}" placeholder="{{ __('reports.code_name_email') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="status" class="form-label">{{ __('reports.status') }}</label>
                            <select class="form-select" id="status" name="status">
                                <option value="all" {{ ($validated['status'] ?? 'all') === 'all' ? 'selected' : '' }}>{{ __('reports.all') }}</option>
                                <option value="active" {{ ($validated['status'] ?? '') === 'active' ? 'selected' : '' }}>{{ __('reports.active') }}</option>
                                <option value="inactive" {{ ($validated['status'] ?? '') === 'inactive' ? 'selected' : '' }}>{{ __('reports.inactive') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="date_from" class="form-label">{{ __('reports.date_from') }}</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="{{ $validated['date_from'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="date_to" class="form-label">{{ __('reports.date_to') }}</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="{{ $validated['date_to'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="mb-3 w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> {{ __('reports.filter') }}
                            </button>
                            <a href="{{ route('reports.suppliers.by-code-name') }}" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> {{ __('reports.reset') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ __('reports.results') }}</h5>
            <div>
                <a href="{{ route('reports.suppliers.export-by-code-name', array_merge($validated, ['format' => 'csv'])) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-file-csv"></i> {{ __('reports.export_csv') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('reports.code') }}</th>
                            <th>{{ __('reports.name') }}</th>
                            <th>{{ __('reports.email') }}</th>
                            <th>{{ __('reports.phone') }}</th>
                            <th>{{ __('reports.city') }}</th>
                            <th class="text-end">{{ __('reports.total_invoices') }}</th>
                            <th class="text-end">{{ __('reports.total_purchases') }}</th>
                            <th>{{ __('reports.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vendors as $vendor)
                        <tr>
                            <td>{{ $vendor->code }}</td>
                            <td>{{ $vendor->name }}</td>
                            <td>{{ $vendor->email }}</td>
                            <td>{{ $vendor->phone }}</td>
                            <td>{{ $vendor->city }}</td>
                            <td class="text-end">{{ $vendor->purchase_summary->total_invoices ?? 0 }}</td>
                            <td class="text-end">{{ number_format($vendor->purchase_summary->total_gross ?? 0, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $vendor->is_active ? 'success' : 'danger' }}">
                                    {{ $vendor->is_active ? __('reports.active') : __('reports.inactive') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">{{ __('reports.no_records') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $vendors->links() }}
        </div>
    </div>
</div>
@endsection

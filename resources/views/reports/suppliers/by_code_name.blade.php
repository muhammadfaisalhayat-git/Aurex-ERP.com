@extends('layouts.app')

@section('title', 'Supplier Report by Code/Name')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Supplier Report by Code/Name</h1>
            <a href="{{ route('reports.suppliers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Filters</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('reports.suppliers.by-code-name') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search"
                                    value="{{ $validated['search'] ?? '' }}"
                                    placeholder="Code, Name or Email">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="all" {{ ($validated['status'] ?? 'all') === 'all' ? 'selected' : '' }}>
                                        All
                                    </option>
                                    <option value="active" {{ ($validated['status'] ?? '') === 'active' ? 'selected' : '' }}>
                                        Active
                                    </option>
                                    <option value="inactive" {{ ($validated['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="date_from" class="form-label">Date From</label>
                                <input type="date" class="form-control" id="date_from" name="date_from"
                                    value="{{ $validated['date_from'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="date_to" class="form-label">Date To</label>
                                <input type="date" class="form-control" id="date_to" name="date_to"
                                    value="{{ $validated['date_to'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="mb-3 w-100">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="{{ route('reports.suppliers.by-code-name') }}" class="btn btn-secondary">
                                    <i class="fas fa-undo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Results</h5>
                <div>
                    <a href="{{ route('reports.suppliers.export-by-code-name', array_merge($validated, ['format' => 'csv'])) }}"
                        class="btn btn-sm btn-success">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>City</th>
                                <th class="text-end">Total Invoices</th>
                                <th class="text-end">Total Purchases</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vendors as $vendor)
                                <tr>
                                    <td>{{ $vendor->code }}</td>
                                    <td>{{ $vendor->name }}</td>
                                    <td>{{ $vendor->email }}</td>
                                    <td>{{ $vendor->phone ?? $vendor->mobile }}</td>
                                    <td>{{ $vendor->city }}</td>
                                    <td class="text-end">{{ $vendor->purchase_invoices_count ?? 0 }}</td>
                                    <td class="text-end">
                                        {{ number_format($vendor->purchase_invoices_sum_total_amount ?? 0, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $vendor->status === 'active' ? 'success' : 'danger' }}">
                                            {{ $vendor->status === 'active' ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No records found</td>
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
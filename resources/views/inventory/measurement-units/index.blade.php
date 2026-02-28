@extends('layouts.app')

@section('title', 'Measurement Units')

@section('content')
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap">
            <h2 class="mb-0 text-white">
                <i class="fas fa-balance-scale me-2"></i>Measurement Units
            </h2>
            <a href="{{ route('inventory.measurement.units.create') }}"
                class="btn btn-primary shadow-sm rounded-pill mt-2 mt-sm-0">
                <i class="fas fa-plus me-1"></i> Add Unit
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <!-- Filters -->
            <form action="{{ route('inventory.measurement.units.index') }}" method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-lg-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i
                                    class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0"
                                placeholder="Search by name or code..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary w-100">Search</button>
                    </div>
                    @if(request()->hasAny(['search']))
                        <div class="col-md-2">
                            <a href="{{ route('inventory.measurement.units.index') }}"
                                class="btn btn-outline-secondary w-100">Clear</a>
                        </div>
                    @endif
                </div>
            </form>

            <!-- Data Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 rounded-start-3">Code</th>
                            <th class="border-0">Name</th>
                            <th class="border-0 text-center">Status</th>
                            <th class="border-0 rounded-end-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($units as $unit)
                            <tr>
                                <td class="text-muted fw-bold">{{ $unit->code ?? '-' }}</td>
                                <td class="fw-semibold">{{ $unit->name }}</td>
                                <td class="text-center">
                                    @if($unit->is_active)
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Active</span>
                                    @else
                                        <span
                                            class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('inventory.measurement.units.edit', $unit) }}"
                                            class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('inventory.measurement.units.destroy', $unit) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Are you sure you want to delete this unit?')"
                                                title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="fas fa-balance-scale fa-3x mb-3 text-light"></i>
                                    <p class="mb-0">No measurement units found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $units->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
@extends('layouts.app')

@section('title', 'Voucher Details - ' . $voucher->voucher_number)

@section('content')
<div class="page-header d-block d-md-flex">
    <div class="mb-3 mb-md-0">
        <h1 class="page-title">Maintenance: {{ $voucher->voucher_number }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('maintenance.vouchers.index') }}">Maintenance</a></li>
                <li class="breadcrumb-item active" aria-current="page">Details</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto d-flex gap-2">
        <a href="{{ route('maintenance.vouchers.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
        
        @if($voucher->status === 'draft')
            <form action="{{ route('maintenance.vouchers.start', $voucher) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-play me-2"></i>Start Maintenance
                </button>
            </form>
        @endif

        @if($voucher->status === 'in_progress')
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#completeModal">
                <i class="fas fa-check-double me-2"></i>Complete & Close
            </button>
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-4 col-lg-5">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title">General Info</h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Entity / Asset</label>
                    <div class="h5 mb-0 text-primary">{{ $voucher->entity_name }}</div>
                    <div class="text-muted small">{{ ucfirst($voucher->entity_type) }}</div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <label class="text-muted small text-uppercase fw-bold d-block mb-1">Status</label>
                        <span class="badge {{ $voucher->status === 'completed' ? 'bg-success' : 'bg-primary' }}">
                            {{ ucfirst(str_replace('_', ' ', $voucher->status)) }}
                        </span>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small text-uppercase fw-bold d-block mb-1">Workshop</label>
                        <div class="fw-semibold">{{ $voucher->workshop->name ?? 'N/A' }}</div>
                    </div>
                </div>

                @if($voucher->customer || $voucher->vendor)
                <div class="mb-4">
                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Linked Entity</label>
                    @if($voucher->customer)
                        <div class="h6 mb-0 text-success"><i class="fas fa-user-tie me-2"></i>{{ $voucher->customer->name }}</div>
                        <div class="text-muted small">Customer</div>
                    @endif
                    @if($voucher->vendor)
                        <div class="h6 mb-0 text-warning"><i class="fas fa-truck-field me-2"></i>{{ $voucher->vendor->name }}</div>
                        <div class="text-muted small">Vendor (Provider)</div>
                    @endif
                </div>
                @endif

                <div class="mb-4">
                    <label class="text-muted small text-uppercase fw-bold d-block">Cost Summary</label>
                    <div class="p-3 bg-light rounded border mt-2">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Estimated:</span>
                            <span class="fw-bold">{{ number_format($voucher->estimated_cost, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-success fw-bold pt-2 border-top">
                            <span>Actual Cost:</span>
                            <span>{{ number_format($voucher->actual_cost, 2) }} USD</span>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Problem Description</label>
                    <p class="mb-0 small p-2 bg-warning-subtle rounded border border-warning-subtle text-dark">
                        {{ $voucher->problem_description }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8 col-lg-7">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Spare Parts & Components</h5>
                @if($voucher->status === 'in_progress')
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPartModal">
                        <i class="fas fa-plus me-1"></i>Add Part
                    </button>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">Part/Product</th>
                                <th>Quantity</th>
                                <th>Unit Cost</th>
                                <th class="text-end pe-3">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($voucher->parts as $part)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold">{{ $part->product->name ?? 'Unknown Part' }}</div>
                                        <small class="text-muted">{{ $part->product->code ?? '' }}</small>
                                    </td>
                                    <td>{{ number_format($part->quantity, 2) }}</td>
                                    <td>{{ number_format($part->unit_cost, 2) }}</td>
                                    <td class="text-end pe-3 fw-bold">{{ number_format($part->total_cost, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted small">No parts recorded for this voucher.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($voucher->parts->count() > 0)
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Parts Total:</td>
                                    <td class="text-end pe-3 fw-bold text-primary">{{ number_format($voucher->parts->sum('total_cost'), 2) }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        @if($voucher->status === 'completed')
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Completion Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small text-uppercase fw-bold d-block mb-1">Work Description</label>
                        <p class="mb-0 text-dark">{{ $voucher->work_description }}</p>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold d-block mb-1">Completion Date</label>
                            <div>{{ $voucher->completion_date->format('Y-m-d') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold d-block mb-1">Completed By</label>
                            <div>{{ $voucher->completedBy->name ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Complete Modal -->
<div class="modal fade" id="completeModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('maintenance.vouchers.complete', $voucher) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Complete Maintenance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Work Description / Resolution <span class="text-danger">*</span></label>
                    <textarea name="work_description" class="form-control" rows="4" required></textarea>
                </div>
                <div class="mb-0">
                    <label class="form-label">Final Actual Cost</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" name="actual_cost" class="form-control" value="{{ $voucher->parts->sum('total_cost') }}">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary border-0" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Mark as Completed</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Part Modal -->
<div class="modal fade" id="addPartModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('maintenance.vouchers.add-parts', $voucher) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add Spare Part</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Product / Part <span class="text-danger">*</span></label>
                    <select name="product_id" class="form-select" required>
                        <option value="">Search Part...</option>
                        @foreach(App\Models\Product::all() as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="row">
                    <div class="col-6">
                        <label class="form-label">Quantity</label>
                        <input type="number" step="0.01" name="quantity" class="form-control" value="1.00" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Unit Cost</label>
                        <input type="number" step="0.01" name="unit_cost" class="form-control" placeholder="0.00" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary border-0" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Part</button>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'New Maintenance Voucher')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">New Voucher</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('maintenance.vouchers.index') }}">Maintenance</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Maintenance Configuration</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('maintenance.vouchers.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label">Voucher Number <span class="text-danger">*</span></label>
                            <input type="text" name="voucher_number" class="form-control @error('voucher_number') is-invalid @enderror" value="{{ old('voucher_number', 'MV-' . date('Ymd-His')) }}" required>
                            @error('voucher_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="voucher_date" class="form-control" value="{{ old('voucher_date', date('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Workshop <span class="text-danger">*</span></label>
                            <select name="workshop_id" class="form-select @error('workshop_id') is-invalid @enderror" required>
                                <option value="">Select Workshop...</option>
                                @foreach($workshops as $workshop)
                                    <option value="{{ $workshop->id }}" {{ old('workshop_id') == $workshop->id ? 'selected' : '' }}>
                                        {{ $workshop->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Entity Type (Asset/Vehicle/Machine) <span class="text-danger">*</span></label>
                            <select name="entity_type" class="form-select" required>
                                <option value="vehicle" selected>Vehicle</option>
                                <option value="machine">Machine</option>
                                <option value="facility">Facility</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Entity Name / Reference <span class="text-danger">*</span></label>
                            <input type="text" name="entity_name" class="form-control" placeholder="Plate # or Machine ID" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Linked Customer (Optional)</label>
                            <select name="customer_id" class="form-select select2">
                                <option value="">None / Internal</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Linked Vendor (Optional)</label>
                            <select name="vendor_id" class="form-select select2">
                                <option value="">None / Internal</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Maintenance Type <span class="text-danger">*</span></label>
                            <select name="maintenance_type" class="form-select" required>
                                <option value="preventive" selected>Preventive</option>
                                <option value="corrective">Corrective (Repair)</option>
                                <option value="predictive">Predictive</option>
                                <option value="overhaul">Overhaul</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Estimated Cost</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="estimated_cost" class="form-control" value="0.00">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Problem Description <span class="text-danger">*</span></label>
                            <textarea name="problem_description" class="form-control" rows="3" required placeholder="Describe the issue or maintenance required..."></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Scheduled Date</label>
                            <input type="date" name="scheduled_date" class="form-control" value="{{ old('scheduled_date') }}">
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                        <a href="{{ route('maintenance.vouchers.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create Voucher
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

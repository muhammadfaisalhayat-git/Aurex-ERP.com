@extends('layouts.app')

@section('title', 'Measurement Units')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 mb-0 text-white"><i class="fas fa-plus me-2"></i>Create Measurement Unit</h2>
                <a href="{{ route('inventory.measurement.units.index') }}" class="btn btn-outline-light rounded-pill px-4">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('inventory.measurement.units.store') }}" method="POST">
                        @csrf

                        <div class="row g-4">
                            <div class="col-md-4">
                                <label for="code" class="form-label fw-semibold">Unit Code</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code"
                                    name="code" value="{{ old('code') }}" placeholder="e.g. PCS, KG">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label for="name" class="form-label fw-semibold">Unit Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                    name="name" value="{{ old('name') }}" placeholder="e.g. Pieces, Kilograms" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mt-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                        value="1" checked>
                                    <label class="form-check-label ms-2 cursor-pointer" for="is_active">
                                        <span class="fw-semibold text-dark">Active Status</span>
                                        <small class="d-block text-muted mt-1">Inactive units won't be available when
                                            creating or editing products.</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm">
                                <i class="fas fa-save me-2"></i> Save Measurement Unit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
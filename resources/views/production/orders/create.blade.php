@extends('layouts.app')

@section('title', 'New Production Order')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">New Order</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('production.orders.index') }}">Production Orders</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Order Configuration</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('production.orders.store') }}" method="POST">
                        @csrf

                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label">Order Number <span class="text-danger">*</span></label>
                                <input type="text" name="document_number"
                                    class="form-control @error('document_number') is-invalid @enderror"
                                    value="{{ old('document_number', 'PO-' . date('Ymd-His')) }}" required>
                                @error('document_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Product to Manufacture <span class="text-danger">*</span></label>
                                <select name="product_id" class="form-select @error('product_id') is-invalid @enderror"
                                    id="product_id" required>
                                    <option value="">Select a product...</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}
                                            data-units="{{ json_encode($product->units()->with('measurementUnit')->get()) }}">
                                            {{ $product->name }} ({{ __('messages.stock') }}: {{ $product->available_stock }})
                                            ({{ $product->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Quantity / Unit <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.001" name="quantity"
                                        class="form-control @error('quantity') is-invalid @enderror"
                                        value="{{ old('quantity') }}" required>
                                    <select name="measurement_unit_id" class="form-select" id="product_unit_id" required
                                        style="max-width: 120px;">
                                        <option value="">Unit</option>
                                    </select>
                                </div>
                                @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @error('measurement_unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Planned Start Date</label>
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ old('start_date', date('Y-m-d')) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Deadline</label>
                                <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Special Instructions / Notes</label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                            <a href="{{ route('production.orders.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-2"></i>Initialize Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.getElementById('product_id').addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const unitsStr = selectedOption.getAttribute('data-units');
                const units = unitsStr ? JSON.parse(unitsStr) : [];

                const unitDropdown = document.getElementById('product_unit_id');
                unitDropdown.innerHTML = '<option value="">Unit</option>';

                if (units && units.length > 0) {
                    units.forEach(u => {
                        const unitName = u.measurement_unit ? u.measurement_unit.name : (u.name || '');
                        const option = new Option(unitName, u.measurement_unit_id);
                        if (u.is_default) option.selected = true;
                        unitDropdown.add(option);
                    });
                }
            });

            // Trigger change on load if product is selected
            window.addEventListener('load', function () {
                if (document.getElementById('product_id').value) {
                    document.getElementById('product_id').dispatchEvent(new Event('change'));
                }
            });
        </script>
    @endpush
@endsection
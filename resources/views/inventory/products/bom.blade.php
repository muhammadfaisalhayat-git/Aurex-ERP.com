@extends('layouts.app')

@section('title', __('messages.bill_of_materials') . ' - ' . $product->name)

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a
                                href="{{ route('inventory.products.index') }}">{{ __('messages.products') }}</a></li>
                        <li class="breadcrumb-item active">{{ $product->name }} ({{ __('messages.stock') }}:
                            {{ $product->available_stock }})</li>
                    </ol>
                </nav>
                <h1 class="h3 mb-0">{{ __('messages.bill_of_materials') }}</h1>
            </div>
            <a href="{{ route('inventory.products.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card glassy mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('messages.product_details') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small d-block">{{ __('messages.name') }}</label>
                            <span class="fw-bold">{{ $product->name }} ({{ __('messages.stock') }}:
                                {{ $product->available_stock }})</span>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small d-block">{{ __('messages.code') }}</label>
                            <code>{{ $product->code }}</code>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small d-block">{{ __('messages.type') }}</label>
                            <span class="badge bg-primary">{{ ucfirst($product->type) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card glassy">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ __('messages.components') }}</h5>
                        @if($product->isComposite())
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addComponentModal">
                                <i class="fas fa-plus me-1"></i> {{ __('messages.add_component') }}
                            </button>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.component') }}</th>
                                        <th>{{ __('messages.quantity') }}</th>
                                        <th>{{ __('messages.waste_percentage') }}</th>
                                        <th>{{ __('messages.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($product->bomComponents as $bom)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $bom->component->name }}</div>
                                                <small class="text-muted">{{ $bom->component->code }}</small>
                                            </td>
                                            <td>
                                                {{ $bom->quantity }}
                                                <span class="badge bg-light text-dark">
                                                    {{ $bom->measurementUnit->name ?? $bom->component->unit_of_measure }}
                                                </span>
                                            </td>
                                            <td>{{ $bom->waste_percentage }}%</td>
                                            <td>
                                                <form action="{{ route('inventory.products.bom.destroy', [$product, $bom]) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('{{ __('messages.are_you_sure') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <div class="text-muted mb-2">{{ __('messages.no_components_found') }}</div>
                                                @if(!$product->isComposite())
                                                    <div class="small text-warning">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                        {{ __('messages.only_composite_products_can_have_bom') }}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($product->isComposite())
        <!-- Add Component Modal -->
        <div class="modal fade" id="addComponentModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('inventory.products.bom.update', $product) }}" method="POST">
                    @csrf
                    <div class="modal-content glassy">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('messages.add_component') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">{{ __('messages.component') }}</label>
                                <select name="component_id" class="form-select select2-products" required style="width: 100%">
                                    <option value="">{{ __('messages.select_product') }}</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('messages.quantity') }}</label>
                                    <input type="number" name="quantity" class="form-control" step="0.001" min="0.001" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('messages.unit') }}</label>
                                    <select name="measurement_unit_id" class="form-select" required>
                                        <option value="">{{ __('messages.select_unit') }}</option>
                                        @foreach($measurementUnits as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('messages.waste_percentage') }} (%)</label>
                                <input type="number" name="waste_percentage" class="form-control" step="0.01" min="0" max="100"
                                    value="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('messages.notes') }}</label>
                                <textarea name="notes" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.select2-products').select2({
                dropdownParent: $('#addComponentModal'),
                ajax: {
                    url: '{{ route("inventory.products.ajax-search") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function (item) {
                                return {
                                    id: item.id,
                                    text: (item.code ? '[' + item.code + '] ' : '') + ({{ app()->getLocale() == 'ar' }} ? item.name_ar : item.name_en),
                                    units: item.units
                                };
                            })
                        };
                    },
                    cache: true
                },
                placeholder: '{{ __('messages.select_product') }}',
                minimumInputLength: 1
            });

            $('.select2-products').on('select2:select', function (e) {
                var data = e.params.data;
                var unitSelect = $('select[name="measurement_unit_id"]');
                unitSelect.empty();
                unitSelect.append('<option value="">{{ __('messages.select_unit') }}</option>');

                if (data.units && data.units.length > 0) {
                    data.units.forEach(function (unit) {
                        unitSelect.append('<option value="' + unit.measurement_unit_id + '">' + unit.name + '</option>');
                    });
                } else {
                    // Fallback to all units if product has no specific units defined (unlikely but safe)
                    @foreach($measurementUnits as $unit)
                        unitSelect.append('<option value="{{ $unit->id }}">{{ $unit->name }}</option>');
                    @endforeach
            }
            });
        });
    </script>
@endpush
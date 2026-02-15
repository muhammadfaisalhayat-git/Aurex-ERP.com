@extends('layouts.app')

@section('title', isset($product) ? __('messages.edit') . ' ' . $product->name : __('messages.create_product'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ isset($product) ? __('messages.edit_product') : __('messages.create_product') }}</h1>
            <a href="{{ route('inventory.products.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="card glassy">
            <div class="card-body">
                <form
                    action="{{ isset($product) ? route('inventory.products.update', $product) : route('inventory.products.store') }}"
                    method="POST">
                    @csrf
                    @if(isset($product)) @method('PUT') @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.name_en') }}</label>
                            <input type="text" name="name_en" class="form-control"
                                value="{{ old('name_en', $product->name_en ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.name_ar') }}</label>
                            <input type="text" name="name_ar" class="form-control"
                                value="{{ old('name_ar', $product->name_ar ?? '') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.code') }}</label>
                            <input type="text" name="code" class="form-control"
                                value="{{ old('code', $product->code ?? '') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.category') }}</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">{{ __('messages.select_category') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ (old('category_id', $product->category_id ?? '') == $category->id) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.type') }}</label>
                            <select name="type" class="form-select" required>
                                <option value="simple" {{ (old('type', $product->type ?? '') == 'simple') ? 'selected' : '' }}>{{ __('messages.simple') }}</option>
                                <option value="composite" {{ (old('type', $product->type ?? '') == 'composite') ? 'selected' : '' }}>{{ __('messages.composite') }}</option>
                                <option value="service" {{ (old('type', $product->type ?? '') == 'service') ? 'selected' : '' }}>{{ __('messages.service') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.cost_price') }}</label>
                            <input type="number" name="cost_price" class="form-control" step="0.01"
                                value="{{ old('cost_price', $product->cost_price ?? '0.00') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.sale_price') }}</label>
                            <input type="number" name="sale_price" class="form-control" step="0.01"
                                value="{{ old('sale_price', $product->sale_price ?? '0.00') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.tax_rate') }}</label>
                            <div class="input-group">
                                <input type="number" name="tax_rate" class="form-control" step="0.01"
                                    value="{{ old('tax_rate', $product->tax_rate ?? '15.00') }}" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> {{ __('messages.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
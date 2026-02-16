@extends('layouts.app')

@section('title', isset($category) ? __('messages.edit') . ' ' . $category->name : __('messages.create_category'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ isset($category) ? __('messages.edit_category') : __('messages.create_category') }}</h1>
            <a href="{{ route('inventory.categories.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="card glassy">
            <div class="card-body">
                <form
                    action="{{ isset($category) ? route('inventory.categories.update', $category) : route('inventory.categories.store') }}"
                    method="POST">
                    @csrf
                    @if(isset($category)) @method('PUT') @endif

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.code') }}</label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                                value="{{ old('code', $category->code ?? '') }}" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.name_en') }}</label>
                            <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror"
                                value="{{ old('name_en', $category->name_en ?? '') }}" required>
                            @error('name_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.name_ar') }}</label>
                            <input type="text" name="name_ar" class="form-control @error('name_ar') is-invalid @enderror"
                                value="{{ old('name_ar', $category->name_ar ?? '') }}" required>
                            @error('name_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.parent_category') }}</label>
                            <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                                <option value="">{{ __('messages.no_parent') }}</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ (old('parent_id', $category->parent_id ?? '') == $cat->id) ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.status') }}</label>
                            <select name="is_active" class="form-select">
                                <option value="1" {{ old('is_active', $category->is_active ?? true) ? 'selected' : '' }}>
                                    {{ __('messages.active') }}</option>
                                <option value="0" {{ !old('is_active', $category->is_active ?? true) ? 'selected' : '' }}>
                                    {{ __('messages.inactive') }}</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">{{ __('messages.description') }}</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                rows="3">{{ old('description', $category->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
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
                        <div class="col-md-12">
                            <label class="form-label">{{ __('messages.name') }}</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $category->name ?? '') }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">{{ __('messages.description') }}</label>
                            <textarea name="description" class="form-control"
                                rows="3">{{ old('description', $category->description ?? '') }}</textarea>
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
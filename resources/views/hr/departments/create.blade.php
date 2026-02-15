@extends('layouts.app')

@section('title', __('messages.create_department'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.create_department') }}</h1>
            <a href="{{ route('hr.departments.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="card glassy">
            <div class="card-body">
                <form action="{{ route('hr.departments.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.code') }}</label>
                            <input type="text" name="code" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.name_en') }}</label>
                            <input type="text" name="name_en" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.name_ar') }}</label>
                            <input type="text" name="name_ar" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">{{ __('messages.branch') }}</label>
                            <select name="branch_id" class="form-select">
                                <option value="">{{ __('messages.select_branch') }}</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
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
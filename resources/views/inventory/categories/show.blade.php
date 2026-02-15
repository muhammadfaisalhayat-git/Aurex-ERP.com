@extends('layouts.app')

@section('title', $category->name)

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ $category->name }}</h1>
            <div>
                <a href="{{ route('inventory.categories.edit', $category) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i> {{ __('messages.edit') }}
                </a>
                <a href="{{ route('inventory.categories.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> {{ __('messages.back') }}
                </a>
            </div>
        </div>

        <div class="card glassy">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('messages.basic_information') }}</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th style="width: 150px;">{{ __('messages.name') }}</th>
                        <td>{{ $category->name }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('messages.description') }}</th>
                        <td>{{ $category->description ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection
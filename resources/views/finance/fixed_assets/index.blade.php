@extends('layouts.app')

@section('title', __('messages.fixed_assets') . ' - ' . __('messages.finance_banking'))

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.fixed_assets') }}</h1>
        <a href="{{ route('finance.fixed-assets.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> {{ __('messages.add_asset') }}
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.asset_list') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ __('messages.code') }}</th>
                            <th>{{ __('messages.name') }}</th>
                            <th>{{ __('messages.category') }}</th>
                            <th>{{ __('messages.purchase_cost') }}</th>
                            <th>{{ __('messages.current_value') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assets as $asset)
                            <tr>
                                <td><code>{{ $asset->code }}</code></td>
                                <td>{{ App::getLocale() == 'ar' ? ($asset->name_ar ?? $asset->name_en) : $asset->name_en }}</td>
                                <td>{{ $asset->category->name_en ?? 'N/A' }}</td>
                                <td class="text-end font-weight-bold">{{ number_format($asset->purchase_cost, 2) }}</td>
                                <td class="text-end text-success font-weight-bold">{{ number_format($asset->current_value, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $asset->status == 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($asset->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('finance.fixed-assets.edit', $asset->id) }}" class="btn btn-datatable btn-icon btn-transparent-dark mr-2"><i class="fas fa-edit"></i></a>
                                    <a href="{{ route('finance.fixed-assets.show', $asset->id) }}" class="btn btn-datatable btn-icon btn-transparent-dark"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

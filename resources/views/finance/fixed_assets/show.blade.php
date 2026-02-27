@extends('layouts.app')

@section('title', __('messages.asset_details') . ' - ' . __('messages.finance'))

@section('content')
<div class="container-fluid px-4">
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.finance') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('finance.fixed_assets.index') }}">{{ __('messages.fixed_assets') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('messages.asset_details') }} ({{ $asset->code }})</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-xl-4 col-md-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.basic_info') }}</h6>
                    <a href="{{ route('finance.fixed_assets.edit', $asset->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-edit me-1"></i> {{ __('messages.edit') }}
                    </a>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="bg-light d-inline-block rounded-circle p-4 mb-2">
                            <i class="fas fa-building fa-4x text-gray-400"></i>
                        </div>
                        <h4 class="mb-0">{{ App::getLocale() == 'ar' ? ($asset->name_ar ?? $asset->name_en) : $asset->name_en }}</h4>
                        <span class="text-muted"><code>{{ $asset->code }}</code></span>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="small text-muted mb-1">{{ __('messages.category') }}</label>
                        <div class="h6 mb-0">{{ $asset->category->name_en }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted mb-1">{{ __('messages.purchase_date') }}</label>
                        <div class="h6 mb-0">{{ $asset->purchase_date }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted mb-1">{{ __('messages.purchase_cost') }}</label>
                        <div class="h6 mb-0 text-primary">{{ number_format($asset->purchase_cost, 2) }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted mb-1">{{ __('messages.current_value') }}</label>
                        <div class="h6 mb-0 text-success">{{ number_format($asset->current_value, 2) }}</div>
                    </div>
                    <div class="mb-0">
                        <label class="small text-muted mb-1">{{ __('messages.status') }}</label>
                        <div>
                            <span class="badge bg-{{ $asset->status == 'active' ? 'success' : 'danger' }}">
                                {{ strtoupper($asset->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-md-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.depreciation_accounting') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted mb-1">{{ __('messages.asset_account') }}</label>
                            <div class="p-2 border rounded bg-light">{{ $asset->assetAccount->code }} - {{ $asset->assetAccount->name_en }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted mb-1">{{ __('messages.depreciation_method') }}</label>
                            <div class="p-2 border rounded bg-light">{{ ucfirst($asset->depreciation_method) }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted mb-1">{{ __('messages.useful_life_years') }}</label>
                            <div class="p-2 border rounded bg-light">{{ $asset->useful_life_years }} {{ __('messages.years') }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted mb-1">{{ __('messages.salvage_value') }}</label>
                            <div class="p-2 border rounded bg-light">{{ number_format($asset->salvage_value, 2) }}</div>
                        </div>
                    </div>
                    
                    <h6 class="font-weight-bold mb-3">{{ __('messages.depreciation_schedule') }}</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('messages.year') }}</th>
                                    <th>{{ __('messages.depreciation_expense') }}</th>
                                    <th>{{ __('messages.accumulated_depreciation') }}</th>
                                    <th>{{ __('messages.book_value') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $cost = $asset->purchase_cost;
                                    $salvage = $asset->salvage_value;
                                    $life = $asset->useful_life_years;
                                    $accumulated = 0;
                                    $yearly = ($cost - $salvage) / $life;
                                @endphp
                                @for($i = 1; $i <= $life; $i++)
                                    @php
                                        $accumulated += $yearly;
                                        $bookValue = $cost - $accumulated;
                                    @endphp
                                    <tr>
                                        <td>{{ __('messages.year') }} {{ $i }}</td>
                                        <td>{{ number_format($yearly, 2) }}</td>
                                        <td>{{ number_format($accumulated, 2) }}</td>
                                        <td>{{ number_format($bookValue, 2) }}</td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

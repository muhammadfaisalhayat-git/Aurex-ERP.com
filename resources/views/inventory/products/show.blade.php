@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ $product->name }}</h1>
            <div>
                <a href="{{ route('inventory.products.edit', $product) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i> {{ __('messages.edit') }}
                </a>
                <a href="{{ route('inventory.products.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> {{ __('messages.back') }}
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card glassy h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('messages.basic_information') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 150px;">{{ __('messages.code') }}</th>
                                <td><code>{{ $product->code }}</code></td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.name_en') }}</th>
                                <td>{{ $product->name_en }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.name_ar') }}</th>
                                <td>{{ $product->name_ar }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.category') }}</th>
                                <td>{{ $product->category->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.type') }}</th>
                                <td><span class="badge bg-info">{{ ucfirst($product->type) }}</span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card glassy h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('messages.financial_information') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 150px;">{{ __('messages.cost_price') }}</th>
                                <td>{{ number_format($product->cost_price, 2) }} {{ __('messages.sar') }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.sale_price') }}</th>
                                <td>{{ number_format($product->sale_price, 2) }} {{ __('messages.sar') }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.tax_rate') }}</th>
                                <td>{{ $product->tax_rate }}%</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
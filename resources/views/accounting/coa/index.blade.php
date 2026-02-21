@extends('layouts.app')

@section('title', __('messages.chart_of_accounts'))

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('messages.chart_of_accounts') }}</h1>
            @can('manage chart of accounts')
                <a href="{{ route('accounting.gl.coa.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> {{ __('messages.create') }}
                </a>
            @endcan
        </div>

        <turbo-frame id="coa_frame" data-turbo-action="advance">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('messages.code') }}</th>
                                    <th>{{ __('messages.name') }}</th>
                                    <th>{{ __('messages.account_type') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th width="150">{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($accounts as $account)
                                    <tr>
                                        <td><code>{{ $account->code }}</code></td>
                                        <td>
                                            @if($account->parent_id)
                                                <span class="text-muted ms-3">|—</span>
                                            @endif
                                            {{ $isRtl ? ($account->name_ar ?? $account->name_en) : ($account->name_en ?? $account->name_ar) }}
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-dark">
                                                {{ __('messages.' . $account->type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $account->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $account->is_active ? __('messages.active') : __('messages.inactive') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('accounting.gl.transactions.jv.create') }}?account_id={{ $account->id }}"
                                                    class="btn btn-outline-info" title="{{ __('messages.journal_voucher') }}"
                                                    data-turbo-frame="main-frame">
                                                    <i class="fas fa-book"></i>
                                                </a>
                                                <a href="{{ route('accounting.gl.coa.edit', $account->id) }}"
                                                    class="btn btn-outline-primary" data-turbo-frame="main-frame">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('accounting.gl.coa.destroy', $account->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('{{ __('messages.are_you_sure') }}')"
                                                    data-turbo="false">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            {{ __('messages.no_data_found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </turbo-frame>
    </div>
@endsection
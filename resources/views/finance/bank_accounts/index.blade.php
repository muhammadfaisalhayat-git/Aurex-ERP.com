@extends('layouts.app')

@section('title', __('messages.bank_cash_accounts') . ' - ' . __('messages.finance_banking'))

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('messages.bank_cash_accounts') }}</h1>
            <a href="{{ route('finance.bank-accounts.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> {{ __('messages.add_account') }}
            </a>
        </div>

        <turbo-frame id="bank_accounts_frame" data-turbo-action="advance">
            <div class="row">
                @foreach($accounts as $account)
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-{{ $account->account_type == 'bank' ? 'primary' : 'success' }} shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-{{ $account->account_type == 'bank' ? 'primary' : 'success' }} text-uppercase mb-1">
                                            {{ $account->account_type == 'bank' ? $account->bank_name : __('messages.cash_account') }}
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($account->current_balance, 2) }} {{ $account->currency_code }}</div>
                                        <div class="text-muted small mt-2">{{ App::getLocale() == 'ar' ? ($account->name_ar ?? $account->name_en) : $account->name_en }}</div>
                                        <div class="text-muted small">{{ $account->account_number }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-{{ $account->account_type == 'bank' ? 'university' : 'money-bill-wave' }} fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('finance.bank-accounts.edit', $account->id) }}" class="btn btn-sm btn-link p-0 text-primary" data-turbo-frame="main-frame">{{ __('messages.edit') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.account_list') }}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.code') }}</th>
                                    <th>{{ __('messages.name') }}</th>
                                    <th>{{ __('messages.type') }}</th>
                                    <th>{{ __('messages.bank') }}</th>
                                    <th>{{ __('messages.account_number') }}</th>
                                    <th>{{ __('messages.balance') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($accounts as $account)
                                    <tr>
                                        <td><code>{{ $account->code }}</code></td>
                                        <td>{{ App::getLocale() == 'ar' ? ($account->name_ar ?? $account->name_en) : $account->name_en }}</td>
                                        <td><span class="badge bg-{{ $account->account_type == 'bank' ? 'info' : 'warning' }}">{{ __('messages.' . $account->account_type) }}</span></td>
                                        <td>{{ $account->bank_name }}</td>
                                        <td>{{ $account->account_number }}</td>
                                        <td class="text-end font-weight-bold">{{ number_format($account->current_balance, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $account->is_active ? 'success' : 'danger' }}">
                                                {{ $account->is_active ? __('messages.active') : __('messages.inactive') }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('finance.bank-accounts.edit', $account->id) }}" class="btn btn-datatable btn-icon btn-transparent-dark mr-2" data-turbo-frame="main-frame"><i class="fas fa-edit"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </turbo-frame>
    </div>
@endsection

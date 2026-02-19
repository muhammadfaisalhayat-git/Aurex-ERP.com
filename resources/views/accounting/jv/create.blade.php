@extends('layouts.app')

@section('title', __('messages.create_journal_voucher'))

@section('content')
    <div class="container-fluid px-0">
        <!-- Professional Toolbar -->
        <div class="toolbar-bg p-2 mb-3 border-bottom d-flex justify-content-between align-items-center">
            <div class="d-flex gap-1">
                <button type="button" class="btn btn-tool" id="btn-new" title="New"><i
                        class="fas fa-file-plus"></i></button>
                <button type="submit" form="jv-form" class="btn btn-tool" id="btn-save" title="Save"><i
                        class="fas fa-save"></i></button>
                <button type="button" class="btn btn-tool" id="btn-delete" title="Delete"><i
                        class="fas fa-trash-alt"></i></button>
                <div class="v-divider"></div>
                <button type="button" class="btn btn-tool" id="btn-search" title="Search"><i
                        class="fas fa-search"></i></button>
                <button type="button" class="btn btn-tool" id="btn-print" title="Print"><i
                        class="fas fa-print"></i></button>
                <div class="v-divider"></div>
                <button type="button" class="btn btn-tool" id="btn-first" title="First"><i
                        class="fas fa-step-backward"></i></button>
                <button type="button" class="btn btn-tool" id="btn-prev" title="Previous"><i
                        class="fas fa-chevron-left"></i></button>
                <button type="button" class="btn btn-tool" id="btn-next" title="Next"><i
                        class="fas fa-chevron-right"></i></button>
                <button type="button" class="btn btn-tool" id="btn-last" title="Last"><i
                        class="fas fa-step-forward"></i></button>
                <div class="v-divider"></div>
                <button type="button" class="btn btn-tool" id="btn-post" title="Post"><i
                        class="fas fa-check-circle"></i></button>
                <button type="button" class="btn btn-tool" id="btn-exit" title="Exit"
                    onclick="window.location='{{ route('accounting.gl.transactions.jv.index') }}'"><i
                        class="fas fa-door-open"></i></button>
            </div>
            <div class="d-flex align-items-center me-3">
                <span class="badge bg-primary px-3 py-2 uppercase tracking-wider small fw-bold shadow-sm">Journal Voucher
                    #NEW</span>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="px-4">
            <!-- Explorer Toggle -->
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="text-secondary fw-bold mb-0 small uppercase letter-spacing-1">
                    {{ __('messages.create_journal_voucher') }}
                </h5>
                <button type="button" class="btn btn-sm btn-outline-secondary border-dashed" id="toggle-explorer">
                    <i class="fas fa-columns me-1"></i> {{ __('messages.show_account_explorer') }}
                </button>
            </div>

            <div class="row g-3 mb-3 d-none" id="explorer-row">
                <!-- Account Explorer (Re-using logic from previously integrated version) -->
                <div class="col-md-3">
                    <div class="card shadow-sm explorer-card border-primary">
                        <div class="card-header bg-primary text-white py-1">
                            <small class="fw-bold"><i class="fas fa-list-ul me-2"></i>
                                {{ __('messages.main_accounts') }}</small>
                        </div>
                        <div class="card-body p-0 explorer-list" id="main-accounts-list" style="max-height: 200px;">
                            @foreach($mainAccounts as $account)
                                <div class="list-group-item list-group-item-action explorer-item border-0 border-bottom d-flex justify-content-between align-items-center py-1 px-3"
                                    data-id="{{ $account->id }}" data-type="main" onclick="loadExplorerSubAccounts(this)">
                                    <span class="account-label small">
                                        <code class="text-primary me-2">{{ $account->code }}</code>
                                        {{ app()->getLocale() === 'ar' ? ($account->name_ar ?? $account->name_en) : ($account->name_en ?? $account->name_ar) }}
                                    </span>
                                    @if($account->children_count > 0)
                                        <i class="fas fa-chevron-right text-muted icon-chevron small"></i>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm explorer-card border-info">
                        <div class="card-header bg-info text-white py-1">
                            <small class="fw-bold"><i class="fas fa-sitemap me-2"></i>
                                {{ __('messages.sub_accounts') }}</small>
                        </div>
                        <div class="card-body p-0 explorer-list" id="sub-accounts-list" style="max-height: 200px;">
                            <div class="p-4 text-center text-muted placeholder-text x-small">Select Parent Account</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm explorer-card border-dark">
                        <div class="card-header bg-dark text-white py-1 d-flex justify-content-between align-items-center">
                            <small class="fw-bold" id="selected-account-title"><i class="fas fa-info-circle me-2"></i>
                                {{ __('messages.account_data') }}</small>
                            <span class="badge bg-light text-dark d-none" id="explorer-account-code-badge"></span>
                        </div>
                        <div class="card-body explorer-data p-2" id="explorer-data-container"
                            style="max-height: 200px; overflow-y: auto;">
                            <div class="p-4 text-center text-muted placeholder-text x-small">
                                {{ __('messages.select_account_to_view_data') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('accounting.gl.transactions.jv.store') }}" method="POST" id="jv-form">
                @csrf

                <!-- Professional Header Section (Tabbed) -->
                <div class="card border-0 shadow-sm mb-4">
                    <ul class="nav nav-tabs professional-tabs px-3 border-bottom-0" id="jvTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="master-tab" data-bs-toggle="tab"
                                data-bs-target="#master-pane" type="button"
                                role="tab">{{ __('messages.master_details') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="import-tab" data-bs-toggle="tab" data-bs-target="#import-pane"
                                type="button" role="tab">{{ __('messages.import_from_file') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="additional-tab" data-bs-toggle="tab"
                                data-bs-target="#additional-pane" type="button"
                                role="tab">{{ __('messages.additional_data') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="default-tab" data-bs-toggle="tab" data-bs-target="#default-pane"
                                type="button" role="tab">{{ __('messages.default_data') }}</button>
                        </li>
                    </ul>
                    <div class="tab-content border border-top-0 rounded-bottom p-3 bg-light-panel" id="jvTabsContent">
                        <div class="tab-pane fade show active" id="master-pane" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <div class="row mb-2">
                                        <label
                                            class="col-sm-4 col-form-label col-form-label-sm fw-bold">{{ __('messages.branch_no') }}</label>
                                        <div class="col-sm-8">
                                            <select name="branch_id"
                                                class="form-select form-select-sm shadow-none border-gray select2">
                                                @foreach($branches as $branch)
                                                    <option value="{{ $branch->id }}">{{ $branch->code }}-{{ $branch->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <label
                                            class="col-sm-4 col-form-label col-form-label-sm fw-bold">{{ __('messages.doc_type') }}</label>
                                        <div class="col-sm-8">
                                            <select name="doc_type"
                                                class="form-select form-select-sm shadow-none border-gray">
                                                <option value="1-Journal">1-Journal Voucher</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <label
                                            class="col-sm-4 col-form-label col-form-label-sm fw-bold">{{ __('messages.ref_code') }}</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="reference_no"
                                                class="form-control form-control-sm shadow-none border-gray"
                                                placeholder="AUTO">
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <label
                                            class="col-sm-4 col-form-label col-form-label-sm fw-bold">{{ __('messages.jv_date') }}</label>
                                        <div class="col-sm-8">
                                            <input type="date" name="voucher_date"
                                                class="form-control form-control-sm shadow-none border-gray"
                                                value="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <label
                                            class="col-sm-4 col-form-label col-form-label-sm fw-bold">{{ __('messages.no_of_attachments') }}</label>
                                        <div class="col-sm-8">
                                            <input type="number" name="no_of_attachments"
                                                class="form-control form-control-sm shadow-none border-gray" value="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row mb-2">
                                        <label
                                            class="col-sm-4 col-form-label col-form-label-sm fw-bold">{{ __('messages.recipient_name') }}</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="recipient_name"
                                                class="form-control form-control-sm shadow-none border-gray">
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <label
                                            class="col-sm-4 col-form-label col-form-label-sm fw-bold">{{ __('messages.beneficiary_name') }}</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="beneficiary_name"
                                                class="form-control form-control-sm shadow-none border-gray">
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <label
                                            class="col-sm-4 col-form-label col-form-label-sm fw-bold">{{ __('messages.notes') }}</label>
                                        <div class="col-sm-8">
                                            <textarea name="description"
                                                class="form-control form-control-sm shadow-none border-gray"
                                                rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card p-2 border-0 bg-soft-blue shadow-none h-100">
                                        <div class="label-group d-flex flex-column gap-1">
                                            <div class="form-check form-switch-sm">
                                                <input class="form-check-input" type="checkbox" name="is_posted">
                                                <label
                                                    class="form-check-label x-small fw-bold">{{ __('messages.is_posted') }}</label>
                                            </div>
                                            <div class="form-check form-switch-sm">
                                                <input class="form-check-input" type="checkbox" name="is_reversed">
                                                <label
                                                    class="form-check-label x-small fw-bold">{{ __('messages.is_reversed') }}</label>
                                            </div>
                                            <div class="form-check form-switch-sm">
                                                <input class="form-check-input" type="checkbox" name="is_periodic">
                                                <label
                                                    class="form-check-label x-small fw-bold">{{ __('messages.is_periodic') }}</label>
                                            </div>
                                            <div class="form-check form-switch-sm">
                                                <input class="form-check-input" type="checkbox"
                                                    name="is_currency_discrepancy">
                                                <label
                                                    class="form-check-label x-small fw-bold">{{ __('messages.is_currency_discrepancy') }}</label>
                                            </div>
                                            <div class="form-check form-switch-sm">
                                                <input class="form-check-input" type="checkbox" name="is_suspended">
                                                <label
                                                    class="form-check-label x-small fw-bold">{{ __('messages.is_suspended') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Amount in Words Banner -->
                <div
                    class="amount-words-banner px-4 py-2 mb-4 d-flex align-items-center justify-content-between shadow-sm rounded-pill">
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-white text-success px-4 py-2 rounded-pill fw-bold blink"
                            style="font-size: 1.1rem;">Debit: <span id="banner-debit-total">0.00</span></span>
                        <span class="badge bg-white text-danger px-4 py-2 rounded-pill fw-bold blink"
                            style="font-size: 1.1rem;">Credit: <span id="banner-credit-total">0.00</span></span>
                    </div>
                    <div class="text-white text-end flex-grow-1 px-4">
                        <div class="x-small opacity-75 uppercase tracking-wider">{{ __('messages.amount_in_words') }}</div>
                        <div class="fw-bold tracking-wide" id="amount-text" style="font-size: 1.2rem;">ZERO RIYALS ONLY |
                            صفر ريال فقط</div>
                    </div>
                    <div class="balance-indicator text-white p-2 border-start border-white border-opacity-25 ms-3">
                        <i class="fas fa-balance-scale fa-2x opacity-50"></i>
                    </div>
                </div>

                <!-- Professional Detail Grid -->
                <div class="card border-0 shadow-sm overflow-hidden">
                    <div class="grid-container" style="overflow-x: auto;">
                        <table class="table table-bordered table-sm dense-table mb-0" id="items-table">
                            <thead
                                class="bg-gray-200 text-gray-700 x-small text-center uppercase tracking-tighter align-middle">
                                <tr style="height: 40px;">
                                    <th style="width: 40px;">No.</th>
                                    <th style="min-width: 140px;">{{ __('messages.account_code') }}</th>
                                    <th style="min-width: 200px;">{{ __('messages.account_name') }}</th>
                                    <th style="min-width: 140px;">{{ __('messages.details') }}</th>
                                    <th style="width: 100px;">{{ __('messages.debit') }}</th>
                                    <th style="width: 100px;">{{ __('messages.credit') }}</th>
                                    <th style="width: 70px;">{{ __('messages.percentage') }} %</th>
                                    <th style="width: 70px;">{{ __('messages.currency') }}</th>
                                    <th style="min-width: 120px;">{{ __('messages.center_no') }}</th>
                                    <th style="min-width: 120px;">{{ __('messages.activity_no') }}</th>
                                    <th style="min-width: 120px;">{{ __('messages.lc_no') }}</th>
                                    <th style="min-width: 100px;">{{ __('messages.rep') }}</th>
                                    <th style="min-width: 120px;">{{ __('messages.collector_no') }}</th>
                                    <th style="min-width: 120px;">{{ __('messages.promoter_code') }}</th>
                                    <th style="min-width: 200px;">{{ __('messages.notes') }}</th>
                                    <th style="width: 40px;"></th>
                                </tr>
                            </thead>
                            <tbody id="items-body">
                                <!-- Row 1 -->
                                <tr class="item-row align-middle" data-idx="0">
                                    <td class="text-center x-small fw-bold text-muted bg-light">1</td>
                                    <td>
                                        <select name="items[0][chart_of_account_id]"
                                            class="form-select form-select-sm account-select select2-ajax px-2"
                                            style="width: 100%;" data-idx="0"></select>
                                        <input type="hidden" name="items[0][main_account_id]" class="main-account-id">
                                    </td>
                                    <td><span class="account-name-label text-muted x-small d-block text-truncate"
                                            style="max-width: 180px;">---</span></td>
                                    <td>
                                        <div class="entity-wrapper d-none">
                                            <select name="items[0][customer_id]"
                                                class="form-select form-select-sm customer-select select2-ajax px-1"></select>
                                            <select name="items[0][vendor_id]"
                                                class="form-select form-select-sm vendor-select select2-ajax px-1"></select>
                                        </div>
                                        <div class="text-center py-1 opacity-25"><i class="fas fa-link x-small"></i></div>
                                    </td>
                                    <td><input type="number" name="items[0][debit]"
                                            class="form-control form-control-sm text-end debit-input" value="0.00"
                                            step="0.01"></td>
                                    <td><input type="number" name="items[0][credit]"
                                            class="form-control form-control-sm text-end credit-input" value="0.00"
                                            step="0.01"></td>
                                    <td><input type="number" name="items[0][percentage]"
                                            class="form-control form-control-sm text-center x-small px-1" value="100"></td>
                                    <td><input type="text" name="items[0][currency]"
                                            class="form-control form-control-sm text-center x-small px-1" value="SR"></td>
                                    <td><select name="items[0][cost_center_no]"
                                            class="form-select form-select-sm cost-center-select select2-ajax px-1"></select>
                                    </td>
                                    <td><select name="items[0][activity_no]"
                                            class="form-select form-select-sm activity-select select2-ajax px-1"></select>
                                    </td>
                                    <td><select name="items[0][lc_no]"
                                            class="form-select form-select-sm lc-select select2-ajax px-1"></select></td>
                                    <td><select name="items[0][rep]"
                                            class="form-select form-select-sm employee-select select2-ajax px-1"></select>
                                    </td>
                                    <td><select name="items[0][collector_no]"
                                            class="form-select form-select-sm employee-select select2-ajax px-1"></select>
                                    </td>
                                    <td><select name="items[0][promoter_code]"
                                            class="form-select form-select-sm promoter-select select2-ajax px-1"></select>
                                    </td>
                                    <td><input type="text" name="items[0][notes]"
                                            class="form-control form-control-sm x-small px-1"></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm text-danger opacity-50 p-0 remove-row"><i
                                                class="fas fa-times-circle"></i></button>
                                    </td>
                                </tr>
                                <!-- Row 2 -->
                                <tr class="item-row align-middle" data-idx="1">
                                    <td class="text-center x-small fw-bold text-muted bg-light">2</td>
                                    <td>
                                        <select name="items[1][chart_of_account_id]"
                                            class="form-select form-select-sm account-select select2-ajax px-2"
                                            style="width: 100%;" data-idx="1"></select>
                                        <input type="hidden" name="items[1][main_account_id]" class="main-account-id">
                                    </td>
                                    <td><span class="account-name-label text-muted x-small d-block text-truncate"
                                            style="max-width: 180px;">---</span></td>
                                    <td>
                                        <div class="entity-wrapper d-none">
                                            <select name="items[1][customer_id]"
                                                class="form-select form-select-sm customer-select select2-ajax px-1"></select>
                                            <select name="items[1][vendor_id]"
                                                class="form-select form-select-sm vendor-select select2-ajax px-1"></select>
                                        </div>
                                        <div class="text-center py-1 opacity-25"><i class="fas fa-link x-small"></i></div>
                                    </td>
                                    <td><input type="number" name="items[1][debit]"
                                            class="form-control form-control-sm text-end debit-input" value="0.00"
                                            step="0.01"></td>
                                    <td><input type="number" name="items[1][credit]"
                                            class="form-control form-control-sm text-end credit-input" value="0.00"
                                            step="0.01"></td>
                                    <td><input type="number" name="items[1][percentage]"
                                            class="form-control form-control-sm text-center x-small px-1" value="100"></td>
                                    <td><input type="text" name="items[1][currency]"
                                            class="form-control form-control-sm text-center x-small px-1" value="SR"></td>
                                    <td><select name="items[1][cost_center_no]"
                                            class="form-select form-select-sm cost-center-select select2-ajax px-1"></select>
                                    </td>
                                    <td><select name="items[1][activity_no]"
                                            class="form-select form-select-sm activity-select select2-ajax px-1"></select>
                                    </td>
                                    <td><select name="items[1][lc_no]"
                                            class="form-select form-select-sm lc-select select2-ajax px-1"></select></td>
                                    <td><select name="items[1][rep]"
                                            class="form-select form-select-sm employee-select select2-ajax px-1"></select>
                                    </td>
                                    <td><select name="items[1][collector_no]"
                                            class="form-select form-select-sm employee-select select2-ajax px-1"></select>
                                    </td>
                                    <td><select name="items[1][promoter_code]"
                                            class="form-select form-select-sm promoter-select select2-ajax px-1"></select>
                                    </td>
                                    <td><input type="text" name="items[1][notes]"
                                            class="form-control form-control-sm x-small px-1"></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm text-danger opacity-50 p-0 remove-row"><i
                                                class="fas fa-times-circle"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-gray-100 border-top-2 border-primary">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold py-2 px-3 small">{{ __('messages.total') }}</td>
                                    <td class="text-end py-2 px-2 fw-bold text-primary" id="grid-total-debit">0.00</td>
                                    <td class="text-end py-2 px-2 fw-bold text-danger" id="grid-total-credit">0.00</td>
                                    <td colspan="10" class="text-end">
                                        <button type="button"
                                            class="btn btn-xs fw-bold text-primary border-0 bg-transparent"
                                            id="add-detail-row">
                                            <i class="fas fa-plus-circle me-1"></i> {{ __('messages.add_row') }}
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Metadata Footer -->
                <div
                    class="mt-4 p-2 border-top bg-light-panel rounded-bottom x-small d-flex justify-content-between text-muted italic">
                    <div>Inserted By: <span class="fw-bold">{{ auth()->user()->name }}</span> | Date:
                        {{ date('Y-m-d H:i') }}
                    </div>
                    <div>Modified By: --- | Date: ---</div>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* Professional ERP Aesthetics */
        :root {
            --erp-bg: #f5f7fb;
            --toolbar-bg: #f8f9fa;
            --border-gray: #dce0e4;
            --vibrant-green: #2ecc71;
            --primary-blue: #4e73df;
        }

        body {
            background-color: var(--erp-bg);
        }

        .toolbar-bg {
            background-color: var(--toolbar-bg);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .btn-tool {
            background: white;
            border: 1px solid var(--border-gray);
            color: #555;
            padding: 5px 12px;
            transition: all 0.2s;
        }

        .btn-tool:hover {
            background: #f0f3f6;
            color: var(--primary-blue);
            border-color: var(--primary-blue);
            transform: translateY(-1px);
        }

        .v-divider {
            width: 1px;
            background: var(--border-gray);
            margin: 0 5px;
        }

        .professional-tabs .nav-link {
            color: #666;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05rem;
            border: none;
            padding: 10px 20px;
            border-top: 3px solid transparent;
        }

        .professional-tabs .nav-link.active {
            color: var(--primary-blue);
            background: white;
            border-top: 3px solid var(--primary-blue);
            box-shadow: 0 -3px 5px rgba(0, 0, 0, 0.02);
        }

        .bg-light-panel {
            background-color: #ffffff;
        }

        .bg-soft-blue {
            background-color: #f0f4ff;
        }

        .amount-words-banner {
            background: linear-gradient(135deg, var(--vibrant-green) 0%, #27ae60 100%);
            border: none;
        }

        .dense-table th {
            background-color: #edf2f7;
            color: #4a5568 !important;
            font-weight: 800 !important;
            font-size: 0.65rem !important;
            border-bottom: 2px solid #cbd5e0 !important;
        }

        .dense-table td {
            padding: 4px 6px !important;
        }

        .dense-table input.form-control-sm,
        .dense-table select.form-select-sm {
            height: 28px;
            font-size: 0.75rem;
            border-radius: 2px;
        }

        .x-small {
            font-size: 0.7rem;
        }

        .letter-spacing-1 {
            letter-spacing: 1px;
        }

        .explorer-card {
            border-top-width: 3px;
        }

        .explorer-item:hover {
            background-color: #edf2f7;
        }

        .explorer-item.active {
            background-color: #ebf4ff !important;
            border-left: 4px solid var(--primary-blue) !important;
        }

        /* Blinking Animation */
        @keyframes blink-animation {
            0% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.7;
                transform: scale(1.02);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .blink {
            animation: blink-animation 1.5s infinite ease-in-out;
            display: inline-block;
        }

        /* Select2 Fixes */
        .select2-container {
            width: 100% !important;
            display: block !important;
        }

        .select2-selection--single {
            height: 28px !important;
            padding: 0 4px !important;
            line-height: 28px !important;
            border: 1px solid #dee2e6 !important;
        }

        .select2-container--bootstrap-5 .select2-selection {
            min-height: 28px !important;
            font-size: 0.7rem !important;
            border-radius: 4px !important;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            padding-left: 4px !important;
            line-height: 26px !important;
        }

        /* Responsive Table Adjustments */
        .dense-table td:nth-child(2) {
            min-width: 160px !important;
            width: 160px !important;
        }

        .dense-table td:nth-child(3) {
            min-width: 180px !important;
        }

        .account-name-label {
            white-space: normal !important;
            word-break: break-all !important;
            line-height: 1.2 !important;
            display: block !important;
            max-width: 100%;
        }

        /* Shadow effects */
        .shadow-sm {
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        }
    </style>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let rowCount = 2;
                const accountsData = @json($accounts);

                // Toggle Explorer
                document.getElementById('toggle-explorer').addEventListener('click', function () {
                    const row = document.getElementById('explorer-row');
                    row.classList.toggle('d-none');
                    this.innerHTML = row.classList.contains('d-none')
                        ? '<i class="fas fa-columns me-1"></i> {{ __('messages.show_account_explorer') }}'
                        : '<i class="fas fa-times me-1"></i> {{ __('messages.hide_account_explorer') }}';
                });

                // Calc Totals and Amount Text
                function updateFinancials() {
                    let debs = 0;
                    let creds = 0;
                    document.querySelectorAll('.debit-input').forEach(i => debs += parseFloat(i.value || 0));
                    document.querySelectorAll('.credit-input').forEach(i => creds += parseFloat(i.value || 0));

                    document.getElementById('grid-total-debit').innerText = debs.toFixed(2);
                    document.getElementById('grid-total-credit').innerText = creds.toFixed(2);
                    document.getElementById('banner-debit-total').innerText = debs.toFixed(2);
                    document.getElementById('banner-credit-total').innerText = creds.toFixed(2);

                    // Dynamic amount text fetching
                    if (debs > 0) {
                        fetch(`/ajax/accounting/amount-to-words?amount=${debs}&currency=SAR`)
                            .then(r => r.json())
                            .then(d => {
                                document.getElementById('amount-text').innerText = d.words_en + ' | ' + d.words_ar;
                            });
                    } else {
                        document.getElementById('amount-text').innerText = 'ZERO RIYALS ONLY | صفر ريال فقط';
                    }
                }

                // Ajax Account Search Wrapper
                function initProfessionalSelects(row) {
                    initSelect2Generic(row.find('.cost-center-select'), "{{ route('ajax.cost-centers.search') }}", 'Search Center...');
                    initSelect2Generic(row.find('.activity-select'), "{{ route('ajax.activities.search') }}", 'Search Activity...');
                    initSelect2Generic(row.find('.lc-select'), "{{ route('ajax.lcs.search') }}", 'Search LC...');
                    initSelect2Generic(row.find('.promoter-select'), "{{ route('ajax.promoters.search') }}", 'Search Promoter...');
                    initSelect2Generic(row.find('.employee-select'), "{{ route('ajax.employees.search') }}", 'Search Employee...');
                }

                function initSelect2Generic(el, url, placeholder) {
                    $(el).select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        dropdownAutoWidth: true,
                        placeholder: placeholder,
                        allowClear: true,
                        minimumInputLength: 0,
                        ajax: {
                            url: url,
                            dataType: 'json',
                            delay: 250,
                            data: function (params) {
                                return { q: params.term || '' };
                            },
                            processResults: function (data) {
                                return {
                                    results: data.map(i => ({
                                        id: i.code || i.id,
                                        text: i.code + (i.name ? ' - ' + i.name : (i.name_en ? ' - ' + i.name_en : '')),
                                        original: i
                                    }))
                                };
                            }
                        }
                    });
                }

                function initAccountSelect(el) {
                    $(el).select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        dropdownAutoWidth: true,
                        placeholder: 'Search Code...',
                        allowClear: true,
                        minimumInputLength: 0,
                        ajax: {
                            url: "{{ route('ajax.accounts.search') }}",
                            dataType: 'json',
                            delay: 250,
                            data: function (params) {
                                return { q: params.term || '' };
                            },
                            processResults: function (data) {
                                return {
                                    results: data.map(i => ({
                                        id: i.id,
                                        text: i.code + ' - ' + ({{ app()->getLocale() === 'ar' ? 'true' : 'false' }} ? (i.name_ar || i.name_en) : (i.name_en || i.name_ar)),
                                        original: i
                                    }))
                                };
                            }
                        }
                    }).on('select2:select change', function (e) {
                        const data = e.params ? e.params.data.original : ($(this).select2('data')[0] ? $(this).select2('data')[0].original : null);
                        if (!data) return;

                        const row = $(this).closest('tr');
                        const localName = {{ app()->getLocale() === 'ar' ? 'true' : 'false' }} ? (data.name_ar || data.name_en) : (data.name_en || data.name_ar);
                        row.find('.account-name-label').text(localName);
                        row.find('.main-account-id').val(data.parent_id || data.id);

                        // Handle Sub-ledger fields
                        const entityWrapper = row.find('.entity-wrapper');
                        const cust = row.find('.customer-select');
                        const vend = row.find('.vendor-select');
                        const linkIcon = row.find('.fa-link').parent();

                        entityWrapper.addClass('d-none');
                        cust.prop('disabled', true).addClass('d-none');
                        vend.prop('disabled', true).addClass('d-none');
                        linkIcon.removeClass('d-none');

                        if (data.sub_ledger_type === 'customer') {
                            entityWrapper.removeClass('d-none');
                            cust.prop('disabled', false).removeClass('d-none');
                            linkIcon.addClass('d-none');
                            initEntitySelect(cust, 'customer');
                        } else if (data.sub_ledger_type === 'vendor') {
                            entityWrapper.removeClass('d-none');
                            vend.prop('disabled', false).removeClass('d-none');
                            linkIcon.addClass('d-none');
                            initEntitySelect(vend, 'vendor');
                        }
                    });
                }

                function initEntitySelect(el, type) {
                    const url = type === 'customer' ? "{{ route('ajax.customers.search') }}" : "{{ route('ajax.vendors.search') }}";
                    $(el).select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        dropdownAutoWidth: true,
                        minimumInputLength: 0,
                        ajax: {
                            url: url,
                            dataType: 'json',
                            data: function (params) {
                                return {
                                    q: params.term || ''
                                };
                            },
                            processResults: function (data) {
                                return { results: data.map(i => ({ id: i.id, text: i.name })) };
                            }
                        }
                    });
                }

                // Initial Init
                $('.account-select').each(function () { initAccountSelect(this); });
                $('.customer-select').each(function () { initEntitySelect(this, 'customer'); });
                $('.vendor-select').each(function () { initEntitySelect(this, 'vendor'); });

                // Initialize professional selects for initial rows
                $('.item-row').each(function () {
                    initProfessionalSelects($(this));
                });

                updateFinancials();

                document.getElementById('btn-save').addEventListener('click', function () {
                    document.getElementById('jv-form').submit();
                });

                document.getElementById('btn-exit')?.addEventListener('click', function () {
                    window.location.href = "{{ route('accounting.gl.transactions.jv.index') }}";
                });

                document.getElementById('btn-new').addEventListener('click', function () {
                    window.location.href = "{{ route('accounting.gl.transactions.jv.create') }}";
                });

                document.getElementById('btn-search').addEventListener('click', function () {
                    window.location.href = "{{ route('accounting.gl.transactions.jv.index') }}";
                });

                document.getElementById('btn-print').addEventListener('click', function () {
                    @if(isset($jv))
                        window.open("{{ route('accounting.gl.transactions.jv.print', $jv->id) }}", '_blank');
                    @else
                        alert('Please save the voucher before printing.');
                    @endif
                                                                                                        });

                // Navigation (Mock/Basic for now - usually involves fetching IDs)
                document.getElementById('btn-first')?.addEventListener('click', function () {
                    // Logic to fetch first record ID and redirect
                });

                document.getElementById('btn-post')?.addEventListener('click', function () {
                    @if(isset($jv))
                        document.getElementById('jv-form').action = "{{ route('accounting.gl.transactions.jv.post', $jv->id) }}";
                        document.getElementById('jv-form').submit();
                    @else
                        alert('Save the voucher first.');
                    @endif
                                                                                                        });

                // Calc Totals and Amount Text

                document.getElementById('add-detail-row').addEventListener('click', function () {
                    const tbody = document.getElementById('items-body');
                    const firstRow = tbody.querySelector('.item-row');
                    const newRow = firstRow.cloneNode(true);
                    const $newRow = $(newRow);

                    newRow.setAttribute('data-idx', rowCount);
                    newRow.querySelector('td:first-child').innerText = rowCount + 1;

                    // Update names
                    $newRow.find('[name]').each(function () {
                        this.name = this.name.replace(/items\[\d+\]/, `items[${rowCount}]`);
                        if (!$(this).hasClass('percentage') && !$(this).is('[name*="currency"]')) {
                            this.value = '';
                        }
                        if ($(this).hasClass('debit-input') || $(this).hasClass('credit-input')) {
                            this.value = '0.00';
                        }
                    });

                    // Reset labels
                    $newRow.find('.account-name-label').text('---');
                    $newRow.find('.entity-wrapper').addClass('d-none');

                    // Clean select2 artifacts before injecting
                    $newRow.find('.select2-container').remove();
                    $newRow.find('.select2-ajax').removeClass('select2-hidden-accessible').empty();

                    tbody.appendChild(newRow);

                    // Re-initialize all Select2 in new row
                    initAccountSelect($newRow.find('.account-select')[0]);
                    initEntitySelect($newRow.find('.customer-select')[0], 'customer');
                    initEntitySelect($newRow.find('.vendor-select')[0], 'vendor');
                    initProfessionalSelects($newRow);

                    rowCount++;
                });

                // Explorer Logic (Retained from previous)
                window.loadExplorerSubAccounts = function (el) {
                    const id = el.dataset.id;
                    document.getElementById('sub-accounts-list').innerHTML = '<div class="p-3 text-center small text-muted">Loading...</div>';
                    fetch(`/ajax/accounting/sub-accounts/${id}`).then(r => r.json()).then(data => {
                        let html = '';
                        data.forEach(s => {
                            html += `<div class="list-group-item list-group-item-action explorer-item border-0 border-bottom d-flex justify-content-between align-items-center py-1 px-3" onclick="loadExplorerAccountData(${s.id})">
                                                                                                                    <span class="x-small"><code>${s.code}</code> ${ {{ app()->getLocale() === 'ar' ? 'true' : 'false' }} ? (s.name_ar || s.name_en) : (s.name_en || s.name_ar)}</span>
                                                                                                                </div>`;
                        });
                        document.getElementById('sub-accounts-list').innerHTML = html || '<div class="p-3 text-center x-small">No subs</div>';
                    });
                };

                window.loadExplorerAccountData = function (id) {
                    document.getElementById('explorer-data-container').innerHTML = 'Loading...';
                    fetch(`/ajax/accounting/account-data/${id}`).then(r => r.json()).then(res => {
                        const acc = res.account;
                        const typeName = acc.account_type ? ({{ app()->getLocale() === 'ar' ? 'true' : 'false' }} ? (acc.account_type.name_ar || acc.account_type.name_en) : (acc.account_type.name_en || acc.account_type.name_ar)) : '---';

                        document.getElementById('selected-account-title').innerText = acc.code + ' - ' + ({{ app()->getLocale() === 'ar' ? 'true' : 'false' }} ? (acc.name_ar || acc.name_en) : (acc.name_en || acc.name_ar));
                        document.getElementById('explorer-data-container').innerHTML = `
                                                                                        <div class="p-2 bg-light rounded shadow-none">
                                                                                            <div class="mb-2 border-bottom pb-1 d-flex justify-content-between x-small">
                                                                                                <span class="text-muted">Type: <span class="fw-bold text-dark">${typeName}</span></span>
                                                                                                <span class="text-muted">Link: <span class="fw-bold text-dark text-capitalize">${acc.sub_ledger_type || 'none'}</span></span>
                                                                                            </div>
                                                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                                                <small class="fw-bold text-primary uppercase">Balance: ${res.summary.balance}</small>
                                                                                                <button type="button" class="btn btn-xs btn-primary py-0" onclick="addAccountAt(${acc.id})"><i class="fas fa-plus"></i> Add</button>
                                                                                            </div>
                                                                                            <div class="x-small text-muted italic">Ready to post to journal.</div>
                                                                                        </div>
                                                                                    `;
                    });
                };

                window.addAccountAt = function (id) {
                    const row = $('#items-body tr').filter(function () {
                        return !$(this).find('.account-select').val();
                    }).first();

                    let targetRow;
                    if (row.length) {
                        targetRow = row;
                    } else {
                        document.getElementById('add-detail-row').click();
                        targetRow = $('#items-body tr').last();
                    }

                    const select = targetRow.find('.account-select');
                    fetch(`/ajax/accounting/account-data/${id}`)
                        .then(r => r.json())
                        .then(res => {
                            const acc = res.account;
                            const newOption = new Option(`${acc.code} - ${acc.name_en}`, acc.id, true, true);
                            newOption.original = acc;
                            select.append(newOption);
                            initProfessionalSelects(targetRow); // Initialize professional selects for the target row
                            select.trigger('change');
                            select.trigger({
                                type: 'select2:select',
                                params: { data: { original: acc } }
                            });
                        });
                };

                // Remove Row Event
                $(document).on('click', '.remove-row', function () {
                    const rows = $('#items-body tr');
                    if (rows.length > 2) {
                        $(this).closest('tr').remove();
                        // Re-index
                        $('#items-body tr').each(function (i) {
                            $(this).find('td:first-child').text(i + 1);
                        });
                        updateFinancials();
                    } else {
                        alert('Minimum 2 rows required.');
                    }
                });
            });
        </script>
    @endpush
@endsection
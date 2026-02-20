<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        $accounts = BankAccount::with('chartOfAccount')->get();
        return view('finance.bank_accounts.index', compact('accounts'));
    }

    public function create()
    {
        $coaAccounts = ChartOfAccount::posting()->where('type', 'asset')->get();
        return view('finance.bank_accounts.create', compact('coaAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:bank_accounts',
            'name_en' => 'required',
            'name_ar' => 'nullable',
            'bank_name' => 'nullable',
            'account_number' => 'nullable',
            'iban' => 'nullable',
            'currency_code' => 'required',
            'account_type' => 'required|in:bank,cash',
            'opening_balance' => 'required|numeric',
            'chart_of_account_id' => 'required|exists:chart_of_accounts,id',
        ]);

        $validated['current_balance'] = $validated['opening_balance'];
        $validated['company_id'] = session('active_company_id');

        BankAccount::create($validated);

        return redirect()->route('finance.bank-accounts.index')->with('success', 'Bank Account created successfully');
    }

    public function edit(BankAccount $bankAccount)
    {
        $coaAccounts = ChartOfAccount::posting()->where('type', 'asset')->get();
        return view('finance.bank_accounts.edit', compact('bankAccount', 'coaAccounts'));
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $validated = $request->validate([
            'name_en' => 'required',
            'name_ar' => 'nullable',
            'bank_name' => 'nullable',
            'account_number' => 'nullable',
            'iban' => 'nullable',
            'is_active' => 'boolean',
        ]);

        $bankAccount->update($validated);

        return redirect()->route('finance.bank-accounts.index')->with('success', 'Bank Account updated successfully');
    }
}

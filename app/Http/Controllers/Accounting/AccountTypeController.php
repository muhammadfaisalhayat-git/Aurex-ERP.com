<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AccountType;

class AccountTypeController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'required|string|max:50|unique:account_types,code',
            'description' => 'nullable|string',
        ]);

        $accountType = AccountType::create($validated);

        return response()->json([
            'success' => true,
            'data' => $accountType,
            'message' => __('messages.record_created')
        ]);
    }

    public function show(AccountType $accountType)
    {
        return response()->json([
            'success' => true,
            'data' => $accountType
        ]);
    }

    public function update(Request $request, AccountType $accountType)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'required|string|max:50|unique:account_types,code,' . $accountType->id,
            'description' => 'nullable|string',
        ]);

        $accountType->update($validated);

        return response()->json([
            'success' => true,
            'data' => $accountType,
            'message' => __('messages.record_updated')
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\TaxSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    public function index()
    {
        $taxSettings = TaxSetting::getCurrent();
        $systemSettings = SystemSetting::withoutGlobalScope('tenant')
            ->orderBy('group')
            ->orderBy('key')
            ->get()
            ->groupBy('group');

        return view('admin.settings.index', compact('taxSettings', 'systemSettings'));
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            // Handle Tax Settings
            if ($request->has('tax')) {
                $taxData = $request->validate([
                    'tax.tax_enabled' => 'nullable|boolean',
                    'tax.default_tax_rate' => 'required|numeric|min:0|max:100',
                    'tax.rounding_mode' => 'required|string|in:per_line,per_total',
                    'tax.tax_name_en' => 'required|string|max:50',
                    'tax.tax_name_ar' => 'required|string|max:50',
                    'tax.tax_number' => 'nullable|string|max:50',
                ]);

                $taxSettings = TaxSetting::getCurrent();
                $taxSettings->update([
                    'tax_enabled' => $request->boolean('tax.tax_enabled'),
                    'default_tax_rate' => $taxData['tax']['default_tax_rate'],
                    'rounding_mode' => $taxData['tax']['rounding_mode'],
                    'tax_name_en' => $taxData['tax']['tax_name_en'],
                    'tax_name_ar' => $taxData['tax']['tax_name_ar'],
                    'tax_number' => $taxData['tax']['tax_number'] ?? null,
                ]);
            }

            // Handle System Settings
            if ($request->has('settings')) {
                Log::info('System settings update request received', ['count' => count($request->input('settings'))]);
                foreach ($request->input('settings') as $id => $value) {
                    $setting = SystemSetting::withoutGlobalScope('tenant')->findOrFail($id);
                    if ($setting->is_editable) {
                        $oldValue = $setting->value;
                        $setting->update(['value' => $value]);
                        Log::info("Setting updated: {$setting->key}", [
                            'id' => $id,
                            'old' => $oldValue,
                            'new' => $value
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('acp.system.settings.index')->with('success', __('messages.settings_updated'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating settings: ' . $e->getMessage())->withInput();
        }
    }

    public function factoryReset(Request $request)
    {
        $request->validate([
            'confirm_reset' => 'required|string|in:RESET',
            'password' => 'required|string',
            'security_code' => 'required|string',
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($request->password, auth()->user()->password)) {
            return back()->with('error', __('messages.invalid_password'));
        }

        $sessionCode = session('factory_reset_code');
        $sessionCodeExpires = session('factory_reset_code_expires');

        if (!$sessionCode || $sessionCode !== $request->security_code || now()->greaterThan($sessionCodeExpires)) {
            return back()->with('error', __('messages.invalid_security_code'));
        }

        try {
            DB::beginTransaction();

            // Tables to truncate (Transactional and User-entered master data)
            $tables = [
                'leads',
                'opportunities',
                'crm_activities',
                'customer_requests',
                'quotations',
                'sales_orders',
                'sales_invoices',
                'sales_returns',
                'commission_runs',
                'commission_rules',
                'supply_orders',
                'purchase_invoices',
                'local_purchases',
                'products',
                'stock_ledger_entries',
                'stock_supplies',
                'stock_receivings',
                'stock_transfers',
                'stock_transfer_requests',
                'stock_issue_orders',
                'composite_assemblies',
                'employees',
                'attendances',
                'leave_requests',
                'salaries',
                'designations',
                'departments',
                'budgets',
                'fixed_assets',
                'asset_categories',
                'payment_vouchers',
                'receipt_vouchers',
                'bank_accounts',
                'journal_vouchers',
                'journal_voucher_details',
                'cost_centers',
                'letter_of_credits',
                'patients',
                'doctors',
                'appointments',
                'medical_services',
                'trailers',
                'transport_orders',
                'transport_contracts',
                'transport_claims',
                'fuel_logs',
                'delivery_vehicles',
                'maintenance_vouchers',
                'maintenance_workshops',
                'audit_logs',
                'notifications',
                'failed_jobs'
            ];

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            foreach ($tables as $table) {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    DB::table($table)->truncate();
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            DB::commit();
            return redirect()->route('acp.system.settings.index')->with('success', __('messages.factory_reset_success'));

        } catch (\Exception $e) {
            DB::rollBack();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return back()->with('error', 'Error during factory reset: ' . $e->getMessage());
        }
    }

    public function sendResetCode(Request $request)
    {
        try {
            $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $user = auth()->user();

            $adminEmail = \App\Models\SystemSetting::where('key', 'admin_primary_email')->value('value') ?? $user->email;

            session([
                'factory_reset_code' => $code,
                'factory_reset_code_expires' => now()->addMinutes(10)
            ]);

            \Illuminate\Support\Facades\Mail::to($adminEmail)->send(new \App\Mail\FactoryResetCodeMail($code, $user));

            return response()->json([
                'success' => true,
                'message' => __('messages.code_sent_success')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending code: ' . $e->getMessage()
            ], 500);
        }
    }
}

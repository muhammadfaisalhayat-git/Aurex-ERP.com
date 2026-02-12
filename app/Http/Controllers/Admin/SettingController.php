<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\TaxSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function index()
    {
        $taxSettings = TaxSetting::getCurrent();
        $systemSettings = SystemSetting::orderBy('group')->orderBy('key')->get()->groupBy('group');

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
                foreach ($request->input('settings') as $id => $value) {
                    $setting = SystemSetting::findOrFail($id);
                    if ($setting->is_editable) {
                        $setting->update(['value' => $value]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.settings.index')->with('success', __('messages.settings_updated'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating settings: ' . $e->getMessage())->withInput();
        }
    }
}

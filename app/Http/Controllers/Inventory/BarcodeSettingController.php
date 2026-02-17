<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\BarcodeSetting;
use Illuminate\Http\Request;

class BarcodeSettingController extends Controller
{
    /**
     * Show the form for editing the barcode settings.
     */
    public function edit()
    {
        $settings = BarcodeSetting::current();
        return view('inventory.barcodes.settings', compact('settings'));
    }

    /**
     * Update the barcode settings.
     */
    public function update(Request $request)
    {
        // Merge checkbox values into the request since they are missing if unchecked
        $request->merge([
            'show_product_name' => $request->has('show_product_name'),
            'show_product_code' => $request->has('show_product_code'),
            'show_product_price' => $request->has('show_product_price'),
            'show_custom_text' => $request->has('show_custom_text'),
            'check_digit' => $request->has('check_digit'),
            'ucc_ean_128' => $request->has('ucc_ean_128'),
        ]);

        $validated = $request->validate([
            'barcode_type' => ['required', 'string'],
            'page_size' => ['required', 'string'],
            'label_width' => ['required', 'numeric', 'min:1'],
            'label_height' => ['required', 'numeric', 'min:1'],
            'labels_per_row' => ['required', 'integer', 'min:1'],
            'margin_top' => ['required', 'numeric', 'min:0'],
            'margin_bottom' => ['required', 'numeric', 'min:0'],
            'margin_left' => ['required', 'numeric', 'min:0'],
            'margin_right' => ['required', 'numeric', 'min:0'],
            'show_product_name' => ['boolean'],
            'show_product_code' => ['boolean'],
            'show_product_price' => ['boolean'],
            'custom_text' => ['nullable', 'string', 'max:50'],
            'show_custom_text' => ['boolean'],
            'template' => ['required', 'string'],
            'font_size_name' => ['required', 'integer', 'min:1', 'max:50'],
            'font_size_code' => ['required', 'integer', 'min:1', 'max:50'],
            'font_size_price' => ['required', 'integer', 'min:1', 'max:50'],
            'font_size_custom' => ['required', 'integer', 'min:1', 'max:50'],
            'font_size_barcode' => ['required', 'integer', 'min:20', 'max:100'],
            'barcode_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'text_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'content_alignment' => ['required', 'string', 'in:left,center,right'],
            'check_digit' => ['boolean'],
            'ucc_ean_128' => ['boolean'],
            'pos_x_name' => ['required', 'numeric'],
            'pos_y_name' => ['required', 'numeric'],
            'pos_x_code' => ['required', 'numeric'],
            'pos_y_code' => ['required', 'numeric'],
            'pos_x_price' => ['required', 'numeric'],
            'pos_y_price' => ['required', 'numeric'],
            'pos_x_custom' => ['required', 'numeric'],
            'pos_y_custom' => ['required', 'numeric'],
            'pos_x_barcode' => ['required', 'numeric'],
            'pos_y_barcode' => ['required', 'numeric'],
        ]);

        $companyId = session('active_company_id') ?? auth()->user()?->company_id;
        $branchId = session('active_branch_id') ?? auth()->user()?->branch_id;

        $settings = BarcodeSetting::updateOrCreate(
            [
                'company_id' => $companyId,
                'branch_id' => $branchId,
            ],
            $validated
        );

        return redirect()->back()->with('success', __('messages.settings_updated'));
    }

    /**
     * Reset the barcode settings to defaults.
     */
    public function reset()
    {
        $companyId = session('active_company_id') ?? auth()->user()?->company_id;
        $branchId = session('active_branch_id') ?? auth()->user()?->branch_id;

        BarcodeSetting::where('company_id', $companyId)
            ->where('branch_id', $branchId)
            ->delete();

        return redirect()->back()->with('success', __('messages.settings_reset_to_defaults') ?? 'Settings reset to defaults');
    }
}

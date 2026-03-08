<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MandatoryFieldConfig;
use Illuminate\Http\Request;

class MandatoryFieldController extends Controller
{
    public function index(Request $request)
    {
        if (MandatoryFieldConfig::count() === 0) {
            $this->seedDummyData();
        }

        $modules = MandatoryFieldConfig::select('module')->distinct()->pluck('module');
        $selectedModule = $request->get('module', $modules->first());

        $fields = MandatoryFieldConfig::where('module', $selectedModule)
            ->orderBy('field_name')
            ->get();

        return view('acp.system.mandatory-fields.index', compact('modules', 'selectedModule', 'fields'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'fields' => 'required|array',
            'fields.*.id' => 'required|exists:mandatory_field_configs,id',
            'fields.*.is_mandatory' => 'boolean',
        ]);

        foreach ($validated['fields'] as $fieldData) {
            MandatoryFieldConfig::where('id', $fieldData['id'])->update([
                'is_mandatory' => $fieldData['is_mandatory'] ?? false,
            ]);
        }

        return back()->with('success', __('messages.sm_mandatory_fields_updated'));
    }

    private function seedDummyData()
    {
        $modules = [
            'Sales Invoice' => [
                ['field_name' => 'customer_id', 'field_label' => 'Customer', 'is_mandatory' => true],
                ['field_name' => 'invoice_date', 'field_label' => 'Invoice Date', 'is_mandatory' => true],
                ['field_name' => 'due_date', 'field_label' => 'Due Date', 'is_mandatory' => false],
                ['field_name' => 'payment_terms', 'field_label' => 'Payment Terms', 'is_mandatory' => false],
                ['field_name' => 'notes', 'field_label' => 'Notes', 'is_mandatory' => false],
                ['field_name' => 'reference', 'field_label' => 'Reference Number', 'is_mandatory' => false],
            ],
            'Purchase Invoice' => [
                ['field_name' => 'vendor_id', 'field_label' => 'Vendor', 'is_mandatory' => true],
                ['field_name' => 'invoice_date', 'field_label' => 'Invoice Date', 'is_mandatory' => true],
                ['field_name' => 'vendor_invoice_no', 'field_label' => 'Vendor Invoice No', 'is_mandatory' => false],
                ['field_name' => 'due_date', 'field_label' => 'Due Date', 'is_mandatory' => false],
                ['field_name' => 'notes', 'field_label' => 'Notes', 'is_mandatory' => false],
            ],
            'Quotation' => [
                ['field_name' => 'customer_id', 'field_label' => 'Customer', 'is_mandatory' => true],
                ['field_name' => 'quotation_date', 'field_label' => 'Quotation Date', 'is_mandatory' => true],
                ['field_name' => 'validity_date', 'field_label' => 'Validity Date', 'is_mandatory' => false],
                ['field_name' => 'terms', 'field_label' => 'Terms & Conditions', 'is_mandatory' => false],
            ],
            'Journal Voucher' => [
                ['field_name' => 'voucher_date', 'field_label' => 'Voucher Date', 'is_mandatory' => true],
                ['field_name' => 'reference', 'field_label' => 'Reference', 'is_mandatory' => false],
                ['field_name' => 'description', 'field_label' => 'Description', 'is_mandatory' => true],
                ['field_name' => 'cost_center_id', 'field_label' => 'Cost Center', 'is_mandatory' => false],
            ],
            'Employee' => [
                ['field_name' => 'name', 'field_label' => 'Full Name', 'is_mandatory' => true],
                ['field_name' => 'email', 'field_label' => 'Email', 'is_mandatory' => true],
                ['field_name' => 'phone', 'field_label' => 'Phone', 'is_mandatory' => false],
                ['field_name' => 'department_id', 'field_label' => 'Department', 'is_mandatory' => false],
                ['field_name' => 'national_id', 'field_label' => 'National ID', 'is_mandatory' => false],
            ],
        ];

        foreach ($modules as $module => $fields) {
            foreach ($fields as $field) {
                MandatoryFieldConfig::create(array_merge($field, [
                    'module' => $module,
                    'is_active' => true,
                ]));
            }
        }
    }
}

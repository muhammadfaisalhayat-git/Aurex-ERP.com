<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlertRule;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AlertSystemController extends Controller
{
    public function index()
    {
        if (AlertRule::count() === 0) {
            $this->seedDummyData();
        }

        $alerts = AlertRule::orderBy('created_at', 'desc')->paginate(20);
        return view('acp.system.alert-system.index', compact('alerts'));
    }

    public function create()
    {
        $modules = $this->getModules();
        $conditionTypes = $this->getConditionTypes();
        return view('acp.system.alert-system.create', compact('modules', 'conditionTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'module' => 'required|string|max:100',
            'condition_type' => 'required|string|max:100',
            'threshold' => 'required|numeric|min:0',
            'recipients' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $recipients = array_filter(array_map('trim', explode(',', $validated['recipients'] ?? '')));

        $alert = AlertRule::create([
            'name' => $validated['name'],
            'module' => $validated['module'],
            'condition_type' => $validated['condition_type'],
            'threshold' => $validated['threshold'],
            'recipients' => $recipients,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        AuditLog::log('create', 'alert_rule', $alert->id, null, $alert->toArray());

        return redirect()->route('acp.system.alert-system.index')
            ->with('success', __('messages.sm_alert_created'));
    }

    public function edit(AlertRule $alertRule)
    {
        $modules = $this->getModules();
        $conditionTypes = $this->getConditionTypes();
        return view('acp.system.alert-system.edit', compact('alertRule', 'modules', 'conditionTypes'));
    }

    public function update(Request $request, AlertRule $alertRule)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'module' => 'required|string|max:100',
            'condition_type' => 'required|string|max:100',
            'threshold' => 'required|numeric|min:0',
            'recipients' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $old = $alertRule->toArray();
        $recipients = array_filter(array_map('trim', explode(',', $validated['recipients'] ?? '')));

        $alertRule->update([
            'name' => $validated['name'],
            'module' => $validated['module'],
            'condition_type' => $validated['condition_type'],
            'threshold' => $validated['threshold'],
            'recipients' => $recipients,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        AuditLog::log('update', 'alert_rule', $alertRule->id, $old, $alertRule->toArray());

        return redirect()->route('acp.system.alert-system.index')
            ->with('success', __('messages.sm_alert_updated'));
    }

    public function destroy(AlertRule $alertRule)
    {
        $old = $alertRule->toArray();
        $alertRule->delete();
        AuditLog::log('delete', 'alert_rule', $alertRule->id, $old);

        return redirect()->route('acp.system.alert-system.index')
            ->with('success', __('messages.sm_alert_deleted'));
    }

    private function getModules()
    {
        return [
            __('messages.sales'),
            __('messages.purchases'),
            __('messages.inventory'),
            __('messages.finance'),
            __('messages.hr'),
            __('messages.accounting')
        ];
    }

    private function getConditionTypes()
    {
        return [
            'low_stock' => __('messages.low_stock_warning'),
            'overdue_invoice' => __('messages.overdue_invoice'),
            'budget_exceeded' => __('messages.budget_exceeded_warning'),
            'large_transaction' => __('messages.large_transaction_alert'),
            'login_failure' => __('messages.login_failure_alert'),
            'expiry_reminder' => __('messages.expiry_reminder'),
        ];
    }

    private function seedDummyData()
    {
        $rules = [
            ['name' => 'Low Stock Alert', 'module' => 'Inventory', 'condition_type' => 'low_stock', 'threshold' => 10, 'recipients' => ['warehouse@company.com'], 'is_active' => true],
            ['name' => 'Overdue Invoice Alert', 'module' => 'Sales', 'condition_type' => 'overdue_invoice', 'threshold' => 30, 'recipients' => ['sales@company.com', 'finance@company.com'], 'is_active' => true],
            ['name' => 'Large Transaction Alert', 'module' => 'Finance', 'condition_type' => 'large_transaction', 'threshold' => 50000, 'recipients' => ['cfo@company.com'], 'is_active' => true],
            ['name' => 'Budget Exceeded Warning', 'module' => 'Accounting', 'condition_type' => 'budget_exceeded', 'threshold' => 90, 'recipients' => ['admin@company.com'], 'is_active' => false],
            ['name' => 'Multiple Login Failures', 'module' => 'HR', 'condition_type' => 'login_failure', 'threshold' => 5, 'recipients' => ['security@company.com'], 'is_active' => true],
        ];

        foreach ($rules as $rule) {
            AlertRule::create($rule);
        }
    }
}

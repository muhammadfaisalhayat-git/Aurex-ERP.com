<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use App\Models\User;
use App\Services\ArabicShaper;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    protected $arabicShaper;

    public function __construct(ArabicShaper $arabicShaper)
    {
        $this->arabicShaper = $arabicShaper;
        $this->middleware('permission:view employees')->only(['index', 'show']);
        $this->middleware('permission:create employees')->only(['create', 'store']);
        $this->middleware('permission:edit employees')->only(['edit', 'update']);
        $this->middleware('permission:delete employees')->only(['destroy']);
    }

    public function index()
    {
        $employees = Employee::with(['department', 'designation'])->paginate(20);
        return view('hr.employees.index', compact('employees'));
    }

    public function create()
    {
        $departments = Department::active()->get();
        $designations = Designation::active()->get();
        $users = User::active()->get();
        return view('hr.employees.create', compact('departments', 'designations', 'users'));
    }

    public function show(Employee $employee)
    {
        $employee->load(['department', 'designation', 'user']);
        return view('hr.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $departments = Department::active()->get();
        $designations = Designation::active()->get();
        $users = User::active()->get();
        return view('hr.employees.edit', compact('employee', 'departments', 'designations', 'users'));
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('hr.employees.index')
            ->with('success', __('messages.deleted_successfully'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_code' => 'required|string|max:50|unique:employees,employee_code',
            'first_name_en' => 'required|string|max:255',
            'last_name_en' => 'required|string|max:255',
            'first_name_ar' => 'nullable|string|max:255',
            'last_name_ar' => 'nullable|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'nullable|string|max:20',
            'nationality' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'user_id' => 'nullable|exists:users,id',
            'joining_date' => 'required|date',
            'basic_salary' => 'nullable|numeric|min:0',
            'house_rent_allowance' => 'nullable|numeric|min:0',
            'conveyance_allowance' => 'nullable|numeric|min:0',
            'dearness_allowance' => 'nullable|numeric|min:0',
            'overtime_allowance' => 'nullable|numeric|min:0',
            'other_allowance' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,on_leave,terminated',
        ]);
        Employee::create($validated);
        return redirect()->route('hr.employees.index')->with('success', __('messages.created_successfully'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'employee_code' => 'required|string|max:50|unique:employees,employee_code,' . $employee->id,
            'first_name_en' => 'required|string|max:255',
            'last_name_en' => 'required|string|max:255',
            'first_name_ar' => 'nullable|string|max:255',
            'last_name_ar' => 'nullable|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:20',
            'nationality' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'user_id' => 'nullable|exists:users,id',
            'joining_date' => 'required|date',
            'basic_salary' => 'nullable|numeric|min:0',
            'house_rent_allowance' => 'nullable|numeric|min:0',
            'conveyance_allowance' => 'nullable|numeric|min:0',
            'dearness_allowance' => 'nullable|numeric|min:0',
            'overtime_allowance' => 'nullable|numeric|min:0',
            'other_allowance' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,on_leave,terminated',
        ]);
        $employee->update($validated);
        return redirect()->route('hr.employees.index')->with('success', __('messages.updated_successfully'));
    }

    public function salarySlip(Employee $employee)
    {
        $employee->load(['department', 'designation', 'company', 'branch']);
        $shaper = app(\App\Services\ArabicShaper::class);
        $employee->name_ar_reshaped = $shaper->shape(($employee->first_name_ar ?? '') . ' ' . ($employee->last_name_ar ?? ''));
        $employee->designation_ar_reshaped = $shaper->shape($employee->designation->name_ar ?? '');
        $employee->department_ar_reshaped = $shaper->shape($employee->department->name_ar ?? '');
        $employee->company_name_ar_reshaped = $shaper->shape($employee->company->name_ar ?? '');
        $logoBase64 = null;
        if ($employee->company?->logo) {
            $path = public_path('storage/' . $employee->company->logo);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('hr.employees.salary-slip', compact('employee', 'logoBase64'));
        return $pdf->stream('salary-slip-' . $employee->employee_code . '.pdf');
    }

    private function buildCardData(array $validated, Employee $employee, bool $isPreview = false): array
    {
        $data = $validated;
        $data['designation_en'] = $employee->designation->name ?? '';
        $data['designation_ar'] = $employee->designation->name_ar ?? '';
        $data['name_ar_reshaped'] = $this->arabicShaper->shape($data['name_ar'] ?? '');
        $data['company_name_ar_reshaped'] = $this->arabicShaper->shape($data['company_name_ar'] ?? '');
        $data['designation_ar_reshaped'] = $this->arabicShaper->shape($data['designation_ar'] ?? '');
        $data['address_ar_reshaped'] = $this->arabicShaper->shape($data['address_ar'] ?? '');
        $data['is_preview'] = $isPreview;
        $data['logoBase64'] = null;
        if ($employee->company?->logo) {
            $path = public_path('storage/' . $employee->company->logo);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $fileData = file_get_contents($path);
                $data['logoBase64'] = 'data:image/' . $type . ';base64,' . base64_encode($fileData);
            }
        }
        return $data;
    }

    private function cardValidationRules(): array
    {
        return [
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'company_name_en' => 'nullable|string|max:255',
            'company_name_ar' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'address_en' => 'nullable|string|max:500',
            'address_ar' => 'nullable|string|max:500',
            'template' => 'required|string|in:template-1,template-2,template-3,template-4,template-5',
        ];
    }

    public function generateVisitingCard(Request $request, Employee $employee)
    {
        $validated = $request->validate($this->cardValidationRules());
        $employee->load(['designation', 'company']);
        $data = $this->buildCardData($validated, $employee, false);
        $templateField = $validated['template'];
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('hr.employees.visiting-cards.' . $templateField, ['data' => $data]);
        $pdf->setPaper([0.0, 0.0, 252.0, 144.0], 'landscape');
        return $pdf->stream('visiting-card-' . $employee->employee_code . '.pdf');
    }

    public function previewVisitingCard(Request $request, Employee $employee)
    {
        $validated = $request->validate($this->cardValidationRules());
        $employee->load(['designation', 'company', 'branch']);
        $data = $this->buildCardData($validated, $employee, true);
        $templateField = $validated['template'];
        return view('hr.employees.visiting-cards.' . $templateField, ['data' => $data, 'is_preview' => true]);
    }
}
<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Services\ArabicShaper;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    protected $arabicShaper;

    public function __construct(ArabicShaper $arabicShaper)
    {
        $this->arabicShaper = $arabicShaper;
        $this->middleware('permission:view employees');
    }

    public function index()
    {
        $employees = Employee::with(['department', 'designation'])
            ->paginate(20);

        return view('hr.experience.index', compact('employees'));
    }

    public function show(Request $request, $id)
    {
        $employee = Employee::withoutGlobalScopes()
            ->with([
                'department' => fn($q) => $q->withoutGlobalScopes(),
                'designation' => fn($q) => $q->withoutGlobalScopes(),
                'company' => fn($q) => $q->withoutGlobalScopes()
            ])
            ->findOrFail($id);

        $template = $request->query('template', 'classic');
        $validTemplates = ['classic', 'modern', 'elegant', 'minimalist', 'executive'];

        if (!in_array($template, $validTemplates)) {
            $template = 'classic';
        }

        // Reshape Arabic text for PDF
        $employee->name_ar_reshaped = $this->arabicShaper->shape($employee->name_ar ?? '');
        $employee->designation_ar_reshaped = $this->arabicShaper->shape($employee->designation->name_ar ?? '');
        $employee->department_ar_reshaped = $this->arabicShaper->shape($employee->department->name_ar ?? '');
        $employee->company_name_ar_reshaped = $this->arabicShaper->shape($employee->company->name_ar ?? '');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView("hr.experience.templates.{$template}", compact('employee'));
        return $pdf->stream("experience-certificate-{$template}-" . $employee->employee_code . '.pdf');
    }
}

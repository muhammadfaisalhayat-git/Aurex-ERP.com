<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view employees');
    }

    public function index()
    {
        $employees = Employee::with(['department', 'designation'])
            ->where('status', 'active')
            ->get();

        return view('hr.salaries.index', compact('employees'));
    }

    public function show(Employee $employee)
    {
        return view('hr.salaries.show', compact('employee'));
    }
}

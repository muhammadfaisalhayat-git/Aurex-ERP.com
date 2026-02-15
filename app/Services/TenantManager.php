<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Branch;
use Illuminate\Support\Facades\Session;

class TenantManager
{
    /**
     * Get the currently active company ID.
     */
    public function getActiveCompanyId(): ?int
    {
        if (Session::has('active_company_id')) {
            return (int) Session::get('active_company_id');
        }

        if (auth()->check()) {
            return auth()->user()->company_id;
        }

        return null;
    }

    /**
     * Get the currently active company model.
     */
    public function getActiveCompany(): ?Company
    {
        $id = $this->getActiveCompanyId();
        return $id ? Company::find($id) : null;
    }

    /**
     * Get the currently active branch ID.
     */
    public function getActiveBranchId(): ?int
    {
        if (Session::has('active_branch_id')) {
            return (int) Session::get('active_branch_id');
        }

        if (auth()->check()) {
            return auth()->user()->branch_id;
        }

        return null;
    }

    /**
     * Get the currently active branch model.
     */
    public function getActiveBranch(): ?Branch
    {
        $id = $this->getActiveBranchId();
        return $id ? Branch::find($id) : null;
    }

    /**
     * Set the active company.
     */
    public function setActiveCompany(int $companyId): void
    {
        Session::put('active_company_id', $companyId);
        Session::forget('active_branch_id'); // Reset branch when company changes
    }

    /**
     * Set the active branch.
     */
    public function setActiveBranch(int $branchId): void
    {
        Session::put('active_branch_id', $branchId);
    }

    /**
     * Check if the user is a Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return auth()->check() && auth()->user()->hasRole('Super Admin');
    }

    /**
     * Check if the user is a Company Admin.
     */
    public function isCompanyAdmin(): bool
    {
        return auth()->check() && auth()->user()->hasRole('Company Admin');
    }
}

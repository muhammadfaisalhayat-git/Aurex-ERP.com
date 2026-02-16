<?php

namespace App\Traits;

use App\Models\Company;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Session;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        static::creating(function ($model) {
            if (empty($model->company_id) && Session::has('active_company_id')) {
                $model->company_id = Session::get('active_company_id');
            }

            if (empty($model->branch_id) && Session::has('active_branch_id')) {
                $model->branch_id = Session::get('active_branch_id');
            }
        });

        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check()) {
                $user = auth()->user();
                $table = $builder->getModel()->getTable();
                $model = $builder->getModel();

                // Determine active company ID (with fallback to user's branch if user record is incomplete)
                $activeCompanyId = Session::get('active_company_id');
                if (!$activeCompanyId && !$user->isSuperAdmin()) {
                    $activeCompanyId = $user->company_id ?: ($user->branch?->company_id);
                }

                // Super Admin can see EVERYTHING unless a specific company is selected in session
                if ($user->hasRole('Super Admin')) {
                    if (Session::has('active_company_id')) {
                        if ($model instanceof Branch) {
                            $builder->where($table . '.company_id', Session::get('active_company_id'));
                        } else {
                            $builder->where($table . '.company_id', Session::get('active_company_id'));
                        }
                    }
                } else {
                    // Normal users are restricted to their assigned company
                    $userCompanyId = $user->company_id ?: ($user->branch?->company_id);

                    if ($model instanceof Company) {
                        $builder->where($table . '.id', $userCompanyId);
                    } else {
                        $builder->where($table . '.company_id', $userCompanyId);
                    }

                    // If they are a Branch Manager or Salesman, further restrict to branch
                    $hasBranchColumn = \Schema::hasColumn($table, 'branch_id');

                    if ($model instanceof Branch) {
                        if (($user->hasRole('Branch Manager') || $user->hasRole('Salesman')) && !Session::has('view_all_branches')) {
                            $builder->where($table . '.id', $user->branch_id);
                        } elseif (Session::has('active_branch_id')) {
                            $builder->where($table . '.id', Session::get('active_branch_id'));
                        }
                    } elseif ($hasBranchColumn) {
                        if (($user->hasRole('Branch Manager') || $user->hasRole('Salesman')) && !Session::has('view_all_branches')) {
                            $builder->where($table . '.branch_id', $user->branch_id);
                        } elseif (Session::has('active_branch_id')) {
                            $builder->where($table . '.branch_id', Session::get('active_branch_id'));
                        }
                    }
                }
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}

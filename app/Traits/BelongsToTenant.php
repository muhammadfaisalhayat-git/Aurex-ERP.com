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
                // Safety check: only set branch_id if the column exists
                if (\Schema::hasColumn($model->getTable(), 'branch_id')) {
                    $model->branch_id = Session::get('active_branch_id');
                }
            }
        });

        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check()) {
                $user = auth()->user();
                $table = $builder->getModel()->getTable();
                $model = $builder->getModel();

                // Determine active company ID
                $activeCompanyId = Session::get('active_company_id');
                if (!$activeCompanyId && !$user->isSuperAdmin()) {
                    $activeCompanyId = $user->company_id ?: ($user->branch?->company_id);
                }

                // Super Admin can see EVERYTHING unless a specific company is selected
                if ($user->hasRole('Super Admin')) {
                    if (Session::has('active_company_id')) {
                        if (\Schema::hasColumn($table, 'company_id')) {
                            $builder->where($table . '.company_id', Session::get('active_company_id'));
                        }
                    }
                } else {
                    // Normal users are restricted to their assigned company
                    $userCompanyId = $user->company_id ?: ($user->branch?->company_id);

                    if ($model instanceof Company) {
                        $builder->where($table . '.id', $userCompanyId);
                    } elseif (\Schema::hasColumn($table, 'company_id')) {
                        $builder->where($table . '.company_id', $userCompanyId);
                    }

                    // Branch restrictions
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

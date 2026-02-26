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

                // Super Admin scoping
                if ($user->hasRole('Super Admin')) {
                    // Company Filter
                    if (Session::has('active_company_id')) {
                        if (\Schema::hasColumn($table, 'company_id')) {
                            $builder->where($table . '.company_id', Session::get('active_company_id'));
                        }
                    }

                    // Branch Filter
                    if (\Schema::hasColumn($table, 'branch_id')) {
                        if (Session::has('active_branch_id')) {
                            $builder->where(function ($q) use ($table) {
                                $q->where($table . '.branch_id', Session::get('active_branch_id'))
                                    ->orWhereNull($table . '.branch_id');
                            });
                        } else {
                            // If no branch selected, show nothing for models that belong to branches
                            $builder->where($table . '.branch_id', 0);
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
                        } else {
                            // No branch selected? Show nothing.
                            $builder->where($table . '.id', 0);
                        }
                    } elseif ($hasBranchColumn) {
                        if (($user->hasRole('Branch Manager') || $user->hasRole('Salesman')) && !Session::has('view_all_branches')) {
                            $builder->where(function ($q) use ($table, $user) {
                                $q->where($table . '.branch_id', $user->branch_id)
                                    ->orWhereNull($table . '.branch_id');
                            });
                        } elseif (Session::has('active_branch_id')) {
                            $builder->where(function ($q) use ($table) {
                                $q->where($table . '.branch_id', Session::get('active_branch_id'))
                                    ->orWhereNull($table . '.branch_id');
                            });
                        } else {
                            // No branch selected? Show nothing.
                            $builder->where($table . '.branch_id', 0);
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
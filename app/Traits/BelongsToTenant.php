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

                // Super Admin can see EVERYTHING unless a specific company is selected in session
                if ($user->hasRole('Super Admin')) {
                    if (Session::has('active_company_id')) {
                        $builder->where($builder->getModel()->getTable() . '.company_id', Session::get('active_company_id'));
                    }
                } else {
                    // Normal users are restricted to their assigned company
                    $builder->where($builder->getModel()->getTable() . '.company_id', $user->company_id);

                    // If they are a Branch Manager or Salesman, further restrict to branch
                    if (($user->hasRole('Branch Manager') || $user->hasRole('Salesman')) && !Session::has('view_all_branches')) {
                        $builder->where($builder->getModel()->getTable() . '.branch_id', $user->branch_id);
                    } elseif (Session::has('active_branch_id')) {
                        $builder->where($builder->getModel()->getTable() . '.branch_id', Session::get('active_branch_id'));
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

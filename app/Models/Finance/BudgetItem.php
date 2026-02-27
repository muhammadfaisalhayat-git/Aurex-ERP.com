<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ChartOfAccount;
use App\Models\CostCenter;

class BudgetItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'budget_id',
        'chart_of_account_id',
        'cost_center_id',
        'month_1', 'month_2', 'month_3', 'month_4', 'month_5', 'month_6',
        'month_7', 'month_8', 'month_9', 'month_10', 'month_11', 'month_12',
        'total_amount',
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class , 'chart_of_account_id');
    }

    public function costCenter()
    {
        return $this->belongsTo(ProfessionalCostCenter::class , 'cost_center_id');
    }
}

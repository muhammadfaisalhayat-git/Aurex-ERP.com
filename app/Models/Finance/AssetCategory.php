<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ChartOfAccount;

class AssetCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_ar',
        'depreciation_method',
        'useful_life_years',
        'salvage_value_percentage',
        'asset_account_id',
        'accumulated_depreciation_account_id',
        'depreciation_expense_account_id',
        'is_active',
    ];

    public function assetAccount()
    {
        return $this->belongsTo(ChartOfAccount::class , 'asset_account_id');
    }

    public function accumulatedDepreciationAccount()
    {
        return $this->belongsTo(ChartOfAccount::class , 'accumulated_depreciation_account_id');
    }

    public function depreciationExpenseAccount()
    {
        return $this->belongsTo(ChartOfAccount::class , 'depreciation_expense_account_id');
    }

    public function assets()
    {
        return $this->hasMany(FixedAsset::class);
    }
}

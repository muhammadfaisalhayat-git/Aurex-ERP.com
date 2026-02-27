<?php

namespace App\Models\Healthcare;

use Illuminate\Database\Eloquent\Model;
use App\Models\Company;
use App\Models\ChartOfAccount;

class MedicalService extends Model
{
    protected $table = 'healthcare_medical_services';

    protected $fillable = [
        'company_id',
        'code',
        'name_en',
        'name_ar',
        'cost',
        'revenue_account_id',
        'is_active',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function revenueAccount()
    {
        return $this->belongsTo(ChartOfAccount::class , 'revenue_account_id');
    }
}

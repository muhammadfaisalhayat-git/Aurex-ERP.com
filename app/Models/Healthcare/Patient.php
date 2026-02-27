<?php

namespace App\Models\Healthcare;

use Illuminate\Database\Eloquent\Model;
use App\Models\Company;
use App\Models\Branch;

class Patient extends Model
{
    protected $table = 'healthcare_patients';

    protected $fillable = [
        'company_id',
        'branch_id',
        'code',
        'name_en',
        'name_ar',
        'date_of_birth',
        'gender',
        'phone',
        'email',
        'address',
        'medical_history',
        'is_active',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}

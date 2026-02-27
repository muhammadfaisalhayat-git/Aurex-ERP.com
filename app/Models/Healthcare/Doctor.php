<?php

namespace App\Models\Healthcare;

use Illuminate\Database\Eloquent\Model;
use App\Models\Company;

class Doctor extends Model
{
    protected $table = 'healthcare_doctors';

    protected $fillable = [
        'company_id',
        'code',
        'name_en',
        'name_ar',
        'specialization',
        'license_number',
        'phone',
        'is_active',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}

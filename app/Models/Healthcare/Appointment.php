<?php

namespace App\Models\Healthcare;

use Illuminate\Database\Eloquent\Model;
use App\Models\Company;
use App\Models\Branch;

class Appointment extends Model
{
    protected $table = 'healthcare_appointments';

    protected $fillable = [
        'company_id',
        'branch_id',
        'patient_id',
        'doctor_id',
        'service_id',
        'appointment_date',
        'reference_no',
        'status',
        'billing_status',
        'total_amount',
        'journal_voucher_id',
        'notes',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function service()
    {
        return $this->belongsTo(MedicalService::class , 'service_id');
    }

    public function journalVoucher()
    {
        return $this->belongsTo(\App\Models\JournalVoucher::class);
    }
}

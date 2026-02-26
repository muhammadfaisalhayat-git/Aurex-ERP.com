<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\BelongsToTenant;

class Employee extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'user_id',
        'employee_code',
        'first_name_en',
        'last_name_en',
        'first_name_ar',
        'last_name_ar',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'nationality',
        'department_id',
        'designation_id',
        'joining_date',
        'exit_date',
        'basic_salary',
        'house_rent_allowance',
        'conveyance_allowance',
        'dearness_allowance',
        'overtime_allowance',
        'other_allowance',
        'national_id',
        'passport_number',
        'iban',
        'status',
    ];

    protected $appends = ['name'];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'exit_date' => 'date',
        'basic_salary' => 'decimal:2',
        'house_rent_allowance' => 'decimal:2',
        'conveyance_allowance' => 'decimal:2',
        'dearness_allowance' => 'decimal:2',
        'overtime_allowance' => 'decimal:2',
        'other_allowance' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ledgerEntries()
    {
        return $this->hasMany(LedgerEntry::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function getFirstNameAttribute()
    {
        return app()->getLocale() === 'ar' && $this->first_name_ar ? $this->first_name_ar : $this->first_name_en;
    }

    public function getLastNameAttribute()
    {
        return app()->getLocale() === 'ar' && $this->last_name_ar ? $this->last_name_ar : $this->last_name_en;
    }

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}

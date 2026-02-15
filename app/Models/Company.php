<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name_en',
        'name_ar',
        'logo',
        'registration_number',
        'tax_number',
        'address',
        'country',
        'currency',
        'default_tax_percentage',
        'fiscal_year_start',
        'fiscal_year_end',
        'contact_email',
        'contact_phone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'default_tax_percentage' => 'decimal:2',
        'fiscal_year_start' => 'date',
        'fiscal_year_end' => 'date',
    ];

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function getNameAttribute()
    {
        return app()->getLocale() === 'ar' ? ($this->name_ar ?? $this->name_en) : $this->name_en;
    }
}

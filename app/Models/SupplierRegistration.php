<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierRegistration extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'registration_number',
        'registration_date',
        'company_name_en',
        'company_name_ar',
        'trade_name',
        'company_type',
        'commercial_registration',
        'cr_issue_date',
        'cr_expiry_date',
        'tax_number',
        'vat_certificate_number',
        'contact_person',
        'contact_position',
        'phone',
        'mobile',
        'email',
        'website',
        'address',
        'city',
        'region',
        'postal_code',
        'country',
        'bank_name',
        'bank_account',
        'iban',
        'business_activities',
        'product_categories',
        'payment_terms',
        'credit_limit',
        'notes',
        'status',
        'submitted_by',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'converted_to_vendor_id',
    ];

    protected $casts = [
        'registration_date' => 'date',
        'cr_issue_date' => 'date',
        'cr_expiry_date' => 'date',
        'reviewed_at' => 'datetime',
        'product_categories' => 'json',
        'credit_limit' => 'decimal:2',
    ];

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'converted_to_vendor_id');
    }

    public function documents()
    {
        return $this->hasMany(SupplierRegistrationDocument::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeUnderReview($query)
    {
        return $query->where('status', 'under_review');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function canBeApproved()
    {
        return in_array($this->status, ['pending', 'under_review']);
    }

    public function approve($userId)
    {
        if (!$this->canBeApproved()) {
            return false;
        }

        $this->update([
            'status' => 'approved',
            'reviewed_by' => $userId,
            'reviewed_at' => now(),
        ]);

        return true;
    }

    public function reject($userId, $reason)
    {
        if (!$this->canBeApproved()) {
            return false;
        }

        $this->update([
            'status' => 'rejected',
            'reviewed_by' => $userId,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
        ]);

        return true;
    }

    public function convertToVendor()
    {
        if (!$this->isApproved() || $this->converted_to_vendor_id) {
            return null;
        }

        $vendorCode = 'VEND-' . str_pad(Vendor::count() + 1, 3, '0', STR_PAD_LEFT);

        $vendor = Vendor::create([
            'code' => $vendorCode,
            'name_en' => $this->company_name_en,
            'name_ar' => $this->company_name_ar,
            'contact_person' => $this->contact_person,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'address' => $this->address,
            'city' => $this->city,
            'region' => $this->region,
            'postal_code' => $this->postal_code,
            'tax_number' => $this->tax_number,
            'commercial_registration' => $this->commercial_registration,
            'status' => 'active',
        ]);

        $this->update(['converted_to_vendor_id' => $vendor->id]);

        return $vendor;
    }

    public function getCompanyNameAttribute()
    {
        return app()->getLocale() === 'ar' && $this->company_name_ar
            ? $this->company_name_ar
            : $this->company_name_en;
    }
}

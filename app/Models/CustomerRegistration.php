<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\BelongsToTenant;

class CustomerRegistration extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'registration_number',
        'registration_date',
        'customer_name_en',
        'customer_name_ar',
        'trade_name',
        'customer_type',
        'id_number',
        'commercial_registration',
        'tax_number',
        'vat_certificate_number',
        'customer_group_id',
        'contact_person',
        'contact_position',
        'phone',
        'mobile',
        'email',
        'website',
        'billing_address',
        'shipping_address',
        'city',
        'region',
        'postal_code',
        'country',
        'branch_id',
        'salesman_id',
        'credit_limit',
        'credit_days',
        'payment_terms',
        'notes',
        'status',
        'submitted_by',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'converted_to_customer_id',
    ];

    protected $casts = [
        'registration_date' => 'date',
        'reviewed_at' => 'datetime',
        'credit_limit' => 'decimal:2',
        'credit_days' => 'integer',
    ];

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'converted_to_customer_id');
    }

    public function customerGroup()
    {
        return $this->belongsTo(CustomerGroup::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function salesman()
    {
        return $this->belongsTo(User::class, 'salesman_id');
    }

    public function documents()
    {
        return $this->hasMany(CustomerRegistrationDocument::class);
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

    public function convertToCustomer()
    {
        if (!$this->isApproved() || $this->converted_to_customer_id) {
            return null;
        }

        $customerCode = 'CUST-' . str_pad(Customer::count() + 1, 3, '0', STR_PAD_LEFT);

        $customer = Customer::create([
            'code' => $customerCode,
            'name_en' => $this->customer_name_en,
            'name_ar' => $this->customer_name_ar,
            'group_id' => $this->customer_group_id,
            'branch_id' => $this->branch_id,
            'contact_person' => $this->contact_person,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'address' => $this->billing_address,
            'city' => $this->city,
            'region' => $this->region,
            'postal_code' => $this->postal_code,
            'tax_number' => $this->tax_number,
            'commercial_registration' => $this->commercial_registration,
            'credit_limit' => $this->credit_limit,
            'credit_days' => $this->credit_days,
            'salesman_id' => $this->salesman_id,
            'status' => 'active',
        ]);

        $this->update(['converted_to_customer_id' => $customer->id]);

        return $customer;
    }

    public function getCustomerNameAttribute()
    {
        return app()->getLocale() === 'ar' && $this->customer_name_ar
            ? $this->customer_name_ar
            : $this->customer_name_en;
    }
}

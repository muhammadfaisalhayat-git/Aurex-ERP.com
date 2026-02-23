<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class StockIssueOrder extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'document_number',
        'issue_date',
        'warehouse_id',
        'reference_type',
        'reference_id',
        'reference_number',
        'issue_type',
        'customer_id',
        'vendor_id',
        'status',
        'notes',
        'created_by',
        'posted_by',
        'posted_at'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'posted_at' => 'datetime'
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(StockIssueOrderItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function isPosted()
    {
        return $this->status === 'posted';
    }
}

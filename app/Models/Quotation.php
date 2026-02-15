<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class Quotation extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'document_number',
        'quotation_date',
        'expiry_date',
        'customer_id',
        'branch_id',
        'warehouse_id',
        'salesman_id',
        'status',
        'version',
        'parent_quotation_id',
        'subtotal',
        'discount_amount',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'terms_conditions',
        'notes',
        'created_by',
        'converted_by',
        'converted_at',
        'converted_to_id',
        'converted_to_type'
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'expiry_date' => 'date',
        'converted_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function salesman()
    {
        return $this->belongsTo(User::class, 'salesman_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }

    public static function generateNextNumber()
    {
        $lastQuotation = self::orderByRaw('CAST(SUBSTRING(document_number, 4) AS INTEGER) DESC')
            ->where('document_number', 'LIKE', 'QT-%')
            ->first();

        $nextNumber = 1;
        if ($lastQuotation) {
            $numberPart = substr($lastQuotation->document_number, 3);
            if (is_numeric($numberPart)) {
                $nextNumber = (int) $numberPart + 1;
            }
        }

        return 'QT-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}

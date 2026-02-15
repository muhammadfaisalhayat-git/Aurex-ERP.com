<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;
class SalesReturn extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;
    protected $fillable = ['company_id', 'document_number', 'return_number', 'return_date', 'sales_invoice_id', 'customer_id', 'branch_id', 'warehouse_id', 'status', 'return_reason', 'reason_description', 'subtotal', 'tax_amount', 'total_amount', 'notes', 'created_by', 'posted_by', 'posted_at'];
    protected $casts = ['return_date' => 'date', 'posted_at' => 'datetime', 'subtotal' => 'decimal:2', 'tax_amount' => 'decimal:2', 'total_amount' => 'decimal:2'];
    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function items()
    {
        return $this->hasMany(SalesReturnItem::class);
    }
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }
}

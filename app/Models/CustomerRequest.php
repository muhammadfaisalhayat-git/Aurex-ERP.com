<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class CustomerRequest extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['document_number', 'request_date', 'customer_id', 'branch_id', 'needed_date', 'status', 'notes', 'created_by', 'converted_by', 'converted_at', 'quotation_id', 'subtotal', 'tax_amount', 'total_amount'];
    protected $casts = ['request_date' => 'date', 'needed_date' => 'date', 'converted_at' => 'datetime'];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function items()
    {
        return $this->hasMany(CustomerRequestItem::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

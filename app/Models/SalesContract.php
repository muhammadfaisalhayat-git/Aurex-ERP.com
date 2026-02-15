<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class SalesContract extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['company_id', 'document_number', 'contract_number', 'contract_date', 'start_date', 'end_date', 'customer_id', 'branch_id', 'warehouse_id', 'salesman_id', 'status', 'contract_value', 'terms_conditions', 'notes', 'created_by', 'approved_by', 'approved_at'];
    protected $casts = ['contract_date' => 'date', 'start_date' => 'date', 'end_date' => 'date', 'approved_at' => 'datetime', 'contract_value' => 'decimal:2'];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function items()
    {
        return $this->hasMany(SalesContractItem::class);
    }
}

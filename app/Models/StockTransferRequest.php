<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class StockTransferRequest extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['company_id', 'document_number', 'request_date', 'from_warehouse_id', 'to_warehouse_id', 'status', 'requested_by', 'approved_by', 'approved_at', 'notes'];
    protected $casts = ['request_date' => 'date', 'approved_at' => 'datetime'];
    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }
    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }
    public function items()
    {
        return $this->hasMany(StockTransferRequestItem::class);
    }
}

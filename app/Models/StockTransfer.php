<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class StockTransfer extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;
    protected $fillable = ['company_id', 'document_number', 'transfer_date', 'from_warehouse_id', 'to_warehouse_id', 'status', 'requested_by', 'approved_by', 'approved_at', 'received_by', 'received_at', 'notes'];
    protected $casts = ['transfer_date' => 'date', 'approved_at' => 'datetime', 'received_at' => 'datetime'];
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
        return $this->hasMany(StockTransferItem::class);
    }
}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class StockReceiving extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;
    protected $table = 'stock_receiving';
    protected $fillable = ['company_id', 'document_number', 'receiving_date', 'warehouse_id', 'vendor_id', 'purchase_order_number', 'delivery_note_number', 'reference_type', 'reference_id', 'status', 'notes', 'created_by', 'received_by', 'received_at'];
    protected $casts = ['receiving_date' => 'date', 'received_at' => 'datetime'];
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function items()
    {
        return $this->hasMany(StockReceivingItem::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
